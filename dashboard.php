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
          <li class="nav-item">
            <a class="nav-link" href="manage_driver.php">
              <i class="fas fa-users"></i> Manage Drivers
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
          <li class="nav-item">
            <a class="nav-link" href="manage_co_driver.php">
              <i class="fas fa-users"></i> Manage Co-Drivers
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
          <li class="nav-item">
            <a class="nav-link" href="manage_vehicle.php">
              <i class="fas fa-book-open"></i> Manage Vehicles
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
      <i class="fas fa-sign-out-alt"></i> Logout
    </button>
  </div>
  <br><br><br>

  <div class="container mt-2 col-md-10">
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <i class="fas fa-user"></i> Drivers
                    <div class="text-white-50 fa-th-large">Total: </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <i class="fas fa-car"></i> Vehicles
                    <div class="text-white-50 fa-buy-n-large">Total: </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <i class="fas fa-users"></i> Co-Drivers
                    <div class="text-white-50 fa-users">Total: </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <i class="fas fa-store"></i> Inventory
                    <div class="text-white-50 fa-th-large">Total:</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add the canvas element for the bar chart -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="chart-container">
                <canvas id="myBarChart"></canvas>
            </div>
        </div>
    </div>
  </div>

  <!-- Include the external JavaScript file -->
  <script src="/path/to/static/dashboard.js"></script> <!-- Replace with your actual static path -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>

  <!-- JavaScript -->
  <script>
    // Function to toggle sub-menu visibility
    function toggleSubMenu(menuId) {
        var subMenu = document.getElementById(menuId);

        // Toggle between showing and hiding the submenu
        if (subMenu.style.display === "block") {
            subMenu.style.display = "none";
        } else {
            subMenu.style.display = "block";
        }
    }

    document.getElementById('drivers-link').addEventListener('click', function() {
        toggleSubMenu('drivers-sub-menu');
    });

    document.getElementById('codrivers-link').addEventListener('click', function() {
        toggleSubMenu('codrivers-sub-menu');
    });

    document.getElementById('vehicles-link').addEventListener('click', function() {
        toggleSubMenu('vehicles-sub-menu');
    });
  </script>
</body>
</html>
