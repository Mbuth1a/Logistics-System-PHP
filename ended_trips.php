<?php
include 'connection.php';  // Include the database connection file

// Fetch ended trips with full names and vehicle registration using IDs
$query = "SELECT 
            trips.trip_id, 
            vehicles.vehicle_regno,  
            drivers.full_name AS driver_full_name, 
            co_drivers.full_name AS co_driver_full_name, 
            trips.from_location, 
            trips.stops, 
            trips.to_location, 
            trips.trip_date, 
            trips.trip_time, 
            trips.trip_day, 
            trips.trip_description, 
            trips.est_distance, 
            trips.start_odometer, 
            trips.end_odometer, 
            trips.trip_status,
            
            trips.actual_distance
          FROM 
            trips
          JOIN 
            vehicles ON trips.vehicle_id = vehicles.id  -- Join on vehicle_id
          JOIN 
            drivers ON trips.driver_id = drivers.id  -- Join on driver_id
          JOIN 
            co_drivers ON trips.co_driver_id = co_drivers.id  -- Join on co_driver_id
          WHERE 
            trips.trip_status = 'Ended'";  // Filter only ended trips

// Execute the query
$result = $conn->query($query);

// Check for errors in the query
if (!$result) {
    die("Query Failed: " . $conn->error);
}

// Check if any rows were returned
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Calculate the actual distance
        $actual_distance = $row['end_odometer'] - $row['start_odometer'];

        // Update the actual distance in the database if it's not already set
        if ($row['actual_distance'] === null) {
            $updateQuery = "UPDATE trips SET actual_distance = ? WHERE trip_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("di", $actual_distance, $row['trip_id']);
            $stmt->execute();
            $stmt->close();
        }

        // Display the trip details in the table
        echo "<tr>
                <td>" . htmlspecialchars($row['vehicle_regno']) . "</td>
                <td>" . htmlspecialchars($row['driver_full_name']) . "</td>
                <td>" . htmlspecialchars($row['co_driver_full_name']) . "</td>
                <td>" . htmlspecialchars($row['from_location']) . "</td>
                <td>" . htmlspecialchars($row['stops']) . "</td>
                <td>" . htmlspecialchars($row['to_location']) . "</td>
                <td>" . htmlspecialchars($row['trip_date']) . "</td>
                <td>" . htmlspecialchars($row['trip_time']) . "</td>
                <td>" . htmlspecialchars($row['trip_day']) . "</td>
                <td>" . htmlspecialchars($row['trip_description']) . "</td>
                <td>" . htmlspecialchars($row['est_distance']) . "</td>
                <td>" . htmlspecialchars($row['start_odometer']) . "</td>
                <td>" . htmlspecialchars($row['end_odometer']) . "</td>
                <td>" . htmlspecialchars($actual_distance) . "</td>
                <td>" . htmlspecialchars($row['trip_status']) . "</td>
                
              </tr>";
    }
} else {
    echo "<tr><td colspan='16'>No ended trips found</td></tr>";
}

// Close the database connection
$conn->close();
