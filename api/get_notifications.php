<?php
require_once '../db.php';

header('Content-Type: application/json');

try {
    // Get upcoming appointments (next 2 hours)
    $stmt = $pdo->query("
        SELECT a.*, p.name as patient_name, d.name as doctor_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.status = 'scheduled'
        AND a.date_time BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 HOUR)
        ORDER BY a.date_time ASC
    ");
    $upcoming = $stmt->fetchAll();
    
    $notifications = [];
    
    foreach ($upcoming as $apt) {
        $time = strtotime($apt['date_time']);
        $diff = ($time - time()) / 60; // minutes
        
        if ($diff <= 30 && $diff > 0) {
            $notifications[] = [
                'id' => $apt['id'],
                'title' => 'Cita Próxima',
                'message' => "{$apt['patient_name']} con Dr. {$apt['doctor_name']} en " . round($diff) . " minutos",
                'time' => date('H:i', $time),
                'icon' => 'fa-solid fa-clock',
                'read' => false
            ];
        }
    }
    
    // Get recent bookings (last hour)
    $stmt = $pdo->query("
        SELECT a.*, p.name as patient_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        WHERE a.created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ORDER BY a.created_at DESC
        LIMIT 5
    ");
    $recent = $stmt->fetchAll();
    
    foreach ($recent as $apt) {
        $notifications[] = [
            'id' => 'new_' . $apt['id'],
            'title' => 'Nueva Cita Agendada',
            'message' => "{$apt['patient_name']} - " . date('d/m H:i', strtotime($apt['date_time'])),
            'time' => date('H:i', strtotime($apt['created_at'])),
            'icon' => 'fa-solid fa-calendar-plus',
            'read' => true
        ];
    }
    
    echo json_encode($notifications);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([]);
}
