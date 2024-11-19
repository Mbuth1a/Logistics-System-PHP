<?php
// Start the session to store messages
session_start();

// Include database connection
include('connection.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $export_date = $_POST['export_date'];
    $customer_name = $_POST['customer_name'];
    $export_day = $_POST['export_day'];
    $proforma = $_POST['proforma'];
    $truck_regno = $_POST['truck_regno'];
    $truck_no = $_POST['truck_no'];
    $destination = $_POST['destination'];

    // Check for CSRF token (add validation if needed)
    $csrf_token = $_POST['csrf_token']; 
    // In a real application, ensure to validate the CSRF token for security.

    // Prepare and execute the SQL query to insert the form data
    $sql = "INSERT INTO export_trips (export_date, customer_name, export_day, proforma, truck_regno, truck_no, destination) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("sssssss", $export_date, $customer_name, $export_day, $proforma, $truck_regno, $truck_no, $destination);
        
        // Execute the statement
        if ($stmt->execute()) {
            // Set success message in session
            $_SESSION['message'] = "Trip exported successfully!";
            $_SESSION['msg_type'] = "success";
        } else {
            // Set error message in session
            $_SESSION['message'] = "Error: " . $stmt->error;
            $_SESSION['msg_type'] = "error";
        }

        // Close the statement
        $stmt->close();
    } else {
        $_SESSION['message'] = "Error preparing the statement: " . $conn->error;
        $_SESSION['msg_type'] = "error";
    }

    // Close the database connection
    $conn->close();

    // Redirect back to the page to display the message
    header("Location: load_export.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Trip</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/export_trip.css">
</head>
<body>

<div class="sidebar">
    <a href="dtms_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>
<div class="main-content">

    <h2 class="text-center text-orange mb-4"><i class="fas fa-road"></i> Export Trip</h2>
    
    <!-- Display success or error message -->
    <?php
    if (isset($_SESSION['message'])) {
        $msg_type = $_SESSION['msg_type'] == 'success' ? 'alert-success' : 'alert-danger';
        echo "<div class='alert $msg_type text-center'>" . $_SESSION['message'] . "</div>";
        unset($_SESSION['message']);
        unset($_SESSION['msg_type']);
    }
    ?>

    <div class="form-container">
        <form id="exportTripForm" method="post" action="export_trip.php">
            <input type="hidden" name="csrf_token" value="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="export_date"><i class="fas fa-calendar-alt"></i> DATE</label>
                        <input type="date" class="form-control" id="export_date" name="export_date" required onchange="populateDay()">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer_name"><i class="fas fa-user"></i>CUSTOMER NAME</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="export_day"><i class="fas fa-sun"></i> DAY</label>
                        <input type="text" class="form-control" id="export_day" name="export_day" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="proforma"><i class="fas fa-file-invoice"></i>PROFORMA INVOICE</label>
                        <input type="varchar" class="form-control" id="proforma" name="proforma" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="truck_regno"><i class="fas fa-truck"></i> TRUCK REG NUMBER</label>
                        <input type="varchar" class="form-control" id="truck_regno" name="truck_regno" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="truck_no"><i class="fas fa-user-friends"></i>TRUCK NUMBER</label>
                        <input type="varchar" class="form-control" id="truck_no" name="truck_no" required>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="destination"><i class="fas fa-truck"></i>DESTINATION</label>
                        <input type="text" class="form-control" id="destination" name="destination" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block bg-orange mt-4"><i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
</div>

<script src="js/export_trip.js"></script>

</body>
</html>
