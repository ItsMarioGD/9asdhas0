<?php
require_once '../db.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(date_time, '%Y-%m') as month, COUNT(*) as count
        FROM appointments
        WHERE status = 'completed'
        GROUP BY month
        ORDER BY month DESC
        LIMIT 12
    ");
    $monthlyStats = $stmt->fetchAll();

    $stmt = $pdo->query("
        SELECT diagnosis, COUNT(*) as count
        FROM consultations
        WHERE diagnosis IS NOT NULL AND diagnosis != ''
        GROUP BY diagnosis
        ORDER BY count DESC
        LIMIT 5
    ");
    $topDiseases = $stmt->fetchAll();

    $stmt = $pdo->query("
        SELECT p.name, p.phone, MAX(a.date_time) as last_visit
        FROM patients p
        JOIN appointments a ON p.id = a.patient_id
        GROUP BY p.id
        HAVING last_visit < DATE_SUB(NOW(), INTERVAL 6 MONTH)
    ");
    $lostPatients = $stmt->fetchAll();

    echo json_encode([
        'monthly_stats' => $monthlyStats,
        'top_diseases' => $topDiseases,
        'lost_patients' => $lostPatients
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
