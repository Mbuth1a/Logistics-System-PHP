<?php
include 'connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the co-driver from the database
    $stmt = $conn->prepare("DELETE FROM co_drivers WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Delete successful";
        header("Location: add_co_drivers.php"); // Redirect back after deletion
    } else {
        echo "Delete failed: " . $stmt->error;
    }

    $stmt->close();
}

