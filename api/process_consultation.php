<?php
require_once '../db.php';
require_once 'gemini.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$text = $input['text'] ?? '';
$appointmentId = $input['appointment_id'] ?? null;

if (!$text || !$appointmentId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing data']);
    exit;
}

try {
    $prompt = "
    Actúa como un médico experto y políglota. Analiza este texto de consulta médica: \"$text\"
    
    Tu tarea es extraer la información clínica y traducirla a 3 idiomas:
    1. Español Latino (Idioma principal)
    2. Inglés (Términos médicos estándar)
    3. Kakchiquel (Idioma maya de Guatemala - usa términos aproximados o descriptivos si no existe traducción exacta)

    Devuelve SOLO un objeto JSON con esta estructura exacta:
    {
      \"symptoms\": \"lista de síntomas en español\",
      \"diagnosis\": \"diagnóstico en español\",
      \"medication\": \"medicamentos y dosis en español\",
      \"notes\": \"notas adicionales en español\",
      \"translations\": {
        \"english\": {
          \"diagnosis\": \"diagnosis in English\",
          \"medication\": \"medication in English\",
          \"instructions\": \"brief instructions in English\"
        },
        \"kakchiquel\": {
          \"diagnosis\": \"diagnóstico en Kakchiquel (o descripción simple)\",
          \"medication\": \"medicamentos (nombres se mantienen) y dosis explicada\",
          \"instructions\": \"instrucciones breves en Kakchiquel\"
        }
      }
    }
    ";

    $response = callGemini($prompt);
    $cleanedText = str_replace(['```json', '```'], '', $response);
    $parsedData = json_decode($cleanedText, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Failed to parse AI response");
    }

    $stmt = $pdo->prepare("
        INSERT INTO consultations (appointment_id, symptoms, diagnosis, medication, notes) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $appointmentId,
        $parsedData['symptoms'] ?? '',
        $parsedData['diagnosis'] ?? '',
        $parsedData['medication'] ?? '',
        $parsedData['notes'] ?? ''
    ]);
    
    $consultationId = $pdo->lastInsertId();

    $stmt = $pdo->prepare("UPDATE appointments SET status = 'completed' WHERE id = ?");
    $stmt->execute([$appointmentId]);

    $parsedData['consultation_id'] = $consultationId;
    echo json_encode($parsedData);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
