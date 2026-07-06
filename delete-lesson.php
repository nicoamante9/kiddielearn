<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $lesson_id = (int)$_GET['id'];

    // Optional: delete lesson items if needed
    // $stmt = $conn->prepare("DELETE FROM lesson_items WHERE lesson_id = ? AND teacher_id = ?");
    // $stmt->bind_param("ii", $lesson_id, $_SESSION['user']['id']);
    // $stmt->execute();
    // $stmt->close();

    $stmt = $conn->prepare("DELETE FROM lessons WHERE id = ? AND teacher_id = ?");
    $stmt->bind_param("ii", $lesson_id, $_SESSION['user']['id']);
    $stmt->execute();
    $stmt->close();

    // Redirect with success query param
    header("Location: manage-lessons.php?success=deleted");
    exit;
}

// If no id provided, just redirect
header("Location: manage-lessons.php");
exit;
?>
