<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BIKE PARKING MANAGER</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #333;
      color: #ff9800;
    }
    .sidebar {
      width: 250px;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      background-color: #333;
      padding-top: 20px;
    }
    .sidebar h2 {
      color: #ff9800;
      padding-left: 20px;
    }
    .sidebar a {
      padding: 10px 20px;
      font-size: 20px;
      display: block;
      color: #fff;
      transition: color 0.3s ease;
    }
    .sidebar a:hover {
      color: #ff9800;
    }
    .container {
      margin-left: 300px;
      padding: 20px;
    }
    .card {
      margin-bottom: 20px;
    }
    .card-red {
      background-color: #ff4d4d;
      color: white;
    }
    .card-orange {
      background-color: #ffcc00;
      color: white;
    }
    .card-green {
      background-color: #4caf50;
      color: white;
    }
    .delete-btn {
      background-color: red;
      border: none;
      color: white;
      cursor: pointer;
      padding: 10px 15px;
      font-size: 16px;
      border-radius: 5px;
    }
    .delete-btn:hover {
      background-color: darkred;
      color: #ff9800;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>DLMS</h2>
    <a href="#" class="back-button" onclick="goToDashboard()">Back</a>
  </div>

  <div class="container-fluid col-md-10">
    <h1 class="text-light">Bike Parking Manager</h1>

    <!-- Form to add new bike -->
    <form id="bike-form" method="POST" class="mb-4">
      <div class="form-group">
        <label for="name" class="text-light">Bike Registration</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Enter bike registration number" required>
      </div>
      <div class="form-group">
        <label for="expiry_date" class="text-light">Expiry Date</label>
        <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
      </div>
      <button type="submit" class="btn btn-primary">Add Bike</button>
    </form>

    <!-- List of bikes with color-coded expiry dates -->
    <div id="bike-list">
      <?php
      // Fetch bikes from database and display them here
      // Assume $bikes is an array of bike data fetched from the database
      $bikes = []; // Replace with your actual data fetching logic
      foreach ($bikes as $bike) {
          $colorClass = ''; // Determine the color class based on expiry
          echo "<div class='card $colorClass p-3' id='bike-{$bike['id']}'>
                  <h4>{$bike['name']}</h4>
                  <p>Expiry Date: {$bike['expiry_date']}</p>
                  <p>Days Remaining: {$bike['days_remaining']} days</p>
                  <form method='POST' action='delete_bike.php?id={$bike['id']}' style='display:inline;' onsubmit='return confirmDelete();'>
                    <button type='submit' class='delete-btn'>Delete</button>
                  </form>
                </div>";
      }
      if (empty($bikes)) {
          echo "<p>No Bike added yet.</p>";
      }
      ?>
    </div>
  </div>

  <script>
    // Function to ask for delete confirmation
    function confirmDelete() {
      return confirm('Are you sure you want to delete this bike?');
    }
    function goToDashboard() {
      window.location.href = "dtms_dashboard.php"; // Adjust URL as necessary
    }
  </script>
</body>
</html>
