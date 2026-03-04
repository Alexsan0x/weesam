<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST method required.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($input['message'] ?? '');

if (empty($userMessage)) {
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty.']);
    exit;
}

$systemPrompt = "You are Abu Mahmoud, a friendly and knowledgeable virtual tourism guide for Jordan. "
    . "You help tourists discover places in Jordan including Petra, Wadi Rum, the Dead Sea, Jerash, Aqaba, "
    . "Amman, Ajloun Castle, Madaba, Dana Nature Reserve, Mount Nebo, Karak Castle, and more. "
    . "You provide practical travel tips, historical context, cultural insights, restaurant suggestions, "
    . "and safety advice. You respond in the same language the user writes in — if they write in Arabic, "
    . "respond in Arabic. If they write in English, respond in English. Be warm, helpful, and enthusiastic "
    . "about Jordan's heritage. Keep responses concise but informative (2-4 paragraphs max). "
    . "If asked about things unrelated to Jordan or tourism, politely steer the conversation back to "
    . "helping with their Jordan visit.";

$apiKey = $gemini_api_key;

$models = [
    'gemini-2.5-flash',
    'gemini-2.0-flash'
];

$requestBody = [
    'system_instruction' => [
        'parts' => [
            ['text' => $systemPrompt]
        ]
    ],
    'contents' => [
        [
            'parts' => [
                ['text' => $userMessage]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.8,
        'maxOutputTokens' => 800
    ]
];

$aiResponse = null;

foreach ($models as $model) {
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey;

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($requestBody),
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError || $httpCode !== 200) {
        continue;
    }

    $data = json_decode($response, true);
    $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

    if ($aiResponse) {
        break;
    }
}

if ($aiResponse) {
    echo json_encode(['success' => true, 'response' => $aiResponse]);
} else {
    echo json_encode(['success' => false, 'message' => 'Could not generate a response. Please try again.']);
}
