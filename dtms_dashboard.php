<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DANCO LTD Logistics System</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<!-- Replace with the actual path to your CSS file -->
  <link rel="stylesheet"href = "css/dtms_dashboard.css">
</head>
<body>
<div class="sidebar">
    <h2>DANCO LTD <br> LOGISTICS <br> SYSTEM</h2>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link" id="trips-link" href="#" onclick="toggleSubMenu('trips-submenu', 'trips-plus')">
          <i class="fas fa-route"></i>CREATE TRIPS <i class="fas fa-plus" id="trips-plus"></i>
        </a>
        <ul class="nav flex-column sub-menu" id="trips-submenu" style="display: none;">
          <li class="nav-item">
            <a class="nav-link" href="create_trip.php">
              <i class="fas fa-map-marker-alt"></i> Local Trip
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="walk.php">
              <i class="fas fa-walking"></i> Walk In Customer
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="export_trip.php">
              <i class="fas fa-file-export"></i> Exports
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="transfers.php">
              <i class="fas fa-exchange-alt"></i> Transfers
            </a>
          </li>
          
        </ul>
      </li>
          
          
      <li class="nav-item">
        <a class="nav-link" id="load-link" href="#" onclick="toggleSubMenu('load-submenu', 'load-plus')">
          <i class="fas fa-tools"></i> LOAD TRIPS <i class="fas fa-plus" id="load-plus"></i>
        </a>
        <ul class="nav flex-column sub-menu" id="load-submenu" style="display: none;">
        <li class="nav-item">
            <a class="nav-link" href="load_trip.php">
              <i class="fas fa-map-marker-alt"></i> Local Trips
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="load_export.php">
              <i class="fas fa-file-export"></i> Export Trips
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="load_transfer.php">
              <i class="fas fa-exchange-alt"></i> Transfer Trips
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="load_walk_in.php">
              <i class="fas fa-walking"></i> Walk In Customers
            </a>
          </li>
        </ul>
      </li>    
          
          
      <li class="nav-item">
        <a class="nav-link" href="expenses.php">
          <i class="fas fa-dollar-sign"></i> EXPENSES
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="fuel-link" href="#" onclick="toggleSubMenu('fuel-submenu', 'fuel-plus')">
          <i class="fas fa-tools"></i> FUEL RECORDS <i class="fas fa-plus" id="fuel-plus"></i>
        </a>
        <ul class="nav flex-column sub-menu" id="fuel-submenu" style="display: none;">
          <li class="nav-item">
            <a class="nav-link" href="fuel_records.php">
              <i class="fas fa-cogs"></i> Local Trips
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="transfer_fuel.php">
              <i class="fas fa-wrench"></i> Transfer Trips
            </a>
          </li>
          
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link" id="maintenance-link" href="#" onclick="toggleSubMenu('maintenance-submenu', 'maintenance-plus')">
          <i class="fas fa-tools"></i> MAINTENANCE & REMINDERS <i class="fas fa-plus" id="maintenance-plus"></i>
        </a>
        <ul class="nav flex-column sub-menu" id="maintenance-submenu" style="display: none;">
          <li class="nav-item">
            <a class="nav-link" href="garage.php">
              <i class="fas fa-cogs"></i> Garage
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="maintenance.php">
              <i class="fas fa-wrench"></i> Maintenance
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="bike_parking.php">
              <i class="fas fa-biking"></i> Bike Parking
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="report_form.php">
          <i class="fas fa-book"></i> REPORTS
        </a>
      </li>
    </ul>
  </div>

  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#"></a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <button class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> 
                        <a href="logout.php" style="color: white; text-decoration: none;">Logout</a>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

  <div class="modal fade" id="endTripModal" tabindex="-1" role="dialog" aria-labelledby="endTripModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="endTripModalLabel">End Trip</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="end-trip-form">
                    <div class="form-group">
                        <label for="end-odometer">Enter End Odometer Reading</label>
                        <input type="number" class="form-control" id="end-odometer" name="end_odometer" required>
                    </div>
                    <input type="hidden" id="trip-id" name="trip_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="submit-end-trip" class="btn btn-primary">End Trip</button>
            </div>
        </div>
    </div>
  </div>
      
  <div class="modal fade" id="endTransferModal" tabindex="-1" role="dialog" aria-labelledby="endTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="endTransferModalLabel">End Trip</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="end-transfer-form">
                    <div class="form-group">
                        <label for="end-odometer">Enter End Odometer Reading</label>
                        <input type="number" class="form-control" id="end-odometer-reading" name="end_odometer" required>
                    </div>
                    <input type="hidden" id="transfer-id" name="transfer_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="submit-end-transfer" class="btn btn-primary">End Trip</button>
            </div>
        </div>
    </div>
  </div>



  <div class="container">
    <br>
    <br>
    <h2 class="alert-heading position-sticky">Ongoing Trips</h2>
    <table class="table table-dark">
      <thead>
        <tr>
          <th>#</th>
          <th>Vehicle</th>
          <th>Driver</th>
          <th>Co-driver</th>
          <th>From</th>
          <th>Stops</th>
          <th>To</th>
          <th>Date</th>
          <th>Time</th>
          <th>Day</th>
          <th>Description</th>
          <th>Estimated Distance</th>
          <th>Start Odometer</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <!-- List on going trips-->
        
        <?php include 'ongoingtrips.php'; ?> 
      </tbody>
    </table>
        
        
    <h2 class="alert-heading position-sticky">Ongoing Transfer Trips</h2>
    <table class="table table-dark">
      <thead>
        <tr>
          <th>#</th>
          <th>Customer</th>
          <th>Date</th>
          <th>Time</th>
          <th>Day</th>
          <th>Driver</th>
          <th>Vehicle</th>
          <th>To</th>
          <th>Start Odometer</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <!-- List on going trips-->
        
        <?php include 'ongoing_transfers.php'; ?> 
      </tbody>
    </table>
        
    <h2 class="alert-heading position-sticky">Ended Transfer Trips</h2>
    <table class="table table-dark">
      <thead>
        <tr>
          <th>Customer</th>
          <th>Vehicle</th>
          <th>Driver</th>
          <th>To</th>
          <th>Date</th>
          <th>Time</th>
          <th>Day</th>   
          <th>Start Odometer</th>
          <th>End Odometer</th>
          <th>Actual Distance</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
      <?php include 'ended_transfers.php'; ?> 
       <!-- List ended trips-->
      </tbody>
    </table>  

    <h2 class="alert-heading position-sticky">Ended Trips</h2>
    <table class="table table-dark">
      <thead>
        <tr>
          <th>Vehicle</th>
          <th>Driver</th>
          <th>Co-driver</th>
          <th>From</th>
          <th>Stops</th>
          <th>To</th>
          <th>Date</th>
          <th>Time</th>
          <th>Day</th>
          <th>Description</th>
          <th>Estimated Distance</th>
          <th>Start Odometer</th>
          <th>End Odometer</th>
          <th>Actual Distance</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
      <?php include 'ended_trips.php'; ?> 
       <!-- List ended trips-->
      </tbody>
    </table>
        
        
        
    
  </div>

  <!-- Notification Section -->
  <div class="container mt-4">
    <div id="notification" class="alert alert-danger" style="display: none;" role="alert">
        Some maintenance schedules have turned red. Please check immediately.
    </div>
</div>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

  
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="js/dtms_dashboard.js"></script>
<!-- Footer -->

</body>
<footer>
    <b>&copy; <?php echo date("Y"); ?> Developed by ESCO SOLUTIONS.</b>
</footer>
</html>
