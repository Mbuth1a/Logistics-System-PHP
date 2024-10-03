<?php
include 'connection.php';

// Initialize drivers array
$drivers = [];

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $full_name = $_POST['full_name'];
    $employee_number = $_POST['employee_number'];
    $license_number = $_POST['license_number'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO drivers (full_name, employee_number, license_number, phone_number, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $employee_number, $license_number, $phone_number, $email);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Failed: " . $stmt->error; // Show error if insert fails
    }

    $stmt->close();
}

// Fetch registered drivers
$result = $conn->query("SELECT * FROM drivers");
if ($result) {
    $drivers = $result->fetch_all(MYSQLI_ASSOC);
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Drivers - DANCO LTD Logistics System</title>
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
        <h2><i class="fas fa-user-plus"></i> Manage Drivers</h2>
        <div class="modal-body">
            <!-- Button to trigger modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addDriverModal">
                <i class="fas fa-plus"></i> Add Driver
            </button>

            <!-- Driver Form Modal -->
            <div class="modal fade" id="addDriverModal" tabindex="-1" aria-labelledby="addDriverModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addDriverModalLabel"><i class="fas fa-user-plus"></i> Add New Driver</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        
                        <!-- Driver Form inside modal -->
                        <form action = "add_driver.php" method="POST">
                            <div class="form-group">
                                <label for="full_name"><i class="fas fa-user"></i> Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required autofocus>
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
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" name="save_driver" class="btn btn-primary" value="Save">SAVE</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drivers Table -->
        <div class="mt-4">
    <h3>Registered Drivers</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Full Name</th>
                <th scope="col">Employee Number</th>
                <th scope="col">License Number</th>
                <th scope="col">Phone Number</th>
                <th scope="col">Email</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($drivers)): ?>
                <?php foreach ($drivers as $index => $driver): ?>
                    <tr>
                        <th scope="row"><?= $index + 1 ?></th>
                        <td><?= htmlspecialchars($driver['full_name']) ?></td>
                        <td><?= htmlspecialchars($driver['employee_number']) ?></td>
                        <td><?= htmlspecialchars($driver['license_number']) ?></td>
                        <td><?= htmlspecialchars($driver['phone_number']) ?></td>
                        <td><?= htmlspecialchars($driver['email']) ?></td>
                        <td>
                            <a href="update_driver.php?id=<?= $driver['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Update
                            </a>
                            <a href="delete_driver.php?id=<?= $driver['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this driver?');">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No drivers registered yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
    </div>
    
    <!-- Dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
