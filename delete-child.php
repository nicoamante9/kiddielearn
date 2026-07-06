<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'parent') {
    header("Location: login.php");
    exit;
}

if (isset($_GET["child_id"])) {
    $child_id = intval($_GET["child_id"]);
    $parent_id = $_SESSION['user']['id'];

    $conn = new mysqli("localhost", "root", "", "kiddielearn");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Ensure the child belongs to the logged-in parent
    $check = $conn->prepare("SELECT id FROM children WHERE id = ? AND parent_id = ?");
    $check->bind_param("ii", $child_id, $parent_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 1) {
        $delete = $conn->prepare("DELETE FROM children WHERE id = ?");
        $delete->bind_param("i", $child_id);
        if ($delete->execute()) {
            $_SESSION['child_success'] = "Child record deleted successfully!";
        }
        $delete->close();
    }

    $check->close();
    $conn->close();

    header("Location: update-parent.php");
    exit;
}
?>
