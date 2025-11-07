<?php
// Database configuration
define(constant_name: 'DB_HOST', value: 'localhost');
define(constant_name: 'DB_USER', value: 'root');
define(constant_name: 'DB_PASS', value: '1234');
define(constant_name: 'DB_NAME', value: 'court_case_management');

// Create connection
$conn = new mysqli(hostname: DB_HOST, username: DB_USER, password: DB_PASS, database: DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset(charset: "utf8");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
