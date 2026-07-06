<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$teacher_id = (int)($_POST['teacher_id'] ?? 0);
$lesson_name = trim($_POST['lesson_name'] ?? '');

if ($lesson_name === '') {
    die("Lesson name is required.");
}

$stmt = $conn->prepare("INSERT INTO lessons (teacher_id, lesson_name, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("is", $teacher_id, $lesson_name);

if ($stmt->execute()) {
    $stmt->close();
    // Redirect with success query param
    header("Location: manage-lessons.php?success=added");
    exit;
} else {
    die("Database error: " . $conn->error);
}
?>
