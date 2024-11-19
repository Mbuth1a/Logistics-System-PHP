<?php
// Include database connection
include 'connection.php';

// Initialize variables to hold counts
$totalDrivers = 0;
$totalVehicles = 0;
$totalCoDrivers = 0;
$totalInventory = 0;

// Fetch total number of drivers
$resultDrivers = $conn->query("SELECT COUNT(*) as total FROM drivers");
if ($resultDrivers) {
    $row = $resultDrivers->fetch_assoc();
    $totalDrivers = $row['total'];
}

// Fetch total number of vehicles
$resultVehicles = $conn->query("SELECT COUNT(*) as total FROM vehicles");
if ($resultVehicles) {
    $row = $resultVehicles->fetch_assoc();
    $totalVehicles = $row['total'];
}

// Fetch total number of co-drivers
$resultCoDrivers = $conn->query("SELECT COUNT(*) as total FROM co_drivers");
if ($resultCoDrivers) {
    $row = $resultCoDrivers->fetch_assoc();
    $totalCoDrivers = $row['total'];
}

// Fetch total number of inventory items
$resultInventory = $conn->query("SELECT COUNT(*) as total FROM inventorys");
if ($resultInventory) {
    $row = $resultInventory->fetch_assoc();
    $totalInventory = $row['total'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DANCO LTD Logistics System</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="css/dashboard.css"> <!-- Replace with your actual static path -->
  <style>

  </style>
</head>
<body>
  <div class="sidebar">
    <h2>DANCO LTD Logistics System</h2>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link" id="drivers-link" href="#">
          <i class="fas fa-user"></i> DRIVERS <i class="fas fa-plus plus-sign"></i>
        </a>
        <ul class="nav flex-column sub-menu" id="drivers-sub-menu">
          <li class="nav-item">
            <a class="nav-link" href="add_driver.php">
              <i class="fas fa-user-plus"></i> Add Driver
            </a>
          </li>
          
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="codrivers-link" href="#">
          <i class="fas fa-user"></i> CO-DRIVERS <i class="fas fa-plus plus-sign"></i>
        </a>
        <ul class="nav flex-column sub-menu" id="codrivers-sub-menu">
          <li class="nav-item">
            <a class="nav-link" href="add_co_driver.php">
              <i class="fas fa-user-plus"></i> Add Co-Driver
            </a>
          </li>
          
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="vehicles-link" href="#">
          <i class="fas fa-user"></i> VEHICLES <i class="fas fa-plus plus-sign"></i>
        </a>
        <ul class="nav flex-column sub-menu" id="vehicles-sub-menu">
          <li class="nav-item">
            <a class="nav-link" href="add_vehicle.php">
              <i class="fas fa-user-plus"></i> Add Vehicle
            </a>
          </li>
          
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="inventory.php">
          <i class="fas fa-store"></i> INVENTORY
        </a>
      </li>
      
    </ul>
  </div>

  <div class="navbar">
    <h2></h2>
    <button class="btn btn-danger" id="logout-btn">
      <i class="fas fa-sign-out-alt"></i>
      <a href="logout.php">Logout</a>
    </button>
  </div>
  <br><br><br>

  <div class="container mt-2 col-md-10">
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <i class="fas fa-user"></i> Drivers
                    <div class="text-white-50 fa-th-large">Total:<?php echo $totalDrivers; ?> </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <i class="fas fa-car"></i> Vehicles
                    <div class="text-white-50 fa-buy-n-large">Total:<?php echo $totalVehicles; ?> </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <i class="fas fa-users"></i> Co-Drivers
                    <div class="text-white-50 fa-users">Total: <?php echo $totalCoDrivers; ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <i class="fas fa-store"></i> Inventory
                    <div class="text-white-50 fa-th-large">Total:  <?php echo $totalInventory; ?></div>
                </div>
            </div>
        </div>
    </div>

    
  </div>
  <script src="js/dashboard.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>

  
</body>
</html>
