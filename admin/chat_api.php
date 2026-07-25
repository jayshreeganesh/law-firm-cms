<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    if ($msg) {
        $stmt = $pdo->prepare("INSERT INTO admin_chat (sender_id, message) VALUES (?, ?)");
        $stmt->execute([$_SESSION['admin_id'], $msg]);
    }
    echo json_encode(['status' => 'ok']);
    exit;
}

$stmt = $pdo->query(db_limit_offset_sql($db_driver, "SELECT c.*, u.username, u.role, DATE_FORMAT(c.created_at, '%h:%i %p') as time FROM admin_chat c JOIN users u ON c.sender_id = u.id ORDER BY c.created_at ASC", 50));
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($messages);
