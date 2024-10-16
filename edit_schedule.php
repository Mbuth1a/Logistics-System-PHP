<?php
include 'connection.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $schedule_id = $_POST['schedule_id'];
    $vehicle_id = $_POST['vehicle_id'];
    $service_provider = $_POST['service_provider'];
    $maintenance_date = $_POST['maintenance_date'];
    $inspection_date = $_POST['inspection_date'];
    $insurance_date = $_POST['insurance_date'];
    $speed_governor_date = $_POST['speed_governor_date'];
    $kenha_permit_date = $_POST['kenha_permit_date'];
    $track_solid_date = $_POST['track_solid_date'];

    // Check if the schedule ID is set and not empty
    if (isset($schedule_id) && !empty($schedule_id)) {
        // First, retrieve the current track_solid_date
        $currentTrackSolidDateQuery = "SELECT track_solid_date FROM maintenance_schedule WHERE id = ?";
        $stmtCurrent = $conn->prepare($currentTrackSolidDateQuery);
        $stmtCurrent->bind_param("i", $schedule_id);
        $stmtCurrent->execute();
        $result = $stmtCurrent->get_result();

        // Fetch the current value
        $currentTrackSolidDate = null;
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $currentTrackSolidDate = $row['track_solid_date'];
        }

        // Prepare the SQL query to update the existing schedule
        $sql = "UPDATE maintenance_schedule SET 
                    vehicle_id = ?, 
                    service_provider = ?, 
                    maintenance_date = ?, 
                    inspection_date = ?, 
                    insurance_date = ?, 
                    speed_governor_date = ?, 
                    kenha_permit_date = ?, 
                    track_solid_date = ? 
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        // If the track_solid_date input is empty, use the current value
        if (empty($track_solid_date)) {
            $track_solid_date = $currentTrackSolidDate;
        }

        // Bind parameters
        $stmt->bind_param("isssssssi", $vehicle_id, $service_provider, $maintenance_date, $inspection_date, $insurance_date, $speed_governor_date, $kenha_permit_date, $track_solid_date, $schedule_id);

        // Execute the query and check if successful
        if ($stmt->execute()) {
            header("Location: maintenance.php?success=Schedule updated successfully.");
        } else {
            die("Execute failed: " . $stmt->error);
        }
    } else {
        header("Location: maintenance.php?error=No schedule ID provided.");
    }
} else {
    header("Location: maintenance.php");
}
