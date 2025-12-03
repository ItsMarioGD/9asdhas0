<?php
require_once '../db.php';

header('Content-Type: application/json');

try {
    $sql = "
        SELECT p.*, 
               COUNT(a.id) as total_appointments,
               MAX(a.date_time) as last_visit
        FROM patients p
        LEFT JOIN appointments a ON p.id = a.patient_id
        GROUP BY p.id
        ORDER BY last_visit DESC
    ";
    
    $stmt = $pdo->query($sql);
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($patients);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
