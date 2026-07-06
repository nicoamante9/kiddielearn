<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$user_id = $_SESSION['user']['id'];
$other_id = (int)($_GET['user_id'] ?? 0);

$stmt = $conn->prepare("
    SELECT m.*, u.first_name, u.last_name 
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
    ORDER BY sent_at ASC
");
$stmt->bind_param("iiii", $user_id, $other_id, $other_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($messages);
?>
