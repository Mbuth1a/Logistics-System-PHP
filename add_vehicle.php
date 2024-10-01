<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Vehicle - DANCO LTD Logistics System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/add_vehicle.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <div class="container mt-1 col-md-10">
        <h2><i class="fas fa-truck"></i> Add New Vehicle</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="addVehicleForm" method="post" action="">
            <div class="form-group">
                <label for="id_vehicle_regno"><i class="fas fa-id-card"></i> Vehicle Registration Number</label>
                <input type="text" class="form-control" id="id_vehicle_regno" name="vehicle_regno" required value="">
            </div>
            <div class="form-group">
                <label for="id_vehicle_model"><i class="fas fa-truck-monster"></i> Vehicle Model</label>
                <input type="text" class="form-control" id="id_vehicle_model" name="vehicle_model" required value="">
            </div>
            <div class="form-group">
                <label for="id_vehicle_type"><i class="fas fa-truck-pickup"></i> Vehicle Type</label>
                <input type="text" class="form-control" id="id_vehicle_type" name="vehicle_type" required value="">
            </div>
            <div class="form-group">
                <label for="id_engine_number"><i class="fas fa-cogs"></i> Engine Number</label>
                <input type="text" class="form-control" id="id_engine_number" name="engine_number" required value="">
            </div>
            <div class="form-group">
                <label for="id_capacity"><i class="fas fa-weight-hanging"></i> Capacity</label>
                <input type="text" class="form-control" id="id_capacity" name="capacity" required value="">
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane"></i> Submit</button>
        </form>
    </div>

    <script>
        
    
        document.getElementById('addVehicleForm').addEventListener('submit', function(event) {
            var vehicleRegNo = document.getElementById('id_vehicle_regno').value;
            var vehicleModel = document.getElementById('id_vehicle_model').value;
            var vehicleType = document.getElementById('id_vehicle_type').value;
            var engineNumber = document.getElementById('id_engine_number').value;
            var capacity = document.getElementById('id_capacity').value;

            if (!vehicleRegNo || !vehicleModel || !vehicleType || !engineNumber || !capacity) {
                alert('Please fill in all fields.');
                event.preventDefault(); // Prevent form submission
            }
        });
   
        // Optional JavaScript validation can be added here if needed
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
