<?php
include 'connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the query to delete the product
    $stmt = $conn->prepare("DELETE FROM inventorys WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Product deleted successfully.";
    } else {
        echo "Failed to delete product: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the inventory page
    header("Location: inventory.php");
    exit();
} else {
    echo "Invalid request.";
}
