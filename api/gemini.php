<?php
/**
 * Get Groq API Key from environment or db.php
 */
function getGroqKey() {
    // First try from $_ENV
    if (isset($_ENV['GROQ_API_KEY']) && !empty($_ENV['GROQ_API_KEY'])) {
        return $_ENV['GROQ_API_KEY'];
    }
    
    // Try getApiKey() function if it exists (from db.php)
    if (function_exists('getApiKey')) {
        return getApiKey();
    }
    
    return null;
}

function callGroq($prompt) {
    $apiKey = getGroqKey();
    if (!$apiKey) {
        throw new Exception("Groq API Key not found");
    }

    $url = "https://api.groq.com/openai/v1/chat/completions";

    $data = [
        "model" => "llama-3.3-70b-versatile",
        "messages" => [
            [
                "role" => "user",
                "content" => $prompt
            ]
        ],
        "temperature" => 0.7,
        "max_tokens" => 1024
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception('Curl error: ' . $error);
    }
    
    curl_close($ch);

    $json = json_decode($response, true);
    
    if (isset($json['error'])) {
        throw new Exception('Groq API Error: ' . $json['error']['message']);
    }

    if (isset($json['choices'][0]['message']['content'])) {
        return $json['choices'][0]['message']['content'];
    }

    throw new Exception('Unexpected API response: ' . $response);
}

// Backward compatibility - alias for existing code
function callGemini($prompt) {
    return callGroq($prompt);
}
