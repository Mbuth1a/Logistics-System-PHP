<?php 
include 'create_setup.php'    ;
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
        
        <h2 class="text-center text-orange mb-4"><i class="fas fa-road"></i> Create Trip</h2>
            
        <div class="form-container">
            <form id="createTripForm" method="post" action="create_trip.php">
                <input type="hidden" name="csrf_token" value="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date"><i class="fas fa-calendar-alt"></i> Date</label>
                            <input type="date" class="form-control" id="date" name="date" required onchange="populateDay()">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="time"><i class="fas fa-clock"></i> Time</label>
                            <input type="time" class="form-control" id="time" name="time" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="day"><i class="fas fa-sun"></i> Day</label>
                            <input type="text" class="form-control" id="day" name="day" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description"><i class="fas fa-info-circle"></i> Description</label>
                            <select class="form-control" id="description" name="description" required>
                                <option value="" disabled selected>Select Description</option>
                                    <!-- Description choices from Database-->
                                    <option value="Sales Trip">Sales Trip</option>
                                    <option value="Pick Up">Pick Up</option>
                                    <option value="Delivery">Delivery</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Other form fields for driver, co-driver, vehicle, etc. -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="driver"><i class="fas fa-user"></i> Driver</label>
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
                        <label for="co_driver"><i class="fas fa-user-friends"></i> Co-Driver</label>
                        <select class="form-control" id="co_driver" name="co_driver">
                            <option value="" selected disabled>Select a Co-Driver (optional)</option> <!-- Default empty option -->
                            <?php
                            if ($co_drivers_result->num_rows > 0) {
                                while ($row = $co_drivers_result->fetch_assoc()) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['full_name'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="vehicle"><i class="fas fa-truck"></i> Vehicle</label>
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
                            <label for="from_location"><i class="fas fa-map-marker-alt"></i> From Location</label>
                            <input type="text" class="form-control" id="from_location" name="from_location" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="stops"><i class="fas fa-map-marker-alt"></i> Stops (Optional)</label>
                            <input type="text" class="form-control" id="stops" name="stops" placeholder="Enter stops in order (Optional)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="to_location"><i class="fas fa-map-marker-alt"></i> To Location</label>
                            <input type="text" class="form-control" id="to_location" name="to_location" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="est_distance"><i class="fas fa-ruler"></i> Estimated Distance</label>
                            <input type="text" class="form-control" id="est_distance" name="est_distance">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_odometer"><i class="fas fa-tachometer-alt"></i> Start Odometer</label>
                            <input type="text" class="form-control" id="start_odometer" name="start_odometer" required>
                        </div>
                    </div>
                </div>
                    <!-- Other form fields like vehicle, location, stops, distance, etc. -->
                
                
                <button type="submit" class="btn btn-primary btn-block bg-orange mt-4"><i class="fas fa-paper-plane"></i>
                    
                </button>
            </form>
            <div id="distance" class="text-center mt-3 text-grey"></div>
        </div>
    </div>
    <script src="js/create_trip.js"></script>
    
</body>
</html>
