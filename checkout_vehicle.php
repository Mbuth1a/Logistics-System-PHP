<?php
// checkout_vehicle.php

// Include the database connection file
include 'connection.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the vehicle ID and garage expense from the POST request
    $vehicleId = $_POST['vehicle_id'];
    $garageExpense = $_POST['garage_expense'];
    $currentTimestamp = date('Y-m-d H:i:s'); // Get the current timestamp

    // Update the garage record with the garage expense and checked out timestamp
    $updateQuery = "UPDATE garage 
                    SET garage_expense = ?, checked_out_at = ? 
                    WHERE vehicle_id = ? AND checked_out_at IS NULL";
    
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("dsi", $garageExpense, $currentTimestamp, $vehicleId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Vehicle checked out successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to check out the vehicle. Please try again.']);
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
