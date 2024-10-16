<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vehicle_id = $_POST['vehicle_id'];
    $issue_description = $_POST['issue_description'];

    // Check if the vehicle is already in the garage and not checked out
    $checkQuery = "SELECT * FROM garage WHERE vehicle_id = ? AND checked_out_at IS NULL";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Vehicle is already in the garage!']);
    } else {
        // Insert the vehicle into the garage table
        $insertQuery = "INSERT INTO garage (vehicle_id, issue_description, checked_in_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("is", $vehicle_id, $issue_description);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Vehicle added to garage successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add vehicle to garage.']);
        }
    }
    $stmt->close();
    $conn->close();
}
