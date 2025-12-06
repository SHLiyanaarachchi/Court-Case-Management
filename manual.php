<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "court_case_management";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// User details
$username = "sahan";
$password = "1234";
$full_name = "Sahan";

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare SQL
$sql = "INSERT INTO users (username, password, full_name) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $hashed_password, $full_name);

// Execute
if ($stmt->execute()) {
    echo "User 'sahan' added successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
