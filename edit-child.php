<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'parent') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["child_id"])) {
    $child_id = intval($_POST["child_id"]);
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $age = intval($_POST["age"]);
    $parent_id = $_SESSION['user']['id'];

    $conn = new mysqli("localhost", "root", "", "kiddielearn");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Double check if child belongs to this parent
    $check = $conn->prepare("SELECT id FROM children WHERE id = ? AND parent_id = ?");
    $check->bind_param("ii", $child_id, $parent_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 1) {
        $stmt = $conn->prepare("UPDATE children SET first_name = ?, last_name = ?, age = ? WHERE id = ?");
        $stmt->bind_param("ssii", $first_name, $last_name, $age, $child_id);
        if ($stmt->execute()) {
            $_SESSION['child_success'] = "Child updated successfully!";
        }
        $stmt->close();
    }

    $check->close();
    $conn->close();

    header("Location: update-parent.php");
    exit;
}
?>
