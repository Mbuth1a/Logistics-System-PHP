<?php
// fetch_vehicle_list.php
require 'connection.php';

header('Content-Type: application/json');

$query = "SELECT id, vehicle_regno FROM vehicles";
$result = $connection->query($query);

$vehicles = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
}

echo json_encode(['vehicles' => $vehicles]);
