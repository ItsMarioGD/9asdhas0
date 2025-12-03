CREATE DATABASE IF NOT EXISTS medi_agenda_db;
USE medi_agenda_db;
CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialty VARCHAR(100) NOT NULL
);
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL
);
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT,
    patient_id INT,
    date_time DATETIME NOT NULL,
    reason TEXT,
    appointment_type ENUM('consulta', 'laboratorio', 'chequeo') DEFAULT 'consulta',
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    FOREIGN KEY (doctor_id) REFERENCES doctors(id),
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    UNIQUE KEY unique_appointment (doctor_id, date_time)
);
CREATE TABLE IF NOT EXISTS consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT,
    symptoms TEXT,
    diagnosis TEXT,
    medication TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id)
);
INSERT INTO doctors (name, specialty)
SELECT *
FROM (
        SELECT 'Dr. House',
            'Diagnosta'
    ) AS tmp
WHERE NOT EXISTS (
        SELECT name
        FROM doctors
        WHERE name = 'Dr. House'
    )
LIMIT 1;
INSERT INTO doctors (name, specialty)
SELECT *
FROM (
        SELECT 'Dra. Grey',
            'Cirujana'
    ) AS tmp
WHERE NOT EXISTS (
        SELECT name
        FROM doctors
        WHERE name = 'Dra. Grey'
    )
LIMIT 1;