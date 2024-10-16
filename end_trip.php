<?php
include 'connection.php';  // Include the database connection file

// Check if the required data has been received
if (isset($_POST['trip_id']) && isset($_POST['end_odometer'])) {
    // Retrieve the data from the AJAX request
    $tripId = $_POST['trip_id'];
    $endOdometer = $_POST['end_odometer'];

    // Update the trip in the database to mark it as ended
    $query = "UPDATE trips 
              SET trip_status = 'Ended', 
                  end_odometer = ? 
              WHERE trip_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $endOdometer, $tripId);

    // Execute the query and check for success
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data provided']);
}
