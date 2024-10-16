<?php
include 'connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission to add a new bike
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'], $_POST['expiry_date'])) {
    $name = $_POST['name'];
    $expiry_date = $_POST['expiry_date'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO bikes (name, expiry_date) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $expiry_date);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to the same page to see the updated list of bikes
        header("Location: bike_parking.php");
        exit();
    } else {
        die("Error: " . $stmt->error);
    }
}

// Handle bike deletion
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and bind
    $stmt = $conn->prepare("DELETE FROM bikes WHERE id = ?");
    $stmt->bind_param("i", $id);

    // Execute the query
    if ($stmt->execute()) {
        header("Location: bike_parking.php"); // Redirect after deletion
        exit();
    } else {
        die("Error: " . $stmt->error);
    }
}

// Fetch bikes from database
$bikes = [];
$sql = "SELECT id, name, expiry_date, DATEDIFF(expiry_date, CURDATE()) AS days_remaining FROM bikes";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bikes[] = $row;
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIKE PARKING MANAGER</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bike_parking.css">
</head>
<body>
    <div class="sidebar">
        <h2>BIKE PARKING</h2>
        <a href="#" class="back-button" onclick="goToDashboard()">Back</a>
    </div>

    <div class="container col-md-10">
        <h1 class="text-light">Bike Parking Manager</h1>

        <!-- Form to add new bike -->
        <form id="bike-form" method="POST" class="mb-4">
            <div class="form-group">
                <label for="name" class="text-light">Bike Registration</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter bike registration number" required>
            </div>
            <div class="form-group">
                <label for="expiry_date" class="text-light">Expiry Date</label>
                <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Bike</button>
        </form>

        <!-- List of bikes with color-coded expiry dates -->
        <div id="bike-list">
            <?php
            // Check if there are bikes to display
            if (!empty($bikes)) {
                foreach ($bikes as $bike) {
                    // Determine the color class based on days remaining
                    if ($bike['days_remaining'] > 30) {
                        $colorClass = 'bg-success'; // More than 30 days
                    } elseif ($bike['days_remaining'] > 15) {
                        $colorClass = 'bg-success'; // 15 to 30 days (green)
                    } elseif ($bike['days_remaining'] > 7) {
                        $colorClass = 'bg-warning'; // 7 to 15 days (orange)
                    } elseif ($bike['days_remaining'] >= 0) {
                        $colorClass = 'bg-danger'; // 0 to 7 days (red)
                    } else {
                        $colorClass = 'bg-secondary'; // Expired bikes
                    }
                    echo "<div class='card $colorClass p-3 mb-3' id='bike-{$bike['id']}'>
                            <h4>{$bike['name']}</h4>
                            <p>Expiry Date: {$bike['expiry_date']}</p>
                            <p>Days Remaining: {$bike['days_remaining']} days</p>
                            <form method='POST' action='bike_parking.php?id={$bike['id']}' style='display:inline;' onsubmit='return confirmDelete();'>
                                <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
                            </form>
                          </div>";
                }
            } else {
                echo "<p>No bikes added yet.</p>";
            }
            ?>
        </div>
    </div>

    <script>
        // Function to ask for delete confirmation
        function confirmDelete() {
            return confirm('Are you sure you want to delete this bike?');
        }

        function goToDashboard() {
            window.location.href = "dtms_dashboard.php"; // Adjust URL as necessary
        }
    </script>
</body>
</html>
