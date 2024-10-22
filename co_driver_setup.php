<?php
include 'connection.php';

// Initialize drivers array
$co_drivers = [];

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $full_name = $_POST['full_name'];
    $employee_number = $_POST['employee_number'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO co_drivers (full_name, employee_number, phone_number, email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $employee_number, $phone_number, $email);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Failed: " . $stmt->error; // Show error if insert fails
    }

    $stmt->close();
}

// Fetch registered drivers
$result = $conn->query("SELECT * FROM co_drivers");
if ($result) {
    $co_drivers = $result->fetch_all(MYSQLI_ASSOC);
}
$conn->close();