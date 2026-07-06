<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$receiver_id = $_SESSION['user']['id'];
$sender_id = (int)($_GET['user_id'] ?? 0);

if ($sender_id > 0) {
    $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0");
    $stmt->bind_param("ii", $sender_id, $receiver_id);
    $stmt->execute();
    $stmt->close();
}
?>
