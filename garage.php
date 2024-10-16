<?php
// Fetch vehicles data from the database
include 'connection.php'; // Make sure this file exists and has the correct database connection settings

// Fetch all vehicles not currently in the garage
$vehicleQuery = "SELECT id, vehicle_regno, vehicle_model, vehicle_type FROM vehicles 
                 WHERE id NOT IN (SELECT vehicle_id FROM garage WHERE checked_out_at IS NULL) 
                 ORDER BY vehicle_regno ASC";
$vehicleResult = $conn->query($vehicleQuery);
$vehicles = [];

if ($vehicleResult && $vehicleResult->num_rows > 0) {
    while ($row = $vehicleResult->fetch_assoc()) {
        $vehicles[] = $row;
    }
}

// Fetch vehicles that are currently in the garage
$garageQuery = "SELECT g.vehicle_id, v.vehicle_regno, g.issue_description, g.checked_in_at 
                FROM garage g 
                JOIN vehicles v ON g.vehicle_id = v.id 
                WHERE g.checked_out_at IS NULL";
$garageResult = $conn->query($garageQuery);
$garageVehicles = [];

if ($garageResult && $garageResult->num_rows > 0) {
    while ($row = $garageResult->fetch_assoc()) {
        $garageVehicles[] = $row;
    }
}


// Fetch vehicles that have been checked out from the garage
$historyQuery = "SELECT g.vehicle_id, v.vehicle_regno, g.issue_description, g.checked_in_at, g.checked_out_at, g.garage_expense
                 FROM garage g
                 JOIN vehicles v ON g.vehicle_id = v.id
                 WHERE g.checked_out_at IS NOT NULL
                 ORDER BY g.checked_out_at DESC";
$historyResult = $conn->query($historyQuery);
$garageHistory = [];

if ($historyResult && $historyResult->num_rows > 0) {
    while ($row = $historyResult->fetch_assoc()) {
        $garageHistory[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Management</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/garage.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="sidebar">
            <a href="dtms_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
        <!-- Main Content -->
        <div class="col-md-10 content">
            <h1>Garage Management</h1>

            <!-- Search Vehicles -->
            <div class="mb-4">
                <form id="search-form">
                    <div class="form-group">
                        <input type="text" class="form-control" id="search-query" placeholder="Search for vehicles">
                        <button type="submit" class="btn btn-primary mt-2">Search</button>
                    </div>
                </form>
            </div>

            <!-- Vehicle List -->
            <h2>Vehicles</h2>
            <ul id="vehicle-list" class="list-group mb-4">
                <?php foreach ($vehicles as $vehicle): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo htmlspecialchars($vehicle['vehicle_regno']); ?> - 
                        <?php echo htmlspecialchars($vehicle['vehicle_model']); ?> - 
                        <?php echo htmlspecialchars($vehicle['vehicle_type']); ?>
                        <button class="btn btn-info btn-sm" onclick="openGarageModal(<?php echo $vehicle['id']; ?>, '<?php echo htmlspecialchars($vehicle['vehicle_regno']); ?>')">Add to Garage</button>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Vehicles in Garage -->
            <h2>Vehicles in Garage</h2>
            <table id="garage-table" class="table">
                <thead>
                    <tr>
                        <th>Vehicle Registration Number</th>
                        <th>Issue Description</th>
                        <th>Checked In At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="garage-body">
                <?php foreach ($garageVehicles as $garage): ?>
                    <tr id="garage-row-<?php echo $garage['vehicle_id']; ?>">
                        <td><?php echo htmlspecialchars($garage['vehicle_regno']); ?></td>
                        <td><?php echo htmlspecialchars($garage['issue_description']); ?></td>
                        <td><?php echo htmlspecialchars($garage['checked_in_at']); ?></td>
                        <td><button class="btn btn-success btn-sm" onclick="checkoutVehicle(<?php echo $garage['vehicle_id']; ?>)">Check Out</button></td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
                
            <!-- Garage History Section -->
            <h2>Garage History</h2>
            <table id="garage-history-table" class="table">
                <thead>
                    <tr>
                        <th>Vehicle Registration Number</th>
                        <th>Issue Description</th>
                        <th>Checked In At</th>
                        <th>Checked Out At</th>
                        <th>Garage Expense ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($garageHistory as $history): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($history['vehicle_regno']); ?></td>
                            <td><?php echo htmlspecialchars($history['issue_description']); ?></td>
                            <td><?php echo htmlspecialchars($history['checked_in_at']); ?></td>
                            <td><?php echo htmlspecialchars($history['checked_out_at']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($history['garage_expense'], 2)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

                
            <!-- Checkout Modal -->
            <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Vehicle Checkout</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="checkout-form">
                                <input type="hidden" id="checkout-vehicle-id">
                                <div class="form-group">
                                    <label for="garage-expense">Garage Expense ($)</label>
                                    <input type="number" class="form-control" id="garage-expense" step="0.01" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Checkout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Modals -->
            <div class="modal fade" id="garageModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Vehicle to Garage</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="garage-form">
                                <input type="hidden" id="vehicle-id">
                                <div class="form-group">
                                    <label for="issue-description">Issue Description</label>
                                    <textarea class="form-control" id="issue-description" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // Function to open the Garage Modal and set the vehicle information
function openGarageModal(vehicleId, vehicleRegNo) {
    $('#vehicle-id').val(vehicleId); // Set the vehicle ID in the hidden input
    $('#issue-description').val(''); // Clear the issue description field
    $('#garageModal').modal('show'); // Show the modal
}

// Handle form submission for adding a vehicle to the garage
$('#garage-form').on('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    const vehicleId = $('#vehicle-id').val();
    const issueDescription = $('#issue-description').val();

    $.ajax({
        url: 'add_to_garage.php',
        type: 'POST',
        data: {
            vehicle_id: vehicleId,
            issue_description: issueDescription
        },
        success: function(response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert(res.message);
                $('#garageModal').modal('hide'); // Hide the modal on success
                // Remove the added vehicle from the vehicle list and move it to the garage list
                $(`#vehicle-${vehicleId}`).remove();
                refreshGarageList();
            } else {
                alert(res.message);
            }
        },
        error: function() {
            alert('An error occurred while adding the vehicle to the garage.');
        }
    });
});

// Function to open the Checkout Modal with the vehicle information
function checkoutVehicle(vehicleId) {
    $('#checkout-vehicle-id').val(vehicleId); // Set the vehicle ID in the hidden input
    $('#garage-expense').val(''); // Clear the garage expense field
// Clear the notes field
    $('#checkoutModal').modal('show'); // Show the modal
}

// Handle form submission for checking out a vehicle
$('#checkout-form').on('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    const vehicleId = $('#checkout-vehicle-id').val();
    const garageExpense = $('#garage-expense').val();
   

    $.ajax({
        url: 'checkout_vehicle.php',
        type: 'POST',
        data: {
            vehicle_id: vehicleId,
            garage_expense: garageExpense,
            
        },
        success: function(response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert(res.message);
                $('#checkoutModal').modal('hide'); // Hide the modal on success
                // Remove the checked-out vehicle from the garage list
                $(`#garage-row-${vehicleId}`).remove();
            } else {
                alert(res.message);
            }
        },
        error: function() {
            alert('An error occurred while checking out the vehicle.');
        }
    });
});

</script>

</body>
</html>
