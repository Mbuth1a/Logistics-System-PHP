<?php
include 'connection.php';

// Initialize drivers array
$products = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Capture form data
  $product_description = $_POST['product_description'];
  $stock_code = $_POST['stock_code'];
  $product = $_POST['product'];
  $unit_of_measure = $_POST['unit_of_measure'];
  $metres = $_POST['metres'];
  $weight_per_metre = $_POST['weight_per_metre'];

  // Insert data into the database
  $stmt = $conn->prepare("INSERT INTO inventorys (product_description, stock_code, product, unit_of_measure, metres, weight_per_metre) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssss", $product_description, $stock_code, $product, $unit_of_measure, $metres, $weight_per_metre);

  if ($stmt->execute()) {
      // On success, add a JavaScript script for redirection
      echo "<script type='text/javascript'>
              window.onload = function() {
                  window.location.href = 'dtms_dashboard.php'; // redirect to load_trip.php
              }
            </script>";
  } else {
      echo "Failed: " . $stmt->error; // Show error if insert fails
  }

  $stmt->close();
}

// Fetch registered inventory
$result = $conn->query("SELECT * FROM inventorys");
if ($result) {
  $inventorys = $result->fetch_all(MYSQLI_ASSOC);
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>INVENTORY - Add Product - DANCO LOGISTICS SYSTEM</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
  <link rel="stylesheet" href="css/inventory.css">
  
</head>
<body>
<div class="sidebar">
    <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>

  
  
  <div class="container-fluid mt-1 col-md-9">
    <h1 class="text-center"><b><u><i>DLMS Inventory Management</b></u></i></h1>

    <!-- Search Bar -->
    <div class="form-group mt-3" style="width: 300px; float: right;">
      <label for="search-bar">Search by Stock Code</label>
      <div class="input-group">
        <input type="text" class="form-control" id="search-bar" placeholder="Enter stock code">
        <div class="input-group-append">
          <button class="btn btn-outline-secondary" type="button" id="searchButton">
            <i class="fas fa-search"></i> Search
          </button>
        </div>
      </div>
    </div>

    <!-- Add Product Button -->
    <button id="showAddProductFormBtn" class="btn btn-primary mb-12 mt-1" data-toggle="modal" data-target="#addProductModal">
      <i class="fas fa-plus-circle"></i> Add Product
    </button>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form method="POST" action="inventory.php" >
              <div class="form-group">
                <label for="product_description">Description</label>
                <input type="text" class="form-control" id="product_description" name="product_description" required>
              </div>
              <div class="form-group">
                <label for="stock_code">Stock Code</label>
                <input type="text" class="form-control" id="stock_code" name="stock_code" required>
              </div>
              <div class="form-group">
                <label for="product">Product</label>
                <select class="form-control" id="product" name="product" required>
                  <option value="">Select a product</option>
                  <option value="HDPE">HDPE</option>
                  <option value="PPR">PPR</option>
                  <option value="DWC">DWC</option>
                  <option value="DWC">Others</option>
                </select>
              </div>
              <div class="form-group">
                <label for="unit_of_measure">Unit of Measure</label>
                <select class="form-control" id="unit_of_measure" name="unit_of_measure" required>
                  <option value="">Select unit of measure</option>
                  <option value="Rolls">Rolls</option>
                  <option value="Pieces">Pieces</option>
                  <option value="Pieces">Others</option>
                </select>
              </div>
              <div class="form-group">
                <label for="metres">Length</label>
                <input type="number" step="0.01" class="form-control" id="metres" name="metres" required>
              </div>
              <div class="form-group">
                <label for="weight_per_metre">Weight/Metre</label>
                <input type="number" step="0.01" class="form-control" id="weight_per_metre" name="weight_per_metre" required>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-check-circle"></i> Add Product
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4">
    <h3>Registered Products</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">DESCRIPTION</th>
                <th scope="col">STOCK CODE</th>
                <th scope="col">PRODUCT</th>
                <th scope="col">UNIT OF MEASURE</th>
                <th scope="col">METRES</th>
                <th scope="col">WEIGHT / METRES</th>
                <th scope="col">ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($inventorys)): ?>
                <?php foreach ($inventorys as $index => $inventory): ?>
                    <tr>
                        <th scope="row"><?= $index + 1 ?></th>
                        <td><?= htmlspecialchars($inventory['product_description']) ?></td>
                        <td><?= htmlspecialchars($inventory['stock_code']) ?></td>
                        <td><?= htmlspecialchars($inventory['product']) ?></td>
                        <td><?= htmlspecialchars($inventory['unit_of_measure']) ?></td>
                        <td><?= htmlspecialchars($inventory['metres']) ?></td>
                        <td><?= htmlspecialchars($inventory['weight_per_metre']) ?></td>
                        <td>
                            <a href="update_inventory.php?id=<?= $inventory['id'] ?>" class="btn btn-warning btn-sm">Update</a>
                            <a href="delete_inventory.php?id=<?= $inventory['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No Products Added yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


  
</body>
</html>
