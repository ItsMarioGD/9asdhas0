<?php
// Start output buffering to catch any accidental output
ob_start();

require_once '../db.php';
require_once 'gemini.php';

// Clean the output buffer and set proper headers
ob_end_clean();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$text = $input['text'] ?? '';

if (!$text) {
    http_response_code(400);
    echo json_encode(['error' => 'No text provided']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT id, name, specialty FROM doctors");
    $doctors = $stmt->fetchAll();
    
    $prompt = "
    Eres un asistente médico. Extrae los detalles de la cita de este texto: \"$text\"
    
    La fecha actual es: " . date('Y-m-d H:i') . "
    
    Devuelve SOLO un objeto JSON con:
    - patient_name (string)
    - patient_phone (string, null si no se encuentra)
    - date_time (YYYY-MM-DD HH:MM format)
    - reason (string)
    - doctor_name (string, null si no se especifica)
    - appointment_type (enum: 'consulta', 'laboratorio', 'chequeo')
    ";

    $response = callGemini($prompt);
    
    // Clean response
    $cleanedText = trim($response);
    $cleanedText = preg_replace('/```json\s*|\s*```/', '', $cleanedText);
    
    // Extract JSON object
    if (preg_match('/{[^{}]*(?:{[^{}]*}[^{}]*)*}/', $cleanedText, $matches)) {
        $cleanedText = $matches[0];
    }
    
    $cleanedText = preg_replace('/\s+/', ' ', $cleanedText);
    $parsedData = json_decode($cleanedText, true);

    if (json_last_error() !== JSON_ERROR_NONE || !$parsedData) {
        $parsedData = [
            'patient_name' => '',
            'patient_phone' => '',
            'date_time' => date('Y-m-d H:i:00', strtotime('+1 hour')),
            'reason' => '',
            'appointment_type' => 'consulta',
            'error' => 'No se pudo procesar automáticamente. Respuesta IA: ' . substr($response, 0, 300)
        ];
        echo json_encode($parsedData);
        exit;
    }

    // Auto-book logic
    if (count($doctors) === 1) {
        $parsedData['suggested_doctor_id'] = $doctors[0]['id'];
        $parsedData['auto_booked'] = true;
    } else {
        $parsedData['suggested_doctor_id'] = $doctors[0]['id'] ?? null;
    }

    $parsedData['doctors'] = $doctors;
    echo json_encode($parsedData);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
