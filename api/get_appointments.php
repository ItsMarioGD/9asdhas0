<?php
require_once '../db.php';

header('Content-Type: application/json');

try {
    $sql = "
        SELECT a.id, a.date_time, a.appointment_type, a.reason, a.status, 
               p.name as patient_name, d.name as doctor_name 
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.status = 'scheduled'
        ORDER BY a.date_time ASC
    ";

    $stmt = $pdo->query($sql);
    $appointments = $stmt->fetchAll();

    $typeLabels = [
        'consulta' => 'Consulta Médica',
        'laboratorio' => 'Verificación de Laboratorio',
        'chequeo' => 'Chequeo Común'
    ];

    foreach ($appointments as &$appt) {
        $appt['date_time'] = date('Y-m-d H:i', strtotime($appt['date_time']));
        $appt['type_label'] = $typeLabels[$appt['appointment_type']] ?? $appt['appointment_type'];
    }

    echo json_encode($appointments);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
