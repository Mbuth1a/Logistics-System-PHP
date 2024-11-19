<?php
include 'connection.php';  // Include the database connection file

// Fetch ongoing trips with full names and vehicle registration using IDs
$query = "SELECT 
            transfers.transfer_id, 
            vehicles.vehicle_regno,  
            drivers.full_name AS driver_full_name, 
            transfers.destination, 
            transfers.customer_name, 
            transfers.transfer_date, 
            transfers.transfer_time, 
            transfers.transfer_day, 
            transfers.start_odometer, 
            transfers.trip_status
          FROM 
            transfers
          JOIN 
            vehicles ON transfers.vehicle = vehicles.id -- Join on vehicle_id
          JOIN 
            drivers ON transfers.driver = drivers.id  -- Join on driver_id
          
          WHERE 
            transfers.trip_status = 'Ongoing'"; 

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
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['customer_name']) . "</td>
                <td>" . htmlspecialchars($row['transfer_date']) . "</td>
                <td>" . htmlspecialchars($row['transfer_time']) . "</td>
                <td>" . htmlspecialchars($row['transfer_day']) . "</td>
                <td>" . htmlspecialchars($row['driver_full_name']) . "</td>
                <td>" . htmlspecialchars($row['vehicle_regno']) . "</td>
                <td>" . htmlspecialchars($row['destination']) . "</td>
                <td>" . htmlspecialchars($row['start_odometer']) . "</td>
                <td>" . htmlspecialchars($row['trip_status']) . "</td>
                <td><button class='btn btn-primary' data-toggle='modal' data-target='#endTransferModal' data-transfer-id='" . htmlspecialchars($row['id']) . "'>End Trip</button></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='11'>No ongoing Transfer trips found</td></tr>";
}

// Close the connection
$conn->close();
