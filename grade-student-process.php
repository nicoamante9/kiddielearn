<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$teacher_id = $_SESSION['user']['id'];

// DB connect
$conn = new mysqli("localhost", "root", "", "kiddielearn");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Get form data
$child_id = $_POST['child_id'];
$topic = $_POST['topic'];
$grade = $_POST['grade'];
$comment = $_POST['comment'] ?? null;

// Basic validation
if (empty($child_id) || empty($topic) || empty($grade)) {
    echo "<script>alert('All fields are required.'); window.location.href='teacher-grade-student.php';</script>";
    exit;
}

// Insert letter grade
$sql = "INSERT INTO progress (child_id, topic, grade, graded_by, comment) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issis", $child_id, $topic, $grade, $teacher_id, $comment);

if ($stmt->execute()) {
    header("Location: teacher-grade-student.php?success=1");
    exit;
} else {
    echo "<script>alert('Something went wrong.'); window.location.href='teacher-grade-student.php';</script>";
}

$stmt->close();
$conn->close();
?>
