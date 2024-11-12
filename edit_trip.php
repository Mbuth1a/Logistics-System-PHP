<?php
// Include database connection
require_once 'db_connection.php';

// Initialize variables
$error = '';
$success = '';

// Fetch the trip ID from the URL
$trip_id = $_GET['trip_id'] ?? null;

if ($trip_id) {
    // Fetch trip details from the database
    $stmt = $conn->prepare("SELECT * FROM trips WHERE trip_id = ?");
    $stmt->bind_param("i", $trip_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $trip = $result->fetch_assoc();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Print form values
    echo "<pre>";
    print_r($_POST); // Print all submitted form values
    echo "</pre>";
    exit; // Stop execution to check the output

    // Fetch the selected date and time
    $date = $_POST['date'] ?? ''; // Default to empty string if not set
    $time = $_POST['time'] ?? '';

    if (!empty($date) && !empty($time)) {
        // Calculate the day of the week
        $day = date('l', strtotime($date)); // 'l' gives the full textual representation of the day

        // Prepare and execute the update SQL statement
        $update_sql = "UPDATE trips SET date = ?, day = ?, time = ?, description = ?, driver_id = ?, co_driver_id = ?, vehicle_id = ?, from_location = ?, stops = ?, to_location = ?, est_distance = ?, start_odometer = ? WHERE trip_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssssissssssi", $date, $day, $time, $_POST['description'], $_POST['driver'], $_POST['co_driver'], $_POST['vehicle'], $_POST['from_location'], $_POST['stops'], $_POST['to_location'], $_POST['est_distance'], $_POST['start_odometer'], $trip_id);
        
        // Execute the update
        if ($update_stmt->execute()) {
            // Redirect or display a success message
            header("Location: trips.php?success=Trip updated successfully");
            exit;
        } else {
            $error = "Error updating trip: " . $update_stmt->error;
        }
    } else {
        $error = "Date and Time must be provided.";
    }
}

// Fetch drivers and vehicles for dropdowns (add this if needed)
$drivers = []; // Fetch drivers from the database
$vehicles = []; // Fetch vehicles from the database

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Trip</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css"> <!-- Adjust path as necessary -->
</head>
<body>

<div class="container mt-5">
    <h2>Edit Trip</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form action="edit_trip.php?trip_id=<?php echo htmlspecialchars($trip_id); ?>" method="POST">
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" id="date" name="date" required value="<?php echo htmlspecialchars($trip['date']); ?>">
        </div>
        <div class="mb-3">
            <label for="time" class="form-label">Time</label>
            <input type="time" class="form-control" id="time" name="time" required value="<?php echo htmlspecialchars($trip['time']); ?>">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($trip['description']); ?>">
        </div>
        <div class="mb-3">
            <label for="driver" class="form-label">Driver</label>
            <select class="form-select" id="driver" name="driver" required>
                <option value="">Select Driver</option>
                <?php foreach ($drivers as $driver): ?>
                    <option value="<?php echo htmlspecialchars($driver['id']); ?>" <?php echo $trip['driver_id'] == $driver['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($driver['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="co_driver" class="form-label">Co-Driver</label>
            <select class="form-select" id="co_driver" name="co_driver">
                <option value="">Select Co-Driver</option>
                <?php foreach ($drivers as $driver): ?>
                    <option value="<?php echo htmlspecialchars($driver['id']); ?>" <?php echo $trip['co_driver_id'] == $driver['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($driver['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="vehicle" class="form-label">Vehicle</label>
            <select class="form-select" id="vehicle" name="vehicle" required>
                <option value="">Select Vehicle</option>
                <?php foreach ($vehicles as $vehicle): ?>
                    <option value="<?php echo htmlspecialchars($vehicle['id']); ?>" <?php echo $trip['vehicle_id'] == $vehicle['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($vehicle['registration_number']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="from_location" class="form-label">From Location</label>
            <input type="text" class="form-control" id="from_location" name="from_location" value="<?php echo htmlspecialchars($trip['from_location']); ?>">
        </div>
        <div class="mb-3">
            <label for="stops" class="form-label">Stops</label>
            <input type="text" class="form-control" id="stops" name="stops" value="<?php echo htmlspecialchars($trip['stops']); ?>">
        </div>
        <div class="mb-3">
            <label for="to_location" class="form-label">To Location</label>
            <input type="text" class="form-control" id="to_location" name="to_location" value="<?php echo htmlspecialchars($trip['to_location']); ?>">
        </div>
        <div class="mb-3">
            <label for="est_distance" class="form-label">Estimated Distance (km)</label>
            <input type="number" class="form-control" id="est_distance" name="est_distance" value="<?php echo htmlspecialchars($trip['est_distance']); ?>">
        </div>
        <div class="mb-3">
            <label for="start_odometer" class="form-label">Start Odometer</label>
            <input type="number" class="form-control" id="start_odometer" name="start_odometer" value="<?php echo htmlspecialchars($trip['start_odometer']); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update Trip</button>
        <a href="trips.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="path/to/bootstrap.bundle.min.js"></script> <!-- Adjust path as necessary -->
</body>
</html>
