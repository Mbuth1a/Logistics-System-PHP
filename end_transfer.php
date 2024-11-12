<?php
include 'connection.php';  // Include the database connection file

if (isset($_POST['id']) && isset($_POST['end_odometer'])) {
    $Id = $_POST['id'];
    $endOdometer = $_POST['end_odometer'];

    // Step 1: Retrieve start_odometer for the specified trip
    $query = "SELECT start_odometer FROM transfers WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Preparation failed: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param('i', $Id);
    $stmt->execute();
    $stmt->bind_result($startOdometer);
    $stmt->fetch();
    $stmt->close();

    // Step 2: Calculate actual distance
    $actualDistance = $endOdometer - $startOdometer;

    // Step 3: Update trip status, end_odometer, and actual_distance
    $updateQuery = "UPDATE transfers 
                    SET trip_status = 'Ended', 
                        end_odometer = ?, 
                        actual_distance = ? 
                    WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    if (!$updateStmt) {
        echo json_encode(['status' => 'error', 'message' => 'Preparation failed: ' . $conn->error]);
        exit();
    }
    $updateStmt->bind_param('iii', $endOdometer, $actualDistance, $Id);

    // Execute the update query and check for success
    if ($updateStmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }

    // Close the statement and connection
    $updateStmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data provided']);
}
