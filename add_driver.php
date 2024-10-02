<?php
// Include the database connection file
require 'connection.php'; // Ensure this path is correct

// Initialize an empty array for errors
$errors = [];

// Check if the request method exists and is POST
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    if (empty($_POST['full_name'])) {
        $errors[] = 'Full Name is required.';
    }
    if (empty($_POST['employee_number'])) {
        $errors[] = 'Employee Number is required.';
    }
    if (empty($_POST['license_number'])) {
        $errors[] = 'License Number is required.';
    }
    if (empty($_POST['phone_number'])) {
        $errors[] = 'Phone Number is required.';
    }
    if (empty($_POST['email'])) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    // If no errors, proceed with inserting into the database
    if (empty($errors)) {
        // Sanitize form inputs
        $full_name = htmlspecialchars($_POST['full_name']);
        $employee_number = htmlspecialchars($_POST['employee_number']);
        $license_number = htmlspecialchars($_POST['license_number']);
        $phone_number = htmlspecialchars($_POST['phone_number']);
        $email = htmlspecialchars($_POST['email']);

        // Insert data into the database
        try {
            // Prepare an SQL statement
            $stmt = $pdo->prepare("INSERT INTO drivers (full_name, employee_number, license_number, phone_number, email) 
                                    VALUES (:full_name, :employee_number, :license_number, :phone_number, :email)");

            // Bind parameters
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':employee_number', $employee_number);
            $stmt->bindParam(':license_number', $license_number);
            $stmt->bindParam(':phone_number', $phone_number);
            $stmt->bindParam(':email', $email);

            // Execute the statement
            $stmt->execute();

            // Success message
            echo "<p style='color:green;'>Driver added successfully!</p>";
        } catch (PDOException $e) {
            // Handle any errors during the query execution
            echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
        }
    } else {
        // Display errors
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
} else {
    echo "<p style='color:red;'>Form submission failed. Please use the correct method (POST).</p>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Driver - DANCO LTD Logistics System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/add_driver.css"> <!-- Update path accordingly -->
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <!-- Main Content -->
    <div class="container-fluid col-md-10" style="margin-left: 250px;"> <!-- Adjust margin to accommodate sidebar width -->
        <h2><i class="fas fa-user-plus"></i> Add New Driver</h2>

        <!-- Driver Form -->
        <form action="add_driver.php" method="post">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="full_name"><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label for="employee_number"><i class="fas fa-user-tag"></i> Employee Number</label>
                <input type="text" class="form-control" id="employee_number" name="employee_number" required>
            </div>
            <div class="form-group">
                <label for="license_number"><i class="fas fa-id-card"></i> License Number</label>
                <input type="text" class="form-control" id="license_number" name="license_number" required>
            </div>
            <div class="form-group">
                <label for="phone_number"><i class="fas fa-phone"></i> Phone Number</label>
                <input type="tel" class="form-control" id="phone_number" name="phone_number" required>
            </div>
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <small id="emailHelp" class="form-text text-muted">Please enter an email with @gmail.com domain.</small>
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane"></i> Submit</button>
        </form>
    </div>
    
    <!-- Dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
