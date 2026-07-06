<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'parent') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $age = intval($_POST["age"]);
    $parent_id = $_SESSION['user']['id'];

    $conn = new mysqli("localhost", "root", "", "kiddielearn");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO children (parent_id, first_name, last_name, age) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $parent_id, $first_name, $last_name, $age);

    if ($stmt->execute()) {
        $_SESSION['child_success'] = "Child added successfully!";
    }

    $stmt->close();
    $conn->close();

    header("Location: update-parent.php");
    exit;
}
?>
