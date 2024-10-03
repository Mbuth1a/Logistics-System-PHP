<?php
include 'connection.php';

// Fetch vehicle details if ID is set
$vehicle = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehicle = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $vehicle_regno = $_POST['vehicle_regno'];
    $vehicle_model = $_POST['vehicle_model'];
    $vehicle_type = $_POST['vehicle_type'];
    $engine_number = $_POST['engine_number'];
    $capacity = $_POST['capacity'];

    // Update data in the database
    $stmt = $conn->prepare("UPDATE vehicles SET vehicle_regno = ?, vehicle_model = ?, vehicle_type = ?, engine_number = ?, capacity = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $vehicle_regno, $vehicle_model, $vehicle_type, $engine_number, $capacity, $id);

    if ($stmt->execute()) {
        header('Location: add_vehicle.php'); // Redirect back to the main page
        exit();
    } else {
        echo "Failed to update: " . $stmt->error; // Show error if update fails
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Vehicle - DANCO LTD Logistics System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Update Vehicle</h2>
        <form action="update_vehicle.php?id=<?= $vehicle['id'] ?>" method="POST">
            <div class="form-group">
                <label for="vehicle_regno">Vehicle Registration Number</label>
                <input type="text" class="form-control" id="vehicle_regno" name="vehicle_regno" value="<?= htmlspecialchars($vehicle['vehicle_regno']) ?>" required>
            </div>
            <div class="form-group">
                <label for="vehicle_model">Vehicle Model</label>
                <input type="text" class="form-control" id="vehicle_model" name="vehicle_model" value="<?= htmlspecialchars($vehicle['vehicle_model']) ?>" required>
            </div>
            <div class="form-group">
                <label for="vehicle_type">Vehicle Type</label>
                <input type="text" class="form-control" id="vehicle_type" name="vehicle_type" value="<?= htmlspecialchars($vehicle['vehicle_type']) ?>" required>
            </div>
            <div class="form-group">
                <label for="engine_number">Engine Number</label>
                <input type="tel" class="form-control" id="engine_number" name="engine_number" value="<?= htmlspecialchars($vehicle['engine_number']) ?>" required>
            </div>
            <div class="form-group">
                <label for="capacity">Capacity</label>
                <input type="text" class="form-control" id="capacity" name="capacity" value="<?= htmlspecialchars($vehicle['capacity']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Vehicle</button>
            <a href="add_vehicle.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
