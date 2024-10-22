<?php
include 'connection.php'; 

$vehicles = [];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $vehicle_id = $_POST['vehicle_id'];
    $service_provider = $_POST['service_provider'];
    $maintenance_date = $_POST['maintenance_date'];
    $inspection_date = $_POST['inspection_date'];
    $insurance_date = $_POST['insurance_date'];
    $speed_governor_date = $_POST['speed_governor_date'];
    $kenha_permit_date = $_POST['kenha_permit_date'];
    $track_solid_date = $_POST['track_solid_date'];

    // Check if we're updating an existing schedule or creating a new one
    if (isset($_POST['schedule_id']) && !empty($_POST['schedule_id'])) {
        $schedule_id = $_POST['schedule_id'];
        // Update the existing maintenance schedule
        $sql = "UPDATE maintenance_schedule SET
                    service_provider = ?, 
                    maintenance_date = ?, 
                    inspection_date = ?, 
                    insurance_date = ?, 
                    speed_governor_date = ?, 
                    kenha_permit_date = ?, 
                    track_solid_date = ?
                WHERE id = ? AND vehicle_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssiii", $service_provider, $maintenance_date, $inspection_date, $insurance_date, $speed_governor_date, $kenha_permit_date, $track_solid_date, $schedule_id, $vehicle_id);
    } else {
        // Insert a new maintenance schedule
        $sql = "INSERT INTO maintenance_schedule (vehicle_id, service_provider, maintenance_date, inspection_date, insurance_date, speed_governor_date, kenha_permit_date, track_solid_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssss", $vehicle_id, $service_provider, $maintenance_date, $inspection_date, $insurance_date, $speed_governor_date, $kenha_permit_date, $track_solid_date);
    }

    // Execute the query and check if successful
    if ($stmt->execute()) {
        header("Location: maintenance.php?success=1");
        exit();
    } else {
        header("Location: maintenance.php?error=1");
        exit();
    }
}

// Check if an ID was provided for fetching schedule data
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $schedule_id = $_GET['id'];
    $sql = "SELECT * FROM maintenance_schedule WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $schedule = $result->fetch_assoc();

    // Return the schedule data as JSON
    echo json_encode($schedule);
    exit();
}

// Fetch all vehicles from the database for display on the page
$sql = "SELECT * FROM vehicles v 
        WHERE NOT EXISTS (
            SELECT 1 FROM maintenance_schedule ms
            WHERE ms.vehicle_id = v.id
        )";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
}

// Fetch maintenance schedules from the database
$sql = "SELECT ms.*, v.vehicle_regno 
        FROM maintenance_schedule ms 
        JOIN vehicles v ON ms.vehicle_id = v.id";
$result = $conn->query($sql);
$schedules = [];

