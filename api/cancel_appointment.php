<?php
require_once '../db.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$appointmentId = $input['id'] ?? null;

if (!$appointmentId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
    $stmt->execute([$appointmentId]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
