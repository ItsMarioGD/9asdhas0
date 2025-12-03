<?php
require_once '../db.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$doctorId = $input['doctor_id'] ?? null;
$startTime = $input['start_time'] ?? null;
$endTime = $input['end_time'] ?? null;
$reason = $input['reason'] ?? 'No disponible';

if (!$doctorId || !$startTime || !$endTime) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos']);
    exit;
}

try {
    // Verificar que no se solape con citas existentes
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count FROM appointments 
        WHERE doctor_id = ? 
        AND status = 'scheduled'
        AND date_time >= ? 
        AND date_time < ?
    ");
    $stmt->execute([$doctorId, $startTime, $endTime]);
    
    if ($stmt->fetch()['count'] > 0) {
        http_response_code(409);
        echo json_encode(['error' => 'Ya existen citas agendadas en ese horario. Cancélalas primero.']);
        exit;
    }

    // Insertar bloqueo
    $stmt = $pdo->prepare("INSERT INTO blocked_slots (doctor_id, start_time, end_time, reason) VALUES (?, ?, ?, ?)");
    $stmt->execute([$doctorId, $startTime, $endTime, $reason]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
