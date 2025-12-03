from flask import Flask, render_template, request, jsonify
import os
from db import get_db_connection
import google.generativeai as genai
from dotenv import load_dotenv

load_dotenv()

app = Flask(__name__)
app.secret_key = 'supersecretkey' # Change this in production

# Configure Gemini
GENAI_API_KEY = os.getenv('GENAI_API_KEY')
if GENAI_API_KEY:
    genai.configure(api_key=GENAI_API_KEY)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/reception')
def reception():
    return render_template('reception.html')

@app.route('/consultation')
def consultation():
    return render_template('consultation.html')

@app.route('/dashboard')
def dashboard():
    return render_template('dashboard.html')

from datetime import datetime
import json

@app.route('/api/parse_appointment', methods=['POST'])
def parse_appointment():
    data = request.json
    text = data.get('text')
    
    if not text:
        return jsonify({'error': 'No text provided'}), 400

    # Get doctors for context
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    cursor.execute('SELECT id, name, specialty FROM doctors')
    doctors = cursor.fetchall()
    cursor.close()
    conn.close()
    
    doctors_context = json.dumps(doctors)

    prompt = f"""
    Extract appointment details from this text: "{text}"
    
    Available Doctors: {doctors_context}
    
    Return ONLY a JSON object with these fields:
    - patient_name (string, extract if present)
    - date_time (string, ISO 8601 format YYYY-MM-DD HH:MM:SS, assume current year {datetime.now().year} if not specified)
    - reason (string)
    - suggested_doctor_id (int, ID of the most relevant doctor based on specialty or name. If unsure, null)
    
    If the text is not about an appointment, return {{ "error": "Not an appointment request" }}
    """
    
    try:
        model = genai.GenerativeModel('gemini-pro')
        response = model.generate_content(prompt)
        # Clean up code blocks if present
        cleaned_text = response.text.replace('```json', '').replace('```', '').strip()
        parsed_data = json.loads(cleaned_text)
        
        if 'error' in parsed_data:
            return jsonify({'error': parsed_data['error']}), 400
            
        parsed_data['doctors'] = doctors
        return jsonify(parsed_data)
    except Exception as e:
        print(f"AI Error: {e}")
        return jsonify({'error': 'Failed to process with AI'}), 500

@app.route('/api/book_appointment', methods=['POST'])
def book_appointment():
    data = request.json
    doctor_id = data.get('doctor_id')
    patient_name = data.get('patient_name')
    date_time_str = data.get('date_time')
    reason = data.get('reason')
    
    if not all([doctor_id, patient_name, date_time_str]):
        return jsonify({'error': 'Missing fields'}), 400
        
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        
        # Check for conflicts
        # Note: In a real app, we'd check a time range (e.g. 30 mins), but per requirements "mismo doctor a la misma hora"
        cursor.execute(
            'SELECT COUNT(*) as count FROM appointments WHERE doctor_id = %s AND date_time = %s',
            (doctor_id, date_time_str)
        )
        conflict = cursor.fetchone()['count'] > 0
        
        if conflict:
            cursor.close()
            conn.close()
            return jsonify({'error': 'El doctor ya tiene una cita a esa hora. (Candado SQL activado)'}), 409
            
        # Create patient if not exists (simplified logic)
        cursor.execute('SELECT id FROM patients WHERE name = %s', (patient_name,))
        patient = cursor.fetchone()
        
        if not patient:
            # Dummy phone for now as we don't extract it yet
            cursor.execute('INSERT INTO patients (name, phone) VALUES (%s, %s)', (patient_name, '555-' + patient_name[:3]))
            patient_id = cursor.lastrowid
        else:
            patient_id = patient['id']
            
        # Insert appointment
        cursor.execute(
            'INSERT INTO appointments (doctor_id, patient_id, date_time, reason) VALUES (%s, %s, %s, %s)',
            (doctor_id, patient_id, date_time_str, reason)
        )
        conn.commit()
        
        cursor.close()
        conn.close()
        return jsonify({'success': True})
        
    except Exception as e:
        print(f"DB Error: {e}")
        return jsonify({'error': str(e)}), 500

@app.route('/api/get_appointments')
def get_appointments():
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    cursor.execute('''
        SELECT a.id, a.date_time, p.name as patient_name, d.name as doctor_name 
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.status = 'scheduled'
        ORDER BY a.date_time ASC
    ''')
    appointments = cursor.fetchall()
    # Convert datetime to string
    for appt in appointments:
        appt['date_time'] = appt['date_time'].strftime('%Y-%m-%d %H:%M')
    cursor.close()
    conn.close()
    return jsonify(appointments)

@app.route('/api/process_consultation', methods=['POST'])
def process_consultation():
    data = request.json
    text = data.get('text')
    appointment_id = data.get('appointment_id')
    
    if not text or not appointment_id:
        return jsonify({'error': 'Missing data'}), 400
        
    prompt = f"""
    Analyze this medical consultation text: "{text}"
    
    Classify the information into these categories. Return ONLY a JSON object:
    - symptoms (string)
    - diagnosis (string)
    - medication (string)
    - notes (string, any other relevant info)
    """
    
    try:
        model = genai.GenerativeModel('gemini-pro')
        response = model.generate_content(prompt)
        cleaned_text = response.text.replace('```json', '').replace('```', '').strip()
        parsed_data = json.loads(cleaned_text)
        
        conn = get_db_connection()
        cursor = conn.cursor()
        
        cursor.execute(
            'INSERT INTO consultations (appointment_id, symptoms, diagnosis, medication, notes) VALUES (%s, %s, %s, %s, %s)',
            (appointment_id, parsed_data.get('symptoms'), parsed_data.get('diagnosis'), parsed_data.get('medication'), parsed_data.get('notes'))
        )
        consultation_id = cursor.lastrowid
        
        # Update appointment status
        cursor.execute('UPDATE appointments SET status = "completed" WHERE id = %s', (appointment_id,))
        
        conn.commit()
        cursor.close()
        conn.close()
        
        parsed_data['consultation_id'] = consultation_id
        return jsonify(parsed_data)
        
    except Exception as e:
        print(f"Error: {e}")
        return jsonify({'error': str(e)}), 500

