<?php
// expenses.php
require 'connection.php'; // Include your MySQLi connection file

// Check if the request is POST to handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tripId = $_POST['trip_id'];
    $driverExpense = $_POST['driver_expense'];
    $coDriverExpense = $_POST['co_driver_expense'];

    // Prepare and execute the insert statement
    $stmt = $conn->prepare("INSERT INTO expenses (trip_id, driver_expense, co_driver_expense, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("idd", $tripId, $driverExpense, $coDriverExpense);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Expense assigned successfully', 'trip_id' => $tripId]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to assign expense']);
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
        trips.trip_id NOT IN (SELECT trip_id FROM expenses)
    ORDER BY 
        trips.trip_date ASC
";
$tripResult = $conn->query($tripQuery);

// Fetch already assigned expenses from the database
$doneExpensesQuery = "
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
        expenses.driver_expense,
        expenses.co_driver_expense
    FROM 
        expenses
    INNER JOIN 
        trips ON expenses.trip_id = trips.trip_id
    LEFT JOIN 
        drivers ON trips.driver_id = drivers.id 
    LEFT JOIN 
        co_drivers ON trips.co_driver_id = co_drivers.id 
    LEFT JOIN 
        vehicles ON trips.vehicle_id = vehicles.id 
    ORDER BY 
        trips.trip_date ASC
";
$doneExpensesResult = $conn->query($doneExpensesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Expenses to Trips</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/expenses.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="d-flex">
        <div class="sidebar">
            <a href="dtms_dashboard.php">Back to Dashboard</a>
        </div>

        <!-- Main Content -->
        <div class="main-content container-fluid">
            <h1>Assign Expenses to Trips</h1>

            <!-- Trips Table -->
            <div class="trip-table">
                <h2>Pending Expenses</h2>
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
                                    <td><button class="btn btn-primary" onclick='openAssignExpenseModal(<?= json_encode($trip) ?>)'>Assign Expense</button></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="12">No trips found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Done Expenses Table -->
            <div class="done-expenses-table">
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
                            <th>Driver Expense</th>
                            <th>Co-Driver Expense</th>
                        </tr>
                    </thead>
                    <tbody id="doneExpensesTableBody">
                        <?php if ($doneExpensesResult->num_rows > 0): ?>
                            <?php while ($expense = $doneExpensesResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $expense['trip_id'] ?></td>
                                    <td><?= $expense['trip_date'] ?></td>
                                    <td><?= $expense['trip_time'] ?></td>
                                    <td><?= $expense['trip_day'] ?></td>
                                    <td><?= $expense['trip_description'] ?></td>
                                    <td><?= $expense['driver_full_name'] ?></td>
                                    <td><?= $expense['co_driver_full_name'] ?></td>
                                    <td><?= $expense['vehicle_regno'] ?></td>
                                    <td><?= $expense['from_location'] ?></td>
                                    <td><?= $expense['stops'] ?></td>
                                    <td><?= $expense['to_location'] ?></td>
                                    <td><?= $expense['driver_expense'] ?></td>
                                    <td><?= $expense['co_driver_expense'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="13">No expenses found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
        
     <!-- Assign Expense Modal -->
    <div class="modal fade" id="assignExpenseModal" tabindex="-1" aria-labelledby="assignExpenseLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignExpenseLabel">Assign Expense for Trip #<span id="modalTripId"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignExpenseForm">
                        <input type="hidden" id="tripId" name="trip_id">
                        <div class="mb-3">
                            <label for="driverExpense" class="form-label">Driver Expense</label>
                            <input type="number" class="form-control" id="driverExpense" name="driver_expense" required>
                        </div>
                        <div class="mb-3">
                            <label for="coDriverExpense" class="form-label">Co-Driver Expense</label>
                            <input type="number" class="form-control" id="coDriverExpense" name="co_driver_expense" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="assignExpense()">Assign Expense</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openAssignExpenseModal(tripData) {
            document.getElementById('modalTripId').textContent = tripData.trip_id;
            document.getElementById('tripId').value = tripData.trip_id;
            new bootstrap.Modal(document.getElementById('assignExpenseModal')).show();
        }

        function assignExpense() {
            const form = document.getElementById('assignExpenseForm');
            const formData = new FormData(form);

            fetch('expenses.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    const tripId = data.trip_id;
                    const tripRow = document.getElementById('trip-row-' + tripId);
                    tripRow.remove();
                    moveToDoneExpenses(data); // Function to update the Done Expenses table
                } else {
                    alert(data.message);
                }
            });
        }

        function moveToDoneExpenses(data) {
            const doneExpensesTableBody = document.getElementById('doneExpensesTableBody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${data.trip_id}</td>
                <td>${data.trip_date}</td>
                <td>${data.trip_time}</td>
                <td>${data.trip_day}</td>
                <td>${data.trip_description}</td>
                <td>${data.driver_full_name}</td>
                <td>${data.co_driver_full_name}</td>
                <td>${data.vehicle_regno}</td>
                <td>${data.from_location}</td>
                <td>${data.stops}</td>
                <td>${data.to_location}</td>
                <td>${data.driver_expense}</td>
                <td>${data.co_driver_expense}</td>
            `;
            doneExpensesTableBody.appendChild(newRow);
        }
    </script>
</body>
</html>
