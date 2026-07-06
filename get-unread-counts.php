<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$user_id = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];

// Determine counterpart role
$other_role = $role === 'teacher' ? 'parent' : 'teacher';

$stmt = $conn->prepare("
    SELECT sender_id, COUNT(*) as unread_count
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.receiver_id = ? AND m.is_read = 0 AND u.role = ?
    GROUP BY sender_id
");
$stmt->bind_param("is", $user_id, $other_role);
$stmt->execute();
$result = $stmt->get_result();

$counts = [];
while ($row = $result->fetch_assoc()) {
    $counts[$row['sender_id']] = (int)$row['unread_count'];
}
$stmt->close();

echo json_encode($counts);
?>
