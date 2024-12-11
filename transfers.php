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



$vehicles_sql = "
SELECT id, vehicle_regno 
FROM vehicles 
WHERE id NOT IN (
    SELECT vehicle_id FROM trips WHERE trip_status = 'ongoing'
    UNION
    SELECT vehicle FROM transfers WHERE trip_status = 'ongoing'
)";
$vehicles_result = $conn->query($vehicles_sql);




// Handle form submission/ Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $transfer_date = mysqli_real_escape_string($conn, $_POST['transfer_date']);
    $transfer_time = mysqli_real_escape_string($conn, $_POST['transfer_time']);
    $transfer_day = mysqli_real_escape_string($conn, $_POST['transfer_day']);
    $driver = mysqli_real_escape_string($conn, $_POST['driver']);
    $vehicle = mysqli_real_escape_string($conn, $_POST['vehicle']);
    $destination = mysqli_real_escape_string($conn, $_POST['destination']);
    $start_odometer = mysqli_real_escape_string($conn, $_POST['start_odometer']);

    // Set default values for fields not provided
    $end_odometer = null;
    $actual_distance = null;
    $trip_status = 'ongoing';

    // Prepare the SQL statement
    $sql = "INSERT INTO transfers (customer_name, transfer_date, transfer_time, transfer_day, driver, vehicle, destination, start_odometer, end_odometer, actual_distance, trip_status) 
            VALUES ('$customer_name', '$transfer_date', '$transfer_time', '$transfer_day', '$driver', '$vehicle', '$destination', '$start_odometer', NULL, NULL, '$trip_status')";

    // Execute the query and check if successful
    if (mysqli_query($conn, $sql)) {
        echo "Trip created successfully!";
        // Optionally, redirect to another page after success
         header("Location: load_transfer.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);
?>   
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Trip</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet"href = "css/create_trip.css">
</head>
<body>

<div class="sidebar">
        <a href="dtms_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
    <div class="main-content">
        
        <h2 class="text-center text-orange mb-4"><i class="fas fa-road"></i> Transfer Trip</h2>
            
        <div class="form-container">
            <form id="transfersTripForm" method="post" action="transfers.php">
                <input type="hidden" name="csrf_token" value="">
                <div class="row">
                
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_name"><i class="fas fa-info-circle"></i> CUSTOMER NAME</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                        </div>
                    </div>
                
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="transfer_date"><i class="fas fa-calendar-alt"></i> DATE</label>
                            <input type="date" class="form-control" id="transfer_date" name="transfer_date" required onchange="populateDay()">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="transfer_time"><i class="fas fa-clock"></i> TIME</label>
                            <input type="time" class="form-control" id="transfer_time" name="transfer_time" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="transfer_day"><i class="fas fa-sun"></i> DAY</label>
                            <input type="text" class="form-control" id="transfer_day" name="transfer_day" readonly>
                        </div>
                    </div>
                    
                </div>

                <!-- Other form fields for driver, co-driver, vehicle, etc. -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="driver"><i class="fas fa-user"></i> DRIVER</label>
                            <select class="form-control" id="driver" name="driver" required>
                                <?php
                                if ($drivers_result->num_rows > 0) {
                                    while($row = $drivers_result->fetch_assoc()) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['full_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="vehicle"><i class="fas fa-truck"></i> VEHICLE</label>
                            <select class="form-control" id="vehicle" name="vehicle" required>
                                <?php
                                if ($vehicles_result->num_rows > 0) {
                                    while($row = $vehicles_result->fetch_assoc()) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['vehicle_regno'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="destination"><i class="fas fa-map-marker-alt"></i>DESTINATION</label>
                            <input type="text" class="form-control" id="destination" name="destination" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_odometer"><i class="fas fa-tachometer-alt"></i> START ODOMETER</label>
                            <input type="text" class="form-control" id="start_odometer" name="start_odometer" required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block bg-orange mt-4"><i class="fas fa-paper-plane"></i>
                    
                </button>
            </form>
            <div id="distance" class="text-center mt-3 text-grey"></div>
        </div>
    </div>
    <script src="js/transfers.js"></script>
    
</body>
</html>
