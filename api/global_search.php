<?php
require_once '../db.php';

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$results = [];
$search = "%{$query}%";

try {
    // Search Patients
    $stmt = $pdo->prepare("SELECT id, name, phone FROM patients WHERE name LIKE ? OR phone LIKE ? LIMIT 5");
    $stmt->execute([$search, $search]);
    $patients = $stmt->fetchAll();
    
    foreach ($patients as $p) {
        $results[] = [
            'type' => 'Paciente',
            'title' => $p['name'],
            'subtitle' => $p['phone'] ?? 'Sin teléfono',
            'url' => "patient_history.php?id={$p['id']}",
            'icon' => 'fa-solid fa-user'
        ];
    }
    
    // Search Appointments
    $stmt = $pdo->prepare("
        SELECT a.id, a.date_time, a.reason, p.name as patient_name, d.name as doctor_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        WHERE p.name LIKE ? OR a.reason LIKE ?
        LIMIT 5
    ");
    $stmt->execute([$search, $search]);
    $appointments = $stmt->fetchAll();
    
    foreach ($appointments as $a) {
        $results[] = [
            'type' => 'Cita',
            'title' => $a['patient_name'],
            'subtitle' => date('d/m/Y H:i', strtotime($a['date_time'])) . " - Dr. {$a['doctor_name']}",
            'url' => "calendar.php",
            'icon' => 'fa-solid fa-calendar'
        ];
    }
    
    // Search Consultations
    $stmt = $pdo->prepare("
        SELECT c.id, c.diagnosis, p.name as patient_name
        FROM consultations c
        JOIN appointments a ON c.appointment_id = a.id
        JOIN patients p ON a.patient_id = p.id
        WHERE c.diagnosis LIKE ? OR c.medication LIKE ?
        LIMIT 5
    ");
    $stmt->execute([$search, $search]);
    $consultations = $stmt->fetchAll();
    
    foreach ($consultations as $c) {
        $results[] = [
            'type' => 'Consulta',
            'title' => $c['patient_name'],
            'subtitle' => substr($c['diagnosis'], 0, 60) . '...',
            'url' => "patient_history.php?id={$c['id']}",
            'icon' => 'fa-solid fa-notes-medical'
        ];
    }
    
    echo json_encode($results);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([]);
}
