<?php
include 'connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$drivers_sql = "
SELECT d.id, d.full_name 
FROM drivers d
LEFT JOIN trips t ON d.id = t.driver_id AND t.trip_status = 'ongoing'
LEFT JOIN transfers tr ON d.id = tr.driver AND tr.trip_status = 'ongoing'
WHERE t.driver_id IS NULL AND tr.driver IS NULL";
$drivers_result = $conn->query($drivers_sql);


// Fetch co-drivers who are not involved in ongoing trips
$co_drivers_sql = "SELECT id, full_name FROM co_drivers WHERE id NOT IN (SELECT co_driver_id FROM trips WHERE trip_status = 'ongoing')";
$co_drivers_result = $conn->query($co_drivers_sql);

// Fetch vehicles that are not involved in ongoing trips
$vehicles_sql = "
SELECT id, vehicle_regno 
FROM vehicles 
WHERE id NOT IN (
    SELECT vehicle_id FROM trips WHERE trip_status = 'ongoing'
    UNION
    SELECT vehicle FROM transfers WHERE trip_status = 'ongoing'
    UNION
    SELECT vehicle_id FROM garage WHERE checked_out_at IS NULL
)";
$vehicles_result = $conn->query($vehicles_sql);
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $trip_date = $_POST['date'];
    $trip_day = $_POST['day'];
    $trip_time = $_POST['time'];
    $trip_description = $_POST['description'];
    $driver_id = $_POST['driver'];
    // Check if 'co_driver' is set before accessing it
    $co_driver_id = isset($_POST['co_driver']) ? $_POST['co_driver'] : null; 
    $vehicle_id = $_POST['vehicle'];
    $from_location = $_POST['from_location'];
    $stops = $_POST['stops'];
    $to_location = $_POST['to_location'];
    $est_distance = $_POST['est_distance'];
    $start_odometer = $_POST['start_odometer'];

    $sql = "INSERT INTO trips (trip_date, trip_day, trip_time, trip_description, driver_id, co_driver_id, vehicle_id, from_location, stops, to_location, est_distance, start_odometer, trip_status)
            VALUES ('$trip_date', '$trip_day', '$trip_time', '$trip_description', '$driver_id', '$co_driver_id', '$vehicle_id', '$from_location', '$stops', '$to_location', '$est_distance', '$start_odometer', 'ongoing')";

    if ($conn->query($sql) === TRUE) {
        echo "Trip created successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();