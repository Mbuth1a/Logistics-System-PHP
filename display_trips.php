<?php
include 'connection.php';  
$query = "SELECT trip_id, description FROM trips"; 
$result = $conn->query($query);
if (!$result) {
    die("Error fetching trips: " . $conn->error);
}
while ($row = $result->fetch_assoc()) {
    echo "<option value='" . htmlspecialchars($row['trip_id']) . "'>" . htmlspecialchars($row['description']) . "</option>";
}
$conn->close();