// Store schedules in an array if records are found
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Initialize variables for finding the nearest date
        $currentDate = new DateTime();
        $nearestDate = null;
        $minDaysRemaining = PHP_INT_MAX; // Initialize with a very large number

        // List of dates to check for each schedule
        $datesToCheck = [
            $row['maintenance_date'],
            $row['inspection_date'],
            $row['insurance_date'],
            $row['speed_governor_date'],
            $row['kenha_permit_date'],
            $row['track_solid_date']
        ];

        // Find the nearest expiry date among all relevant dates
        foreach ($datesToCheck as $date) {
            if ($date) { // Check if the date is not null
                $dateObject = new DateTime($date);
                $daysRemaining = $dateObject->diff($currentDate)->days;

                // Check if the date is in the future and has fewer days remaining than the current nearest date
                if ($dateObject > $currentDate && $daysRemaining < $minDaysRemaining) {
                    $nearestDate = $dateObject;
                    $minDaysRemaining = $daysRemaining;
                }
            }
        }

        // Determine the color class based on the number of days remaining until the nearest date
        if ($minDaysRemaining > 60) {
            $colorClass = 'bg-success'; // Green
        } elseif ($minDaysRemaining > 30) {
            $colorClass = 'bg-warning'; // Orange
        } else {
            $colorClass = 'bg-danger'; // Red
        }

        // Add the nearest date and color class to the schedule data
        $row['days_remaining'] = $minDaysRemaining;
        $row['color_class'] = $colorClass;
        $schedules[] = $row;
    }
} else {
    echo "No schedules found.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Schedule</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/maintenance.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <a href="dtms_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <!-- Main Content -->
        <div class="container col-md-10">
            <div class="container col-md-10">
                <h1 class="mb-4 text-center">Vehicle Maintenance Schedule</h1>
                
                <!-- Search Form -->
                <form id="searchForm" method="get" class="mb-4 col-md-12">
                    <div class="form-group">
                        <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search by vehicle registration number">
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>

                <!-- Vehicle List -->
                <h2 class="mt-4 mb-3">VEHICLES</h2>
                <div class="row" id="vehicleList">
                    <?php if (empty($vehicles)): ?>
                        <div class="col-md-12">
                            <p>No vehicles found.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <div class="col-md-4 mb-4" data-vehicle-id="<?= $vehicle['id']; ?>">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($vehicle['vehicle_regno']); ?></h5>
                                        <button class="btn btn-primary" data-toggle="modal" data-target="#scheduleModal" data-vehicle-id="<?= $vehicle['id']; ?>">ADD SCHEDULE</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <h2 class="mt-6 mb-6">SCHEDULES</h2>
               
                <div class="row">
                    <?php if (!empty($schedules)): ?>
                        <?php foreach ($schedules as $schedule): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card <?= $schedule['color_class']; ?>" id="schedule-<?= $schedule['id']; ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($schedule['vehicle_regno']); ?></h5>
                                        <p class="card-text"><strong>Service Provider:</strong> <?= htmlspecialchars($schedule['service_provider']); ?></p>
                                        <p class="card-text"><strong>Maintenance Date:</strong> <?= htmlspecialchars($schedule['maintenance_date']); ?></p>
                                        <p class="card-text"><strong>Inspection Date:</strong> <?= htmlspecialchars($schedule['inspection_date']); ?></p>
                                        <p class="card-text"><strong>Insurance Date:</strong> <?= htmlspecialchars($schedule['insurance_date']); ?></p>
                                        <p class="card-text"><strong>Speed Governor Date:</strong> <?= htmlspecialchars($schedule['speed_governor_date']); ?></p>
                                        <p class="card-text"><strong>KENHA Permit Date:</strong> <?= htmlspecialchars($schedule['kenha_permit_date']); ?></p>
                                        <p class="card-text"><strong>Trail My Truck Date:</strong> <?= htmlspecialchars($schedule['track_solid_date']); ?></p>
                                        <p class="card-text"><strong>Days Remaining:</strong> <?= htmlspecialchars($schedule['days_remaining']); ?></p>

                                        <!-- Edit and Delete Buttons -->
                                            <button class="btn btn-primary edit-button" data-toggle="modal" data-target="#scheduleModal" data-schedule-id="<?= $schedule['id']; ?>" 
                                                    data-vehicle-id="<?= $schedule['vehicle_id']; ?>" data-service-provider="<?= htmlspecialchars($schedule['service_provider']); ?>" 
                                                    data-maintenance-date="<?= htmlspecialchars($schedule['maintenance_date']); ?>" data-inspection-date="<?= htmlspecialchars($schedule['inspection_date']); ?>" 
                                                    data-insurance-date="<?= htmlspecialchars($schedule['insurance_date']); ?>" data-speed-governor-date="<?= htmlspecialchars($schedule['speed_governor_date']); ?>" 
                                                    data-kenha-permit-date="<?= htmlspecialchars($schedule['kenha_permit_date']); ?>" data-track-solid-date="<?= htmlspecialchars($schedule['track_solid_date']); ?>">Edit
                                                    
                                            </button>
                                        <form method="POST" action="delete_schedule.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                                            <input type="hidden" name="schedule_id" value="<?= $schedule['id']; ?>">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-md-12">
                            <p>No maintenance schedules found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Modal -->
    <!-- Schedule Modal -->
    <div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleModalLabel">Add Maintenance Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="scheduleForm" method="POST" action="maintenance.php?vehicle_id=0">
                        <input type="hidden" name="vehicle_id" id="vehicleId">
                        <input type="hidden" name="schedule_id" id="scheduleId">
                        <div class="form-group">
                            <label for="serviceProvider">Service Provider</label>
                            <input type="text" class="form-control" name="service_provider" id="serviceProvider" required>
                        </div>
                        <div class="form-group">
                            <label for="maintenanceDate">Maintenance Date</label>
                            <input type="date" class="form-control" name="maintenance_date" id="maintenanceDate" required>
                        </div>
                        <div class="form-group">
                            <label for="inspectionDate">Inspection Date</label>
                            <input type="date" class="form-control" name="inspection_date" id="inspectionDate" required>
                        </div>
                        <div class="form-group">
                            <label for="insuranceDate">Insurance Date</label>
                            <input type="date" class="form-control" name="insurance_date" id="insuranceDate" required>
                        </div>
                        <div class="form-group">
                            <label for="speedGovernorDate">Speed Governor Date</label>
                            <input type="date" class="form-control" name="speed_governor_date" id="speedGovernorDate" required>
                        </div>
                        <div class="form-group">
                            <label for="kenhaPermitDate">KENHA Permit Date</label>
                            <input type="date" class="form-control" name="kenha_permit_date" id="kenhaPermitDate" required>
                        </div>
                        <div class="form-group">
                            <label for="TrackSolidDate">Trail My Truck Date</label>
                            <input type="date" class="form-control" name="track_solid_date" id="trackSolidDate" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Schedule</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
         // Handling Modal Population
         $('#scheduleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var vehicleId = button.data('vehicle-id');
            var scheduleId = button.data('schedule-id');

            var modal = $(this);
            modal.find('#vehicleId').val(vehicleId);
            modal.find('#scheduleId').val(scheduleId);
            if (scheduleId) {
                // Load existing schedule data if editing
                $.ajax({
                    url: 'maintenance.php?id=' + scheduleId,
                    method: 'GET',
                    success: function(data) {
                        var schedule = JSON.parse(data);
                        modal.find('#serviceProvider').val(schedule.service_provider);
                        modal.find('#maintenanceDate').val(schedule.maintenance_date);
                        modal.find('#inspectionDate').val(schedule.inspection_date);
                        modal.find('#insuranceDate').val(schedule.insurance_date);
                        modal.find('#speedGovernorDate').val(schedule.speed_governor_date);
                        modal.find('#kenhaPermitDate').val(schedule.kenha_permit_date);
                        modal.find('#trackSolidDate').val(schedule.track_solid_date);
                        $('#scheduleForm').attr('action', 'maintenance.php?vehicle_id=' + vehicleId + '&schedule_id=' + scheduleId);
                    }
                });
            } else {
                modal.find('#serviceProvider').val('');
                modal.find('#maintenanceDate').val('');
                modal.find('#inspectionDate').val('');
                modal.find('#insuranceDate').val('');
                modal.find('#speedGovernorDate').val('');
                modal.find('#kenhaPermitDate').val('');
                modal.find('#trackSolidDate').val('');
                $('#scheduleForm').attr('action', 'maintenance.php?vehicle_id=' + vehicleId);
            }
        });
        
       // Load the schedule data into the modal when the edit button is clicked
       $('.edit-schedule').click(function() {
            var scheduleId = $(this).data('id');
            $.ajax({
                url: 'maintenance.php?id=' + scheduleId,
                method: 'GET',
                success: function(data) {
                    var schedule = JSON.parse(data);
                    $('#schedule_id').val(schedule.id);
                    $('#vehicle_id').val(schedule.vehicle_id);
                    $('#service_provider').val(schedule.service_provider);
                    $('#maintenance_date').val(schedule.maintenance_date);
                    $('#inspection_date').val(schedule.inspection_date);
                    $('#insurance_date').val(schedule.insurance_date);
                    $('#speed_governor_date').val(schedule.speed_governor_date);
                    $('#kenha_permit_date').val(schedule.kenha_permit_date);
                    $('#track_solid_date').val(schedule.track_solid_date);
                }
            });
        });

    </script>
</body>
</html>
