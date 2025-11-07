<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header(header: 'Location: index.php');
    exit();
}

// Check if case_id is provided
if (!isset($_GET['case_id']) || empty($_GET['case_id'])) {
    header(header: 'Location: dashboard.php');
    exit();
}

$case_id = $_GET['case_id'];

// Verify case exists
$stmt = $conn->prepare(query: "SELECT case_id, case_title FROM cases WHERE case_id = ?");
$stmt->bind_param(types: "s", var: $case_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header(header: 'Location: dashboard.php');
    exit();
}

$case = $result->fetch_assoc();
$stmt->close();

// Delete the case
$delete_stmt = $conn->prepare(query: "DELETE FROM cases WHERE case_id = ?");
$delete_stmt->bind_param(types: "s", var: $case_id);

if ($delete_stmt->execute()) {
    $_SESSION['delete_success'] = 'Case deleted successfully!';
} else {
    $_SESSION['delete_error'] = 'Error deleting case: ' . $conn->error;
}

$delete_stmt->close();

// Redirect to dashboard
header(header: 'Location: dashboard.php');
exit();
?>
