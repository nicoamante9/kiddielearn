<?php
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = ""; // change if you use a password
$DB_NAME = "kiddielearn";

// Create connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set character set to support emojis & multilingual text
$conn->set_charset("utf8mb4");
?>
