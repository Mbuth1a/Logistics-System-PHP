<?php
include 'connection.php';
session_start();

// Fetch trips from the database that do not have fuel consumption recorded
$query = "
    SELECT t.trip_id, v.vehicle_regno, t.trip_date, t.from_location, t.to_location 
    FROM trips t
    JOIN vehicles v ON t.vehicle_id = t.vehicle_id
    LEFT JOIN fuel f ON t.trip_id = f.trip_id
    WHERE f.trip_id IS NULL"; // Filter trips that are not in the fuel table

$result = $conn->query($query);

// Fetch already saved fuel records from the database
$fuelQuery = "
    SELECT f.id, t.trip_id, v.vehicle_regno, f.fuel_consumed, f.created_at, t.from_location, t.to_location 
    FROM fuel f
    JOIN trips t ON f.trip_id = t.trip_id
    JOIN vehicles v ON t.vehicle_id = t.vehicle_id
    ORDER BY f.created_at DESC"; // Order by most recent fuel records

$fuelResult = $conn->query($fuelQuery);

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
                // Check if the query was successful and if there are results
                if ($result && $result->num_rows > 0) {
                    // Output data for each trip
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['trip_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['vehicle_regno']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['trip_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['from_location']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['to_location']) . "</td>";
                        echo "<td><button class='btn btn-info' onclick='openFuelModal(" . $row['trip_id'] . ", \"" . htmlspecialchars($row['vehicle_regno']) . "\", \"" . htmlspecialchars($row['trip_date']) . "\", \"" . htmlspecialchars($row['from_location']) . "\", \"" . htmlspecialchars($row['to_location']) . "\")'>Add Fuel</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No trips found</td></tr>";
                }
                ?>
            </tbody>
        </table>

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
                // Check if the fuel query was successful and if there are results
                if ($fuelResult && $fuelResult->num_rows > 0) {
                    // Output data for each fuel record
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
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Function to open the modal and populate data
        function openFuelModal(tripId, vehicle, tripDate, fromLocation, toLocation) {
            $('#tripId').val(tripId);
            $('#vehicle').val(vehicle);
            $('#tripDate').val(tripDate);
            $('#fromLocation').val(fromLocation);
            $('#toLocation').val(toLocation);
            $('#fuelModal').modal('show');
        }

        $(document).ready(function() {
            // Update the fuel record when the Save button is clicked in the modal
            $('.btn-primary').click(function() {
                // Gather the data from the modal inputs
                const tripId = $('#tripId').val();
                const fuelConsumed = $('#fuelConsumed').val();
                const csrfToken = $('#csrfToken').val();

                // Send an AJAX request to save the fuel record
                $.ajax({
                    url: 'save_fuel.php',
                    type: 'POST',
                    data: {
                        tripId: tripId,
                        fuelConsumed: fuelConsumed,
                        csrfToken: csrfToken
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('Fuel record saved successfully');
                            $('#fuelModal').modal('hide');
                            location.reload(); // Refresh the page to update the trips and fuel records list
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while saving the fuel record');
                    }
                });
            });
        });
    </script>
</body>
</html>
