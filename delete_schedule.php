<?php
include 'connection.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the schedule_id is set
    if (isset($_POST['schedule_id']) && !empty($_POST['schedule_id'])) {
        $schedule_id = $_POST['schedule_id'];

        // Prepare the SQL query to delete the schedule
        $sql = "DELETE FROM maintenance_schedule WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $schedule_id);

        // Execute the query and check if successful
        if ($stmt->execute()) {
            // Redirect back to the maintenance page with a success message
            header("Location: maintenance.php?success=Schedule deleted successfully.");
        } else {
            // Redirect back to the maintenance page with an error message
            header("Location: maintenance.php?error=Error deleting the schedule.");
        }
    } else {
        // Redirect back if no schedule_id was provided
        header("Location: maintenance.php?error=No schedule ID provided.");
    }
} else {
    // Redirect if the request is not a POST request
    header("Location: maintenance.php");
}
