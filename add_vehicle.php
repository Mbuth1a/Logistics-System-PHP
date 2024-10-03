<?php
include 'connection.php';

// Initialize drivers array
$vehicles = [];

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $vehicle_regno = $_POST['vehicle_regno'];
    $vehicle_model = $_POST['vehicle_model'];
    $vehicle_type = $_POST['vehicle_type'];
    $engine_number = $_POST['engine_number'];
    $capacity = $_POST['capacity'];

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO vehicles (vehicle_regno, vehicle_model, vehicle_type, engine_number, capacity) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $vehicle_regno, $vehicle_model, $vehicle_type, $engine_number, $capacity);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Failed: " . $stmt->error; // Show error if insert fails
    }

    $stmt->close();
}

// Fetch registered drivers
$result = $conn->query("SELECT * FROM vehicles");
if ($result) {
    $vehicles = $result->fetch_all(MYSQLI_ASSOC);
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vehicles - DANCO LTD Logistics System</title>
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
        <h2><i class="fas fa-user-plus"></i> Manage Vehicles</h2>
        <div class="modal-body">
            <!-- Button to trigger modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addVehicleModal">
                <i class="fas fa-plus"></i> Add Vehicle
            </button>

            <!-- Driver Form Modal -->
            <div class="modal fade" id="addVehicleModal" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addVehicleModalLabel"><i class="fas fa-user-plus"></i> Add New Vehicle</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        
                        <!-- Driver Form inside modal -->
                        <form action = "add_vehicle.php" method="POST">
                            <div class="form-group">
                                <label for="vehicle_regno"><i class="fas fa-user"></i> Vehicle Registration Number</label>
                                <input type="text" class="form-control" id="vehicle_regno" name="vehicle_regno" required autofocus>
                            </div>
                            <div class="form-group">
                                <label for="vehicle_model"><i class="fas fa-user-tag"></i> Vehicle Model</label>
                                <input type="text" class="form-control" id="vehicle_model" name="vehicle_model" required>
                            </div>
                            <div class="form-group">
                                <label for="vehicle_type"><i class="fas fa-id-card"></i> Vehicel Type</label>
                                <input type="text" class="form-control" id="vehicle_type" name="vehicle_type" required>
                            </div>
                            <div class="form-group">
                                <label for="engine_number"><i class="fas fa-phone"></i> Engine Number</label>
                                <input type="tel" class="form-control" id="engine_number" name="engine_number" required>
                            </div>
                            <div class="form-group">
                                <label for="capacity"><i class="fas fa-envelope"></i> Capacity </label>
                                <input type="capacity" class="form-control" id="capacity" name="capacity" required>
                                <small id="emailHelp" class="form-text text-muted">Please enter the vehicle capacity</small>
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
            <h3>Registered Vehicles</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Vehicle Registration</th>
                        <th scope="col">Vehicle Model</th>
                        <th scope="col">Vehicle Type</th>
                        <th scope="col">Engine Number</th>
                        <th scope="col">Capacity</th>
                    </tr>
                </thead>
                <tbody>
            <?php if (!empty($vehicles)): ?>
            <?php foreach ($vehicles as $index => $vehicle): ?>
            <tr>
                <th scope="row"><?= $index + 1 ?></th>
                <td><?= htmlspecialchars($vehicle['vehicle_regno']) ?></td>
                <td><?= htmlspecialchars($vehicle['vehicle_model']) ?></td>
                <td><?= htmlspecialchars($vehicle['vehicle_type']) ?></td>
                <td><?= htmlspecialchars($vehicle['engine_number']) ?></td>
                <td><?= htmlspecialchars($vehicle['capacity']) ?></td>
                <td>
                    <!-- Update button -->
                    <a href="update_vehicle.php?id=<?= $vehicle['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Update
                    </a>
                    <!-- Delete button -->
                    <a href="delete_vehicle.php?id=<?= $vehicle['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this vehicle?');">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" class="text-center">No Vehicles registered yet.</td>
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
