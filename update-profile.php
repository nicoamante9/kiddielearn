<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "kiddielearn");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = intval($_POST['user_id']);
$first = $conn->real_escape_string($_POST['first_name']);
$last = $conn->real_escape_string($_POST['last_name']);
$email = $conn->real_escape_string($_POST['email']);

$sql = "UPDATE users SET first_name='$first', last_name='$last', email='$email' WHERE id=$user_id";

if ($conn->query($sql) === TRUE) {
    $_SESSION['user']['first_name'] = $first;
    $_SESSION['user']['last_name'] = $last;
    $_SESSION['user']['email'] = $email;

    header("Location: dashboard-teacher.php?updated=1");
    exit;
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>
