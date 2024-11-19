<?php
// expenses.php
require 'connection.php'; // Include your MySQLi connection file

// Check if the request is POST to handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tripId = $_POST['trip_id'];
    $fuelConsumed = $_POST['fuel_consumed'];
    

    // Prepare and execute the insert statement
    $stmt = $conn->prepare("INSERT INTO fuel (trip_id, fuel_consumed, created_at) VALUES (?,  ?, NOW())");
    $stmt->bind_param("id", $tripId, $fuelConsumed);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Fuel assigned successfully', 'trip_id' => $tripId]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to assign Fuel records']);
    }

    $stmt->close();
    $conn->close(); // Close the connection
    exit; // Stop further processing after handling POST
}

// Fetch trips from the database that have not been assigned expenses
$tripQuery = "
    SELECT 
        trips.trip_id,
        trips.trip_date,
        trips.trip_time,
        trips.trip_day,
        trips.trip_description,
        drivers.full_name AS driver_full_name,
        co_drivers.full_name AS co_driver_full_name,
        vehicles.vehicle_regno AS vehicle_regno,
        trips.from_location,
        trips.stops,
        trips.to_location
    FROM 
        trips 
    LEFT JOIN 
        drivers ON trips.driver_id = drivers.id 
    LEFT JOIN 
        co_drivers ON trips.co_driver_id = co_drivers.id 
    LEFT JOIN 
        vehicles ON trips.vehicle_id = vehicles.id 
    WHERE 
        trips.trip_id NOT IN (SELECT trip_id FROM fuel)
    ORDER BY 
        trips.trip_date ASC
";
$tripResult = $conn->query($tripQuery);

$results_per_page = 25; 

// Determine the number of total results
$totalFuelQuery = "SELECT COUNT(*) as total FROM fuel";
$totalFuelResult = $conn->query($totalFuelQuery);
$totalFuel = $totalFuelResult->fetch_assoc()['total'];
$total_pages = ceil($totalFuel / $results_per_page); // Calculate total pages

// Get the current page from the URL, default is page 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($total_pages, $current_page)); // Ensure current page is within range
$offset = ($current_page - 1) * $results_per_page;

// Fetch already assigned expenses from the database
$doneFuelQuery = "
    SELECT 
        trips.trip_id,
        trips.trip_date,
        trips.trip_time,
        trips.trip_day,
        trips.trip_description,
        drivers.full_name AS driver_full_name,
        co_drivers.full_name AS co_driver_full_name,
        vehicles.vehicle_regno AS vehicle_regno,
        trips.from_location,
        trips.stops,
        trips.to_location,
        fuel.fuel_consumed
        
    FROM 
        fuel
    INNER JOIN 
        trips ON fuel.trip_id = trips.trip_id
    LEFT JOIN 
        drivers ON trips.driver_id = drivers.id 
    LEFT JOIN 
        co_drivers ON trips.co_driver_id = co_drivers.id 
    LEFT JOIN 
        vehicles ON trips.vehicle_id = vehicles.id 
    ORDER BY 
        trips.trip_date ASC
    LIMIT $results_per_page OFFSET $offset
";
$doneFuelResult = $conn->query($doneFuelQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Fuel to Trips</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/fuel_records.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="d-flex">
        <div class="sidebar">
            <a href="dtms_dashboard.php">Back to Dashboard</a>
        </div>

        <!-- Main Content -->
        <div class="main-content container-fluid col-md-10">
            <h1>Assign Fuel to Trips</h1>

            <!-- Trips Table -->
            <div class="trip-table">
                <h2>Pending Fuel record</h2>
                <table class="table table-dark table-striped" id="pendingTripsTable">
                    <thead>
                        <tr>
                            <th>Trip ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Day</th>
                            <th>Description</th>
                            <th>Driver</th>
                            <th>Co-Driver</th>
                            <th>Vehicle</th>
                            <th>From Location</th>
                            <th>Stops</th>
                            <th>To Location</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($tripResult->num_rows > 0): ?>
                            <?php while ($trip = $tripResult->fetch_assoc()): ?>
                                <tr id="trip-row-<?= $trip['trip_id'] ?>">
                                    <td><?= $trip['trip_id'] ?></td>
                                    <td><?= $trip['trip_date'] ?></td>
                                    <td><?= $trip['trip_time'] ?></td>
                                    <td><?= $trip['trip_day'] ?></td>
                                    <td><?= $trip['trip_description'] ?></td>
                                    <td><?= $trip['driver_full_name'] ?></td>
                                    <td><?= $trip['co_driver_full_name'] ?></td>
                                    <td><?= $trip['vehicle_regno'] ?></td>
                                    <td><?= $trip['from_location'] ?></td>
                                    <td><?= $trip['stops'] ?></td>
                                    <td><?= $trip['to_location'] ?></td>
                                    <td><button class="btn btn-primary" onclick='openAssignFuelModal(<?= json_encode($trip) ?>)'>Assign Fuel</button></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="12">No trips found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Done Fuel Table -->
            <div class="done-fuel-table">
        <h2>Done Expenses</h2>
        <table class="table table-success table-striped">
            <thead>
                <tr>
                    <th>Trip ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Day</th>
                    <th>Description</th>
                    <th>Driver</th>
                    <th>Co-Driver</th>
                    <th>Vehicle</th>
                    <th>From Location</th>
                    <th>Stops</th>
                    <th>To Location</th>
                    <th>Fuel Consumed</th>          
                </tr>
            </thead>
            <tbody id="doneFuelTableBody">
                <?php if ($doneFuelResult->num_rows > 0): ?>
                    <?php while ($fuel = $doneFuelResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= $fuel['trip_id'] ?></td>
                            <td><?= $fuel['trip_date'] ?></td>
                            <td><?= $fuel['trip_time'] ?></td>
                            <td><?= $fuel['trip_day'] ?></td>
                            <td><?= $fuel['trip_description'] ?></td>
                            <td><?= $fuel['driver_full_name'] ?></td>
                            <td><?= $fuel['co_driver_full_name'] ?></td>
                            <td><?= $fuel['vehicle_regno'] ?></td>
                            <td><?= $fuel['from_location'] ?></td>
                            <td><?= $fuel['stops'] ?></td>
                            <td><?= $fuel['to_location'] ?></td>
                            <td><?= $fuel['fuel_consumed'] ?></td>
                            
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="13">No Fuel records found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                <li class="page-item <?= $page === $current_page ? 'active' : '' ?>">
                    <a class="page-link" href="fuel_records.php?page=<?= $page ?>"><?= $page ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
        </div>
    </div>
        
     <!-- Assign Fuel  Modal -->
    <div class="modal fade" id="assignFuelModal" tabindex="-1" aria-labelledby="assignFuelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignFuelLabel">Assign Fuel Records for Trip #<span id="modalTripId"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignFuelForm">
                        <input type="hidden" id="tripId" name="trip_id">
                        <div class="mb-3">
                            <label for="fuelConsumed" class="form-label">Fuel Consumed</label>
                            <input type="number" class="form-control" id="fuelConsumed" name="fuel_consumed" required>
                        </div>
                        
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="assignFuel()">Assign Fuel records</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/fuel_records.js"></script>
</body>
</html>
