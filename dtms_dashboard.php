<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DANCO LTD Logistics System</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="/path/to/your/css/dtms_dashboard.css"> <!-- Replace with the actual path to your CSS file -->
  <style>
    .body {
      background-color: #333;
      color: #fff;
    }
    .nav-link {
      transition: transform 0.2s ease;
      color: #fff;
      font-size: 20px;
      padding: 10px 20px;
    }
    .nav-link:hover {
      transform: translateX(10px);
      color: #ff9800;
    }
    .sidebar {
      width: 300px;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      padding-top: 20px;
      background-color: #333;
    }
    .sidebar h2 {
      color: #ff9800;
      font-size: 40px;
      padding-left: 20px;
    }
    .container {
      padding: 20px;
      margin-left: 300px;
      max-width: calc(100% - 320px);
    }
    .row {
      margin-right: 0;
      margin-left: 0;
    }
    .col-lg-3, .col-md-6 {
      padding: 15px;
    }
    .card {
      background-color: #6c757d;
    }
    .card-body {
      color: #fff;
    }
    .card .text-white-50 {
      color: rgba(255, 255, 255, 0.5);
    }
    .sub-menu {
      display: none;
      margin-left: 0;
      padding-left: 0;
    }
    .nav-item:hover .sub-menu {
      display: block;
    }
    .sub-menu .nav-link {
      font-size: 18px;
      padding-left: 40px;
    }
    .nav-item .fa-plus {
      float: right;
      margin-left: 10px;
    }
    .nav-item.active .fa-plus {
      transform: rotate(60deg);
    }
    .navbar {
      margin-left: 300px;
      background-color: #333;
      padding-top: 5px;
      padding-bottom: 5px;
      height: 60px;
    }
    .navbar .navbar-nav .nav-link {
      color: #fff;
    }
    .navbar .navbar-nav .nav-link:hover {
      color: #ff9800;
    }
    .logout-button {
      color: #ff9800;
      background-color: transparent;
      border: 1px solid #ff9800;
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    .logout-button:hover {
      color: #fff;
      background-color: #ff9800;
    }
    .table thead th {
      color: #ff9800;
    }
    .table tbody td {
      color: #fff;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>DANCO LTD <br> LOGISTICS <br> SYSTEM</h2>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link" id="trips-link" href="#">
          <i class="fas fa-route"></i> TRIPS <i class="fas fa-plus" id="trips-plus"></i>
        </a>
        <ul class="nav flex-column sub-menu" id="trips-submenu">
          <li class="nav-item">
            <a class="nav-link" href="create_trip.php">
              <i class="fas fa-plus"></i> Create Trip
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="load_trip.php">
              <i class="fas fa-tasks"></i> Load Trip
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
        <a class="nav-link" href="fuel_records.php">
          <i class="fas fa-gas-pump"></i> FUEL
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="maintenance-link" href="#">
          <i class="fas fa-tools"></i> MAINTENANCE & REMINDERS <i class="fas fa-plus" id="maintenance-plus"></i>
        </a>
        <ul class="nav flex-column sub-menu" id="maintenance-submenu">
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
        <li class="nav-item">
          <a class="nav-link" href="report_form.php">
            <i class="fas fa-book"></i> REPORTS
          </a>
        </li>
      </li>
    </ul>
  </div>

  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="navbar-brand" href="#"></a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <button class="btn btn-danger" onclick="logoutUser()">
              <i class="fas fa-sign-out-alt"></i> Logout
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
          <button type="button" class="btn btn-primary" id="submit-end-trip">End Trip</button>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <h2 class="alert-heading position-sticky">Ongoing Trips</h2>
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
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <!-- List on going trips-->
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td> Km</td>
          <td> Km</td>
          <td></td>
          <td>
            <button class="btn btn-danger delete-trip-btn" data-trip-id="">Delete</button>
            <button class="btn btn-danger end-trip-btn" data-trip-id="">End Trip</button>
          </td>
        </tr>
        
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
          <th>Time Ended</th>
        </tr>
      </thead>
      <tbody>
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
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const redCards = document.querySelectorAll('.card-red');
        if (redCards.length > 0) {
            const notification = document.getElementById('notification');
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 5000);
        }
    });

    document.getElementById('trips-link').addEventListener('click', function() {
      var submenu = document.getElementById('trips-submenu');
      var plusIcon = document.getElementById('trips-plus');
      submenu.style.display = (submenu.style.display === 'block') ? 'none' : 'block';
      plusIcon.style.transform = (submenu.style.display === 'block') ? 'rotate(60deg)' : 'rotate(0deg)';
    });

    document.getElementById('maintenance-link').addEventListener('click', function() {
      var submenu = document.getElementById('maintenance-submenu');
      var plusIcon = document.getElementById('maintenance-plus');
      submenu.style.display = (submenu.style.display === 'block') ? 'none' : 'block';
      plusIcon.style.transform = (submenu.style.display === 'block') ? 'rotate(60deg)' : 'rotate(0deg)';
    });

    document.querySelectorAll('.delete-trip-btn').forEach(function(button) {
      button.addEventListener('click', function() {
        const tripId = this.dataset.tripId;
        if (confirm('Are you sure you want to delete this trip? This action is irreversible.')) {
          fetch(`/delete-trip.php`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRFToken': ''  // Use a proper PHP function for CSRF token if needed
            },
            body: JSON.stringify({ trip_id: tripId })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert('Trip deleted successfully.');
              window.location.reload();
            } else {
              alert('Failed to delete the trip: ' + data.error);
            }
          })
          .catch(error => console.error('Error:', error));
        }
      });
    });

    function logoutUser() {
      window.location.href = "logout.php";  // Replace with the correct logout URL in your PHP app
    }
    
    // Handle trip end modal submission
    document.querySelectorAll('.end-trip-btn').forEach(function(button) {
      button.addEventListener('click', function() {
        const tripId = this.dataset.tripId;
        document.getElementById('trip-id').value = tripId;
        $('#endTripModal').modal('show');
      });
    });

    document.getElementById('submit-end-trip').addEventListener('click', function() {
      const endOdometer = document.getElementById('end-odometer').value;
      const tripId = document.getElementById('trip-id').value;

      if (!endOdometer || isNaN(endOdometer)) {
        alert('Please enter a valid End Odometer value.');
        return;
      }

      if (confirm('Are you sure you want to end this trip?')) {
        fetch(`/end-trip.php`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRFToken': ''  // Replace with a proper CSRF token method for PHP
          },
          body: JSON.stringify({ end_odometer: endOdometer, trip_id: tripId })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Trip ended successfully.');
            window.location.reload();
          } else {
            alert('Failed to end the trip: ' + data.error);
          }
        })
        .catch(error => console.error('Error:', error));
      }
    });
  </script>
</body>
</html>