from fpdf import FPDF
from flask import make_response

@app.route('/api/generate_prescription/<int:consultation_id>')
def generate_prescription(consultation_id):
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    
    # Get consultation details
    cursor.execute('''
        SELECT c.*, p.name as patient_name, d.name as doctor_name, d.specialty
        FROM consultations c
        JOIN appointments a ON c.appointment_id = a.id
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        WHERE c.id = %s
    ''', (consultation_id,))
    data = cursor.fetchone()
    cursor.close()
    conn.close()
    
    if not data:
        return "Consultation not found", 404
        
    # Generate Easy Instructions with AI
    prompt = f"""
    Translate this medical diagnosis and medication into simple, friendly instructions for a patient:
    Diagnosis: {data['diagnosis']}
    Medication: {data['medication']}
    
    Write it as a list of bullet points. Be kind and clear.
    """
    try:
        model = genai.GenerativeModel('gemini-pro')
        response = model.generate_content(prompt)
        easy_instructions = response.text
    except:
        easy_instructions = "Siga las instrucciones del médico."

    # Create PDF
    pdf = FPDF()
    pdf.add_page()
    
    # Header
    pdf.set_font("Arial", 'B', 20)
    pdf.cell(0, 10, "Medi-Agenda AI - Receta Médica", 0, 1, 'C')
    pdf.ln(10)
    
    # Doctor Info
    pdf.set_font("Arial", 'B', 12)
    pdf.cell(0, 10, f"Dr. {data['doctor_name']} ({data['specialty']})", 0, 1)
    pdf.set_font("Arial", '', 12)
    pdf.cell(0, 10, f"Fecha: {data['created_at']}", 0, 1)
    pdf.ln(5)
    
    # Patient Info
    pdf.set_font("Arial", 'B', 12)
    pdf.cell(0, 10, f"Paciente: {data['patient_name']}", 0, 1)
    pdf.ln(5)
    
    # Medical Details
    pdf.set_font("Arial", 'B', 14)
    pdf.cell(0, 10, "Diagnóstico:", 0, 1)
    pdf.set_font("Arial", '', 12)
    pdf.multi_cell(0, 10, data['diagnosis'])
    pdf.ln(5)
    
    pdf.set_font("Arial", 'B', 14)
    pdf.cell(0, 10, "Medicamentos:", 0, 1)
    pdf.set_font("Arial", '', 12)
    pdf.multi_cell(0, 10, data['medication'])
    pdf.ln(5)
    
    # Easy Instructions
    pdf.set_font("Arial", 'B', 14)
    pdf.set_text_color(0, 100, 0) # Green color
    pdf.cell(0, 10, "Instrucciones Fáciles (Para el Paciente):", 0, 1)
    pdf.set_font("Arial", '', 12)
    pdf.set_text_color(0, 0, 0)
    pdf.multi_cell(0, 10, easy_instructions)
    
    # Output
    response = make_response(pdf.output(dest='S').encode('latin-1', 'replace'))
    response.headers['Content-Type'] = 'application/pdf'
    response.headers['Content-Disposition'] = f'attachment; filename=receta_{consultation_id}.pdf'
    
    return response

@app.route('/api/dashboard_data')
def dashboard_data():
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    
    # 1. Pacientes por mes (GROUP BY, Date functions)
    cursor.execute('''
        SELECT DATE_FORMAT(date_time, '%Y-%m') as month, COUNT(*) as count
        FROM appointments
        WHERE status = 'completed'
        GROUP BY month
        ORDER BY month DESC
        LIMIT 12
    ''')
    monthly_stats = cursor.fetchall()
    
    # 2. Top 5 enfermedades (GROUP BY, ORDER BY)
    cursor.execute('''
        SELECT diagnosis, COUNT(*) as count
        FROM consultations
        WHERE diagnosis IS NOT NULL
        GROUP BY diagnosis
        ORDER BY count DESC
        LIMIT 5
    ''')
    top_diseases = cursor.fetchall()
    
    # 3. Pacientes perdidos (LEFT JOIN, Date check > 6 months)
    # Logic: Patients who had an appointment > 6 months ago AND NO appointment in the last 6 months
    cursor.execute('''
        SELECT p.name, p.phone, MAX(a.date_time) as last_visit
        FROM patients p
        JOIN appointments a ON p.id = a.patient_id
        GROUP BY p.id
        HAVING last_visit < DATE_SUB(NOW(), INTERVAL 6 MONTH)
    ''')
    lost_patients = cursor.fetchall()
    
    cursor.close()
    conn.close()
    
    return jsonify({
        'monthly_stats': monthly_stats,
        'top_diseases': top_diseases,
        'lost_patients': lost_patients
    })

if __name__ == '__main__':
    app.run(debug=True)
