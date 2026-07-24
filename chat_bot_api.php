<?php
require_once 'includes/db.php';
header('Content-Type: application/json');

$message = $_POST['message'] ?? '';
if (!$message) {
    echo json_encode(['reply' => 'Please ask a legal question.']);
    exit;
}

$api_key = get_setting($pdo, 'gemini_api_key');

if ($api_key) {
    // Attempt Gemini API Call
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $api_key;
    
    $data = [
        'contents' => [
            ['parts' => [['text' => "You are a helpful legal assistant for a law firm. Answer this question concisely and professionally: " . $message]]]
        ]
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpcode == 200) {
        $res = json_decode($response, true);
        $reply = $res['candidates'][0]['content']['parts'][0]['text'] ?? 'I am sorry, I could not process that request.';
        echo json_encode(['reply' => $reply]);
        exit;
    }
}

// Fallback logic if API key is not set or fails (Works without AI)
$message_lower = strtolower($message);
$reply = "Thank you for reaching out. We offer free consultations for all new cases. Please click 'Free Consultation' to book an appointment with our attorneys.";

if (strpos($message_lower, 'price') !== false || strpos($message_lower, 'cost') !== false || strpos($message_lower, 'fee') !== false) {
    $reply = "Our fees vary depending on the complexity of the case. We operate on a contingency basis for personal injury, meaning you pay nothing unless we win. For other cases, we require a retainer. Please schedule a consultation to discuss specifics.";
} elseif (strpos($message_lower, 'time') !== false || strpos($message_lower, 'hours') !== false) {
    $reply = "Our office is open Monday through Friday, 9:00 AM to 5:00 PM. However, you can book an appointment online 24/7.";
} elseif (strpos($message_lower, 'where') !== false || strpos($message_lower, 'location') !== false) {
    $reply = "Our main office is located at " . get_setting($pdo, 'site_address') . ".";
}

echo json_encode(['reply' => $reply]);
