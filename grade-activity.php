<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activity_id = (int)$_POST['activity_id'];
    $grade = $_POST['grade'];
    $comment = trim($_POST['comment']);
    $graded_by = (int)$_SESSION['user']['id'];

    $conn = new mysqli("localhost", "root", "", "kiddielearn");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert into graded_activities
    $stmt = $conn->prepare("
        INSERT INTO graded_activities (activity_id, grade, comment, graded_by)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("issi", $activity_id, $grade, $comment, $graded_by);

    if ($stmt->execute()) {
        // Update the activity status to 'Graded'
        $update_stmt = $conn->prepare("UPDATE activities SET status='Graded' WHERE id=?");
        $update_stmt->bind_param("i", $activity_id);
        $update_stmt->execute();
        $update_stmt->close();

        // Set session to trigger SweetAlert
        $_SESSION['grade_success'] = true;
        $_SESSION['graded_activity_id'] = $activity_id;

        // Redirect back to student-activities page
        header("Location: student-activities.php");
        exit;
    } else {
        echo "<script>alert('Database error.'); window.location.href='student-activities.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
