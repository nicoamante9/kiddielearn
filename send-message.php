<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$sender_id = $_SESSION['user']['id'];
$receiver_id = (int)($_POST['receiver_id'] ?? 0);
$message = trim($_POST['message'] ?? '');

if ($receiver_id <= 0 || $message === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $sender_id, $receiver_id, $message);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
?>
