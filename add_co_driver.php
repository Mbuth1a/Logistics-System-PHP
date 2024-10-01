<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Co-Driver - DANCO LTD Logistics System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/add_co_driver.css"> <!-- Assuming your CSS file is in the same directory -->
    <style>
        /* General styles */

    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>


    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid mt-5 col-md-12">
            <h2><i class="fas fa-user-friends"></i> Add New Co-Driver</h2>
            <form id="addCoDriverForm" method="post" action="add_co_driver.php"> <!-- Update the action URL to your PHP processing file -->
                <input type="hidden" name="csrf_token" value=""> <!-- CSRF Token -->

                <!-- Employee Number Field -->
                <div class="form-group">
                    <label for="employeeNumber">Employee Number</label>
                    <input type="text" name="employee_number" class="form-control" id="employeeNumber" required placeholder="DCL-123" pattern="DCL.*" title="Employee number must start with 'DCL'">
                </div>

                <!-- Co-Driver Name Field -->
                <div class="form-group">
                    <label for="coDriver">Co-driver Name</label>
                    <input type="text" name="co_driver_name" class="form-control" id="coDriver" required placeholder="Co-driver Name">
                </div>

                <!-- Phone Number Field -->
                <div class="form-group">
                    <label for="phoneNumber">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" id="phoneNumber" required placeholder="Phone Number" pattern="\d{10}" title="Phone number must be exactly 10 digits.">
                </div>

                <!-- Email Address Field -->
                <div class="form-group">
                    <label for="emailAddress">Email Address</label>
                    <input type="email" name="email_address" class="form-control" id="emailAddress" required placeholder="Email Address (@gmail.com)" pattern="[a-z0-9._%+-]+@gmail\.com" title="Email address must end with '@gmail.com'">
                </div>

                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane"></i> Submit</button>
            </form>
        </div>
    </div>

    <!-- JavaScript for Form Validation and Back to Dashboard Functionality -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
      

        document.getElementById('addCoDriverForm').addEventListener('submit', function(event) {
            var employeeNumber = document.getElementById('employeeNumber').value;
            var phoneNumber = document.getElementById('phoneNumber').value;
            var emailAddress = document.getElementById('emailAddress').value;

            // Check if phone number is exactly 10 digits
            if (phoneNumber.length !== 10) {
                alert('Phone number must be exactly 10 digits.');
                event.preventDefault(); // Prevent form submission
                return;
            }

            // Check if employee number starts with 'DCL'
            if (!employeeNumber.startsWith('DCL')) {
                alert('Employee number must start with "DCL".');
                event.preventDefault(); // Prevent form submission
                return;
            }

            // Check if email address ends with '@gmail.com'
            if (!emailAddress.endsWith('@gmail.com')) {
                alert('Email address must end with "@gmail.com".');
                event.preventDefault(); // Prevent form submission
                return;
            }
        });
    </script>
</body>
</html>
