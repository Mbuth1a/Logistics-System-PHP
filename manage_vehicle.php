<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vehicles - DANCO LTD Logistics System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="manage_vehicle.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="manage_vehicle.js"></script>
    <link rel="stylesheet" href="css/manage_vehicle.css">
    
</head>
<body>
<div class="sidebar">
    <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>

    <div class="container-fluid mt-2 col-md-9">
        <h2><i class="fas fa-truck"></i> Vehicles</h2>
        <div class="table-container mt-2">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Vehicle REG-NO</th>
                        <th scope="col">Vehicle Model</th>
                        <th scope="col">Vehicle Type</th>
                        <th scope="col">Engine Number</th>
                        <th scope="col">Capacity</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody id="vehicleTableBody">
                    <!-- Fetch vehicle details-->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modals for Edit and Delete -->
    <div class="modal fade" id="editVehicleModal" tabindex="-1" aria-labelledby="editVehicleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVehicleModalLabel">Edit Vehicle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editVehicleForm">
                        <div class="form-group">
                            <label for="edit_vehicle_regno">Vehicle REG-NO</label>
                            <input type="text" class="form-control" id="edit_vehicle_regno" name="vehicle_regno" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_vehicle_model">Vehicle Model</label>
                            <input type="text" class="form-control" id="edit_vehicle_model" name="vehicle_model" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_vehicle_type">Vehicle Type</label>
                            <input type="text" class="form-control" id="edit_vehicle_type" name="vehicle_type" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_engine_number">Engine Number</label>
                            <input type="text" class="form-control" id="edit_engine_number" name="engine_number" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_capacity">Capacity</label>
                            <input type="text" class="form-control" id="edit_capacity" name="capacity" required>
                        </div>
                        <button type="submit" class="btn btn-orange">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteVehicleModal" tabindex="-1" aria-labelledby="deleteVehicleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteVehicleModalLabel">Delete Vehicle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this vehicle?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
                </div>
            </div>
        </div>
    </div>

    
    
</body>
</html>
