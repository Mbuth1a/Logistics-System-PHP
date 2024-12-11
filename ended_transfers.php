<?php
include 'connection.php';  // Include the database connection file

$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // Get the current page from the URL, default to 1
$offset = ($page - 1) * $limit;  // Calculate the offset

// Get the total number of records for pagination
$totalQuery = "SELECT COUNT(*) AS total FROM transfers WHERE trip_status = 'Ended'";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];  // Total number of records
$totalPages = ceil($totalRecords / $limit);  // Calculate total pages

// Fetch ended trips with pagination using LIMIT and OFFSET
$query = "SELECT 
            transfers.transfer_id, 
            transfers.customer_name,
            vehicles.vehicle_regno,  
            drivers.full_name AS driver_full_name, 
             
            transfers.destination, 
            transfers.transfer_date, 
            transfers.transfer_time, 
            transfers.transfer_day,
            transfers.start_odometer, 
            transfers.end_odometer, 
            transfers.trip_status,
            transfers.actual_distance
          FROM 
            transfers
          JOIN 
            vehicles ON transfers.vehicle = vehicles.id  -- Join on vehicle_id
          JOIN 
            drivers ON transfers.driver = drivers.id  -- Join on driver_id
          
          WHERE 
            transfers.trip_status = 'Ended'
          LIMIT $limit OFFSET $offset";  // Add LIMIT and OFFSET for pagination

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
            $updateQuery = "UPDATE transfers SET actual_distance = ? WHERE transfer_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("di", $actual_distance, $row['transfer_id']);
            $stmt->execute();
            $stmt->close();
        }

        // Display the trip details in the table
        echo "<tr>
                <td>" . htmlspecialchars($row['customer_name']) . "</td>
                <td>" . htmlspecialchars($row['vehicle_regno']) . "</td>
                <td>" . htmlspecialchars($row['driver_full_name']) . "</td>
                <td>" . htmlspecialchars($row['destination']) . "</td>
                <td>" . htmlspecialchars($row['transfer_date']) . "</td>
                <td>" . htmlspecialchars($row['transfer_time']) . "</td>
                <td>" . htmlspecialchars($row['transfer_day']) . "</td>
                
                <td>" . htmlspecialchars($row['start_odometer']) . "</td>
                <td>" . htmlspecialchars($row['end_odometer']) . "</td>
                <td>" . htmlspecialchars($actual_distance) . "</td>
                <td>" . htmlspecialchars($row['trip_status']) . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='16'>No ended trips found</td></tr>";
}

echo "</tbody>
      </table>";

// Pagination links (moved below the table)
echo '<div class="pagination">';
if ($page > 1) {
    echo '<a href="?page=' . ($page - 1) . '">&laquo; Previous</a>';
}

for ($i = 1; $i <= $totalPages; $i++) {
    if ($i == $page) {
        echo '<a class="active" href="?page=' . $i . '">' . $i . '</a>';
    } else {
        echo '<a href="?page=' . $i . '">' . $i . '</a>';
    }
}

if ($page < $totalPages) {
    echo '<a href="?page=' . ($page + 1) . '">Next &raquo;</a>';
}
echo '</div>';

// Close the database connection
$conn->close();
