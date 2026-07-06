<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$current_user = $_SESSION['user'];
$role = $current_user['role'];

if ($role === 'teacher') {
    // Get all parents
    $stmt = $conn->prepare("SELECT id, first_name, last_name FROM users WHERE role = 'parent' ORDER BY first_name ASC");
} elseif ($role === 'parent') {
    // Get all teachers
    $stmt = $conn->prepare("SELECT id, first_name, last_name FROM users WHERE role = 'teacher' ORDER BY first_name ASC");
} else {
    http_response_code(403);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($users);
?>
