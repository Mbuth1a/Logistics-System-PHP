<?php
// end_trip.php

// Include your database connection file
include 'connection.php'; // Ensure this is correct

// Check if the connection was successful
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the POST data
    $endOdometer = $_POST['end_odometer'];
    $tripId = $_POST['trip_id'];

    // Validate input
    if (empty($endOdometer) || empty($tripId)) {
        echo json_encode(['success' => false, 'error' => 'Odometer and trip ID are required.']);
        exit;
    }

    // Ensure that endOdometer is numeric
    if (!is_numeric($endOdometer)) {
        echo json_encode(['success' => false, 'error' => 'End odometer must be a number.']);
        exit;
    }

    // Update the trip status and end odometer in the database
    $sql = "UPDATE trips SET end_odometer = ?, status = 'Completed' WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $endOdometer, $tripId); // Assuming both are integers
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No trip found with the given ID or no changes made.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update trip: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}

// Close the database connection
$conn->close();
