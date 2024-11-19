<?php
// expenses.php
require 'connection.php'; // Include your MySQLi connection file

// Define pagination variables at the top before using them in the query
$results_per_page = 25;

// Get the current page from the URL, default is page 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate total results
$totalFuelQuery = "SELECT COUNT(*) as total FROM transfer_fuel";
$totalFuelResult = $conn->query($totalFuelQuery);
$totalFuel = $totalFuelResult->fetch_assoc()['total'];
$total_pages = ceil($totalFuel / $results_per_page); // Calculate total pages

// Make sure the current page is within range
$current_page = max(1, min($total_pages, $current_page));

// Calculate the offset based on the current page
$offset = ($current_page - 1) * $results_per_page;

$transferId = isset($_POST['transfer_id']) ? $_POST['transfer_id'] : null;
// Check if the request is POST to handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transferId = $_POST['transfer_id'];
    $fuelConsumed = $_POST['fuel_consumed'];

    // Prepare and execute the insert statement
    $stmt = $conn->prepare("INSERT INTO transfer_fuel (transfer_id, fuel_consumed, created_at) VALUES (?,  ?, NOW())");
    $stmt->bind_param("id", $transferId, $fuelConsumed);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Fuel assigned successfully', 'transfer_id' => $transferId]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to assign Fuel records']);
    }

    $stmt->close();
    $conn->close(); // Close the connection
    exit; // Stop further processing after handling POST
}

// Fetch trips that have not been assigned fuel records
$tripQuery = "
    SELECT 
        transfers.id,
        
        transfers.customer_name,
        transfers.transfer_date,
        transfers.transfer_time,
        transfers.transfer_day,
        drivers.full_name AS driver,
        vehicles.vehicle_regno AS vehicle,
        transfers.destination
    FROM 
        transfers 
    LEFT JOIN 
        drivers ON transfers.driver = drivers.id
    LEFT JOIN 
        vehicles ON transfers.vehicle = vehicles.id
    WHERE 
        transfers.id NOT IN (SELECT transfer_id FROM transfer_fuel)
    ORDER BY 
        transfers.transfer_date ASC
";
$tripResult = $conn->query($tripQuery);

// Fetch assigned fuel records with pagination
$doneFuelQuery = "
    SELECT 
        transfers.id,
        transfer_fuel.transfer_id,
        transfers.customer_name,
        transfers.transfer_date,
        transfers.transfer_time,
        transfers.transfer_day,
        drivers.full_name AS driver,
        vehicles.vehicle_regno AS vehicle,
        transfers.destination,
        transfer_fuel.fuel_consumed
    FROM 
        transfer_fuel
    INNER JOIN 
        transfers ON transfer_fuel.transfer_id = transfers.id
    LEFT JOIN 
        drivers ON transfers.driver = drivers.id
    LEFT JOIN 
        vehicles ON transfers.vehicle = vehicles.id
    ORDER BY 
        transfers.transfer_date ASC
    LIMIT $results_per_page OFFSET $offset
";
$doneFuelResult = $conn->query($doneFuelQuery);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Trips Fuel</title>
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
            <h1>Assign Fuel to Transfer Trips</h1>

            <!-- Trips Table -->
            <div class="trip-table">
                <h2>Pending Transfer Fuel record</h2>
                <table class="table table-dark table-striped" id="pendingTripsTable">
                    <thead>
                        <tr>
                            <th>Trip ID</th>
                            <th>Customer Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Day</th>
                            
                            <th>Driver</th>
                            
                            <th>Vehicle</th>
                        
                            <th>Destination</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($tripResult->num_rows > 0): ?>
                            <?php while ($trip = $tripResult->fetch_assoc()): ?>
                                <tr id="trip-row-<?= $trip['id'] ?>">
                                    <td><?= $trip['id'] ?></td>
                                    <td><?= $trip['customer_name'] ?></td>
                                    <td><?= $trip['transfer_date'] ?></td>
                                    <td><?= $trip['transfer_time'] ?></td>
                                    <td><?= $trip['transfer_day'] ?></td>
                                    
                                    <td><?= $trip['driver'] ?></td>
                                    
                                    <td><?= $trip['vehicle'] ?></td>
                                    
                                    <td><?= $trip['destination'] ?></td>
                                    <td><button class="btn btn-primary" onclick='openAssignTransferFuelModal(<?= json_encode($trip) ?>)'>Assign Fuel</button></td>
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
        <h2>Done Transfer Fuel Records</h2>
        <table class="table table-success table-striped">
            <thead>
                <tr>
                    <th>Trip ID</th>
                    <th>Customer Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Day</th>
                    <th>Driver</th>
                    <th>Vehicle</th>
                    <th>Destination</th>
                    <th>Fuel Consumed</th>          
                </tr>
            </thead>
            <tbody id="doneFuelTableBody">
                <?php if ($doneFuelResult->num_rows > 0): ?>
                    <?php while ($transfer_fuel = $doneFuelResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= $transfer_fuel['transfer_id'] ?></td>
                            <td><?= $transfer_fuel['customer_name'] ?></td>
                            <td><?= $transfer_fuel['transfer_date'] ?></td>
                            <td><?= $transfer_fuel['transfer_time'] ?></td>
                            <td><?= $transfer_fuel['transfer_day'] ?></td>
                            <td><?= $transfer_fuel['driver'] ?></td>
                            <td><?= $transfer_fuel['vehicle'] ?></td>
                            <td><?= $transfer_fuel['destination'] ?></td>
                            <td><?= $transfer_fuel['fuel_consumed'] ?></td>
                            
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
    <div class="modal fade" id="assignTransferFuelModal" tabindex="-1" aria-labelledby="assignTransferFuelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignTransferFuelLabel">Assign Fuel Records for Trip #<span id="modalTripId"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignTransferFuelForm">
                        <input type="hidden" id="transferId" name="transfer_id">
                        <div class="mb-3">
                            <label for="fuelConsumed" class="form-label">Fuel Consumed</label>
                            <input type="number" class="form-control" id="fuelConsumed" name="fuel_consumed" required>
                        </div>
                        
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="assignTransferFuel()">Assign Fuel records</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/transfer_fuel.js"></script>
</body>
</html>
