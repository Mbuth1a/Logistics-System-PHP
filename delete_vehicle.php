<?php
include 'connection.php';

// Check if ID is set
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header('Location: add_vehicle.php'); // Redirect back to the main page
        exit();
    } else {
        echo "Failed to delete: " . $stmt->error; // Show error if delete fails
    }

    $stmt->close();
} else {
    echo "No ID provided for deletion.";
}
