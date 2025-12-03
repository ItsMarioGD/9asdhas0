<?php
require_once '../db.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, name, specialty FROM doctors");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
