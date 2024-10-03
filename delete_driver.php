<?php
include 'connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare the DELETE query
    $stmt = $conn->prepare("DELETE FROM drivers WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Driver deleted successfully.";
    } else {
        echo "Failed to delete driver: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the manage drivers page
    header("Location: add_driver.php");
    exit();
} else {
    echo "Invalid request.";
}
