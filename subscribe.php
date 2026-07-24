<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
            $stmt->execute([$email]);
            $msg = urlencode("Successfully subscribed to our newsletter!");
        } catch (PDOException $e) {
            $msg = urlencode("You are already subscribed or an error occurred.");
        }
    } else {
        $msg = urlencode("Invalid email address.");
    }
}
header("Location: " . $_SERVER['HTTP_REFERER'] . (strpos($_SERVER['HTTP_REFERER'], '?') !== false ? '&' : '?') . "newsletter_msg=$msg");
exit;
