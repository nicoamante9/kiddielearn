<?php
session_start();

// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "kiddielearn";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    // In production don't echo DB errors - here we redirect with a failure code
    header("Location: register.php?error=failed");
    exit;
}

// Get and sanitize inputs
// Use different variable names than DB credentials to avoid confusion
$form_username = isset($_POST['username']) ? trim($_POST['username']) : '';
$form_password = isset($_POST['password']) ? trim($_POST['password']) : '';
$confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
$role = isset($_POST['role']) ? trim($_POST['role']) : '';
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

// For parent: also get child info
$child_first_name = isset($_POST['child_first_name']) ? trim($_POST['child_first_name']) : null;
$child_last_name = isset($_POST['child_last_name']) ? trim($_POST['child_last_name']) : null;
$child_age = isset($_POST['child_age']) ? trim($_POST['child_age']) : null;

// Basic validation
if (empty($form_username) || empty($form_password) || empty($confirm_password) || empty($role) || empty($first_name) || empty($last_name) || empty($email)) {
    header("Location: register.php?error=empty");
    exit;
}

if ($role === "parent") {
    if (empty($child_first_name) || empty($child_last_name) || $child_age === null || $child_age === '') {
        header("Location: register.php?error=childempty");
        exit;
    }
    // ensure age is numeric and reasonable
    if (!is_numeric($child_age) || (int)$child_age < 1) {
        header("Location: register.php?error=childempty");
        exit;
    }
}

// Check if passwords match
if ($form_password !== $confirm_password) {
    header("Location: register.php?error=nomatch");
    exit;
}

// Check if username already exists
$sql_check = "SELECT id FROM users WHERE username = ?";
$stmt_check = $conn->prepare($sql_check);
if (!$stmt_check) {
    header("Location: register.php?error=failed");
    exit;
}
$stmt_check->bind_param("s", $form_username);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check && $result_check->num_rows > 0) {
    $stmt_check->close();
    header("Location: register.php?error=exists");
    exit;
}
$stmt_check->close();

// Hash the password securely
$hashed_password = password_hash($form_password, PASSWORD_DEFAULT);

// Insert the new user
$sql_insert = "INSERT INTO users (username, password, role, first_name, last_name, email) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql_insert);
if (!$stmt) {
    header("Location: register.php?error=failed");
    exit;
}
$stmt->bind_param("ssssss", $form_username, $hashed_password, $role, $first_name, $last_name, $email);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;
    $stmt->close();

    if ($role === "parent") {
        // Cast age to int
        $age_int = (int)$child_age;

        // Insert child linked to this parent
        $sql_child = "INSERT INTO children (parent_id, first_name, last_name, age) VALUES (?, ?, ?, ?)";
        $stmt_child = $conn->prepare($sql_child);
        if (!$stmt_child) {
            // rollback parent user if we can't prepare child statement
            $conn->query("DELETE FROM users WHERE id = " . intval($user_id));
            header("Location: register.php?error=childfail");
            exit;
        }
        // param types: i (parent_id), s (first_name), s (last_name), i (age)
        $stmt_child->bind_param("issi", $user_id, $child_first_name, $child_last_name, $age_int);

        if (!$stmt_child->execute()) {
            // If child insert fails, rollback parent
            $stmt_child->close();
            $conn->query("DELETE FROM users WHERE id = " . intval($user_id));
            header("Location: register.php?error=childfail");
            exit;
        }
        $stmt_child->close();
    }

    // Success — redirect back to register with success flag (JS will forward to login)
    header("Location: register.php?success=1");
    exit;

} else {
    // Insert failed
    $stmt->close();
    header("Location: register.php?error=failed");
    exit;
}

$conn->close();
?>
