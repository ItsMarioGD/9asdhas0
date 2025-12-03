<?php
require_once '../db.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$doctorId = $input['doctor_id'] ?? null;
$patientName = $input['patient_name'] ?? null;
$dateTimeStr = $input['date_time'] ?? null;
$reason = $input['reason'] ?? null;
$appointmentType = $input['appointment_type'] ?? 'consulta';
$phone = $input['patient_phone'] ?? null;
$email = $input['patient_email'] ?? null;

if (!$doctorId || !$patientName || !$dateTimeStr) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing fields']);
    exit;
}

try {
    // Check if patient exists
    $stmt = $pdo->prepare("SELECT id, email FROM patients WHERE name = ?");
    $stmt->execute([$patientName]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        // New patient
        $phoneToSave = $phone ?: '555-' . substr($patientName, 0, 3); // Placeholder phone if not provided
        $stmt = $pdo->prepare("INSERT INTO patients (name, phone, email) VALUES (?, ?, ?)");
        $stmt->execute([$patientName, $phoneToSave, $email]);
        $patientId = $pdo->lastInsertId();
    } else {
        // Existing patient
        $patientId = $patient['id'];
        // Update contact info if provided
        if ($phone || $email) {
            $updates = [];
            $params = [];
            if ($phone) { $updates[] = "phone = ?"; $params[] = $phone; }
            if ($email) { $updates[] = "email = ?"; $params[] = $email; }
            $params[] = $patientId;
            
            $sql = "UPDATE patients SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        }
        // If email wasn't provided but exists in DB, fetch it
        if (!$email && !empty($patient['email'])) {
            $email = $patient['email'];
        }
    }

    // Check for existing appointment at the same time for the same doctor
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM appointments WHERE doctor_id = ? AND date_time = ?");
    $stmt->execute([$doctorId, $dateTimeStr]);
    $existingAppointment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingAppointment['count'] > 0) {
        http_response_code(409); // Conflict
        echo json_encode(['error' => 'Appointment slot already taken for this doctor.']);
        exit;
    }

    // Insert appointment
    $stmt = $pdo->prepare("INSERT INTO appointments (doctor_id, patient_id, date_time, reason, appointment_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$doctorId, $patientId, $dateTimeStr, $reason, $appointmentType]);

    // Send Email Notification
    if ($email) {
        require_once '../includes/mailer.php';
        $mailer = new Mailer();
        // Get doctor name for email
        $stmt = $pdo->prepare("SELECT name FROM doctors WHERE id = ?");
        $stmt->execute([$doctorId]);
        $doctorName = $stmt->fetchColumn();
        
        $mailer->sendAppointmentConfirmation($email, $patientName, $dateTimeStr, $doctorName);
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
