<?php
// Database connection
require "connection.php";

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize success message
$success_message = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate the input
    $customer_name = htmlspecialchars(trim($_POST['customer_name']));
    $walk_trip_date = htmlspecialchars(trim($_POST['walk_trip_date']));
    $walk_trip_day = htmlspecialchars(trim($_POST['walk_trip_day']));
    $walk_trip_time = htmlspecialchars(trim($_POST['walk_trip_time']));

    // Check if all required fields are filled
    if (!empty($customer_name) && !empty($walk_trip_date) && !empty($walk_trip_day) && !empty($walk_trip_time)) {
        // Prepare SQL query to insert data into the database
        $sql = "INSERT INTO walk_trips (customer_name, walk_trip_date, walk_trip_day, walk_trip_time) 
                VALUES (?, ?, ?, ?)";

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters to the SQL query
            $stmt->bind_param("ssss", $customer_name, $walk_trip_date, $walk_trip_day, $walk_trip_time);

            // Execute the statement
            if ($stmt->execute()) {
                $success_message = "Walk trip has been successfully added!";
            } else {
                $success_message = "Error: Unable to add walk trip.";
            }

            // Close the prepared statement
            $stmt->close();
        } else {
            $success_message = "Error: " . $conn->error;
        }

        // Redirect to the same page to prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $success_message = "Please fill in all fields.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Walk In Trip</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/walk.css">
</head>
<body>

<div class="sidebar">
    <a href="dtms_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>
<div class="main-content">

    <h2 class="text-center text-orange mb-4"><i class="fas fa-road"></i> Walk In Trip</h2>

    <!-- Display success message at the top of the page -->
    <?php if ($success_message): ?>
        <div id="successMessage" class="alert alert-success text-center mt-3" role="alert">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form id="walkTripForm" method="post" action="walk.php">
            <input type="hidden" name="csrf_token" value="">
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer_name"><i class="fas fa-user"></i> Customer Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="walk_trip_date"><i class="fas fa-calendar-alt"></i> Date</label>
                        <input type="date" class="form-control" id="walk_trip_date" name="walk_trip_date" required onchange="populateDay()">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="walk_trip_time"><i class="fas fa-clock"></i> Time</label>
                        <input type="time" class="form-control" id="walk_trip_time" name="walk_trip_time" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="walk_trip_day"><i class="fas fa-sun"></i> Day</label>
                        <input type="text" class="form-control" id="walk_trip_day" name="walk_trip_day" readonly>
                    </div>
                </div>

            </div>

            <button type="submit" class="btn btn-primary btn-block bg-orange mt-4"><i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
</div>

<script src="js/walk.js"></script>
<script>
    // Hide the success message after 5 seconds
    <?php if ($success_message): ?>
        setTimeout(function() {
            document.getElementById('successMessage').style.display = 'none';
        }, 5000); // 5000ms = 5 seconds
    <?php endif; ?>
</script>

</body>
</html>
