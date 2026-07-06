<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$teacher_id = (int)$_SESSION['user']['id'];
$item_id = (int)($_GET['id'] ?? 0);
$lesson_id = (int)($_GET['lesson_id'] ?? 0);

// Verify item belongs to teacher
$stmt = $conn->prepare("
    SELECT li.image_path, li.audio_path 
    FROM lesson_items li 
    JOIN lessons l ON li.lesson_id = l.id 
    WHERE li.id = ? AND l.teacher_id = ?
");
$stmt->bind_param("ii", $item_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) die("Item not found or permission denied.");

// Delete files
if (!empty($item['image_path']) && file_exists($item['image_path'])) unlink($item['image_path']);
if (!empty($item['audio_path']) && file_exists($item['audio_path'])) unlink($item['audio_path']);

// Delete DB record
$stmt = $conn->prepare("DELETE FROM lesson_items WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$stmt->close();

header("Location: manage-lesson-items.php?lesson_id=" . $lesson_id);
exit;
