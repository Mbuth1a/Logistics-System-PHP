<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Schedule</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .sidebar {
            height: 100vh;
            background-color: #333;
            padding: 15px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 220px;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
        }
        .back-button {
            background-color: orange;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            width: 20px;
            height: auto;
            cursor: pointer;
        }
        .btn-primary, .btn-secondary, .btn-danger {
            background-color: orange;
            color: #fff;
            border: none;
        }
        .btn-back:hover, .btn-primary:hover, .btn-secondary:hover, .btn-danger:hover {
            background-color: #ff9800;
        }
        .card-orange { background-color: #ff9800; }
        .card-yellow { background-color: #ffeb3b; }
        .card-green { background-color: #4caf50; }
        .card-red { background-color: #f44336; }
        .card-blue { background-color: #2196f3; }
        .vehicle-list {
            border: 1px solid #dee2e6;
            border-radius: .25rem;
            padding: 15px;
            background-color: #fff;
        }
        .vehicle-item {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .vehicle-item:last-child {
            border-bottom: none;
        }
        .delete-btn {
            background-color: #dc3545;
            color: #fff;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <button class="btn btn-back " onclick="window.location.href='dtms_dashboard.php'">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </button>

        <!-- Main Content -->
        <div class="container-fluid col-md-10">
            <div class="container-fluid col-md-11">
                <h1 class="mb-4 position-center">Vehicle Maintenance Schedule</h1>
                
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

                <!-- Maintenance Schedule Table -->
                <h2 class="mt-6 mb-6">SCHEDULES</h2>
                <div class="row">
                    <!-- Generate schedule cards-->
                </div>
                
            </div>
        </div>
    </div>

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
                    <form id="scheduleForm" method="POST" action="schedule_maintenance.php?vehicle_id=0">
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
    
    <!-- Corrected jQuery inclusion -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
      

        // Confirm delete
        function confirmDelete() {
            return confirm('Are you sure you want to delete this schedule?');
        }

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
                    url: 'get_schedule.php?id=' + scheduleId,
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
                        $('#scheduleForm').attr('action', 'schedule_maintenance.php?vehicle_id=' + vehicleId + '&schedule_id=' + scheduleId);
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
                $('#scheduleForm').attr('action', 'schedule_maintenance.php?vehicle_id=' + vehicleId);
            }
        });
    </script>
</body>
</html>
