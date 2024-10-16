<?php
include 'connection.php';
session_start();

// Check CSRF token
if (!isset($_POST['csrfToken']) || $_POST['csrfToken'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
    exit;
}

// Get the data from the AJAX request
$trip_id = $_POST['tripId'];
$fuel_consumed = $_POST['fuelConsumed'];
$created_at = date('Y-m-d H:i:s');

// Validate data
if (empty($trip_id) || empty($fuel_consumed)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Prepare the SQL statement to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO fuel (trip_id, fuel_consumed, created_at) VALUES (?, ?, ?)");
$stmt->bind_param("ids", $trip_id, $fuel_consumed, $created_at);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Fuel record saved successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save fuel record']);
}

$stmt->close();
$conn->close();
