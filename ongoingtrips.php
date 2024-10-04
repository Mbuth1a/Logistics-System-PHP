<?php
include 'connection.php';  // Include the database connection file

// Fetch ongoing trips with full names and vehicle registration using IDs
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
            trips.trip_status
          FROM 
            trips
          JOIN 
            vehicles ON trips.vehicle_id = vehicles.id  -- Join on vehicle_id
          JOIN 
            drivers ON trips.driver_id = drivers.id  -- Join on driver_id
          JOIN 
            co_drivers ON trips.co_driver_id = co_drivers.id  -- Join on co_driver_id
          WHERE 
            trips.trip_status = 'Ongoing'"; 

// Execute the query
$result = $conn->query($query);

// Check for errors in the query
if (!$result) {
    die("Query Failed: " . $conn->error);
}

// Check if any rows were returned
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['trip_id']) . "</td>
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
                <td>" . htmlspecialchars($row['trip_status']) . "</td>
                <td><button class='btn btn-primary' data-toggle='modal' data-target='#endTripModal' data-trip-id='" . htmlspecialchars($row['trip_id']) . "'>End Trip</button></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='14'>No ongoing trips found</td></tr>";
}

// Close the connection
$conn->close();
