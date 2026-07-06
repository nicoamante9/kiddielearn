<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kiddielearn"; // ✅ Correct spelling

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get input
$username = trim($_POST['username']);
$password = trim($_POST['password']);
$role = $_POST['role'];

// Validate
if (empty($username) || empty($password) || empty($role)) {
    echo "<script>alert('All fields are required.'); window.location.href='login.php';</script>";
    exit;
}

// Check user
$sql = "SELECT * FROM users WHERE username = ? AND role = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    // Verify hashed password
    if (password_verify($password, $user['password'])) {
        // ✅ Store all necessary user info in session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'role' => $user['role']
        ];

        // Redirect based on role
        if ($user['role'] == 'teacher') {
            header("Location: dashboard-teacher.php");
        } elseif ($user['role'] == 'parent') {
            header("Location: dashboard-parent.php");
        } else {
            echo "<script>alert('Invalid role.'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Incorrect password.'); window.location.href='login.php';</script>";
    }
} else {
    echo "<script>alert('Invalid username or role.'); window.location.href='login.php';</script>";
}

$stmt->close();
$conn->close();
?>
