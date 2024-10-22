<?php
include 'connection.php';
session_start();
// Pagination variables
$entries_per_page = 25; // Number of entries per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page from query string
$offset = ($current_page - 1) * $entries_per_page; // Offset for SQL query

// Generate a CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch trips from the database that do not have fuel consumption recorded
$query = "
    SELECT t.trip_id, v.vehicle_regno, t.trip_date, t.from_location, t.to_location 
    FROM trips t
    JOIN vehicles v ON t.vehicle_id = t.vehicle_id
    LEFT JOIN fuel f ON t.trip_id = f.trip_id
    WHERE f.trip_id IS NULL
    LIMIT $entries_per_page OFFSET $offset";

$result = $conn->query($query);

// Fetch total number of trips without fuel records for pagination
$total_query = "SELECT COUNT(*) as total FROM trips t LEFT JOIN fuel f ON t.trip_id = f.trip_id WHERE f.trip_id IS NULL";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_trips = $total_row['total'];
$total_pages = ceil($total_trips / $entries_per_page); // Total pages for pagination

// Fetch already saved fuel records from the database for pagination
$fuelQuery = "
    SELECT f.id, t.trip_id, v.vehicle_regno, f.fuel_consumed, f.created_at, t.from_location, t.to_location 
    FROM fuel f
    JOIN trips t ON f.trip_id = t.trip_id
    JOIN vehicles v ON t.vehicle_id = t.vehicle_id
    LIMIT $entries_per_page OFFSET $offset"; // Pagination on fuel records
$fuelResult = $conn->query($fuelQuery);

// Fetch total number of saved fuel records for pagination
$total_fuel_query = "SELECT COUNT(*) as total FROM fuel";
$total_fuel_result = $conn->query($total_fuel_query);
$total_fuel_row = $total_fuel_result->fetch_assoc();
$total_fuel_records = $total_fuel_row['total'];
$total_fuel_pages = ceil($total_fuel_records / $entries_per_page); // Total pages for fuel records pagination

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fuel Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/fuel_records.css">
</head>
<body>
    <div class="sidebar">
        <a href="dtms_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <div class="content">
        <h2 class="text-center">Fuel Management</h2>

        <!-- Search Form -->
        <div class="form-inline mb-4">
            <div class="form-group">
                <label for="startDate" class="mr-2">From:</label>
                <input type="date" id="startDate" class="form-control">
            </div>
            <div class="form-group mx-sm-3">
                <label for="endDate" class="mr-2">To:</label>
                <input type="date" id="endDate" class="form-control">
            </div>
            <div class="form-group mx-sm-3">
                <label for="vehicleSelect" class="mr-2">Vehicle:</label>
                <select id="vehicleSelect" class="form-control">
                    <option value="">All Vehicles</option>
                    <!-- Dynamically populated -->
                </select>
            </div>
            <button class="btn btn-primary" onclick="searchFuelRecords()">Search</button>
        </div>

        <!-- Trips List -->
        <h3 class="text-center">Trips Without Fuel Records</h3>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vehicle</th>
                    <th>Date</th>
                    <th>From Location</th>
                    <th>To Location</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tripsList">
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['trip_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['vehicle_regno']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['trip_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['from_location']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['to_location']) . "</td>";
                        echo "<td><button class='btn btn-info' onclick='openFuelModal(" . htmlspecialchars($row['trip_id']) . ", \"" . addslashes(htmlspecialchars($row['vehicle_regno'])) . "\", \"" . addslashes(htmlspecialchars($row['trip_date'])) . "\", \"" . addslashes(htmlspecialchars($row['from_location'])) . "\", \"" . addslashes(htmlspecialchars($row['to_location'])) . "\")'>Add Fuel</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No trips found</td></tr>";
                }
                ?>
            </tbody>
        </table>
            <!-- Pagination Controls -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if ($current_page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($current_page == $i) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php if ($current_page >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- Fuel Records Table -->
        <h3 class="text-center mt-5">Saved Fuel Records</h3>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vehicle</th>
                    <th>Trip Date</th>
                    <th>From Location</th>
                    <th>To Location</th>
                    <th>Fuel Consumed (liters)</th>
                    <th>Recorded At</th>
                </tr>
            </thead>
            <tbody id="fuelRecordsList">
                <?php
                if ($fuelResult && $fuelResult->num_rows > 0) {
                    while ($fuelRow = $fuelResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($fuelRow['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($fuelRow['vehicle_regno']) . "</td>";
                        echo "<td>" . htmlspecialchars($fuelRow['created_at']) . "</td>";
                        echo "<td>" . htmlspecialchars($fuelRow['from_location']) . "</td>";
                        echo "<td>" . htmlspecialchars($fuelRow['to_location']) . "</td>";
                        echo "<td>" . htmlspecialchars($fuelRow['fuel_consumed']) . " liters</td>";
                        echo "<td>" . htmlspecialchars($fuelRow['created_at']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No fuel records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
            <!-- Pagination Controls for Fuel Records -->
        <nav aria-label="Fuel navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if ($current_page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_fuel_pages; $i++): ?>
                    <li class="page-item <?php if ($current_page == $i) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php if ($current_page >= $total_fuel_pages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Modal for Fuel Consumption -->
    <div class="modal fade" id="fuelModal" tabindex="-1" role="dialog" aria-labelledby="fuelModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fuelModalLabel">Add Fuel Consumption</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="fuelForm">
                        <input type="hidden" id="tripId">
                        <input type="hidden" id="csrfToken" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="form-group">
                            <label for="vehicle">Vehicle</label>
                            <input type="text" class="form-control" id="vehicle" readonly>
                        </div>
                        <div class="form-group">
                            <label for="tripDate">Date</label>
                            <input type="text" class="form-control" id="tripDate" readonly>
                        </div>
                        <div class="form-group">
                            <label for="fromLocation">From</label>
                            <input type="text" class="form-control" id="fromLocation" readonly>
                        </div>
                        <div class="form-group">
                            <label for="toLocation">To</label>
                            <input type="text" class="form-control" id="toLocation" readonly>
                        </div>
                        <div class="form-group">
                            <label for="fuelConsumed">Fuel Consumed (liters)</label>
                            <input type="number" class="form-control" id="fuelConsumed" min="0" step="0.01" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveFuelBtn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="js/fuel_records.js"></script>
</body>
</html>
