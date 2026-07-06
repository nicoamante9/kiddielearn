<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lesson_id = (int)($_POST['lesson_id'] ?? 0);
    $lesson_name = trim($_POST['lesson_name'] ?? '');

    if ($lesson_name === '') {
        die("Lesson name cannot be empty.");
    }

    $stmt = $conn->prepare("UPDATE lessons SET lesson_name = ? WHERE id = ? AND teacher_id = ?");
    $stmt->bind_param("sii", $lesson_name, $lesson_id, $_SESSION['user']['id']);
    $stmt->execute();
    $stmt->close();

    // Redirect with success query param
    header("Location: manage-lessons.php?success=edited");
    exit;
}
?>
