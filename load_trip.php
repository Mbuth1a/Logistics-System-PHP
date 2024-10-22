<?php
// Include the database connection file
include 'connection.php';
session_start();

// Generate a CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize variables for error and success messages
$errorMsg = '';
$successMsg = '';

// Fetch trips from the database, excluding those already loaded in the load_trip table
$tripOptions = '';
$sql = "SELECT 
            trips.trip_id, 
            trips.trip_day, 
            trips.trip_time, 
            drivers.full_name AS driver_full_name, 
            co_drivers.full_name AS co_driver_full_name, 
            vehicles.vehicle_regno AS vehicle_regno, 
            trips.from_location, 
            trips.stops, 
            trips.to_location
        FROM trips
        LEFT JOIN drivers ON trips.driver_id = drivers.id
        LEFT JOIN co_drivers ON trips.co_driver_id = co_drivers.id
        LEFT JOIN vehicles ON trips.vehicle_id = vehicles.id
        LEFT JOIN load_trip ON trips.trip_id = load_trip.trip_id
        WHERE load_trip.trip_id IS NULL"; // Filter out trips already in load_trip table

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tripDisplay =  $row['trip_day'] . ', ' . 
                        $row['trip_time'] . ', ' . 
                        $row['driver_full_name'] . ', ' . 
                        $row['co_driver_full_name'] . ', ' . 
                        $row['vehicle_regno'] . ', ' . 
                        $row['from_location'] . ', ' . 
                        $row['stops'] . ', ' . 
                        $row['to_location'];

        $tripOptions .= '<option value="' . $row['trip_id'] . '">' . $tripDisplay . '</option>';
    }
} else {
    $tripOptions = '<option value="" disabled>No trips available</option>';
}

// Fetch products from the database
$productOptions = '';
$sql = "SELECT 
            inventorys.id,    
            inventorys.product_description, 
            inventorys.stock_code, 
            inventorys.product, 
            inventorys.unit_of_measure, 
            inventorys.metres, 
            inventorys.weight_per_metre
        FROM inventorys";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productDisplay =  $row['product_description'] . ', ' . 
                          $row['stock_code'] . ', ' .  
                          $row['product'] . ', ' . 
                          $row['unit_of_measure'] . ', ' . 
                          $row['metres'] . ', ' . 
                          $row['weight_per_metre'];
        
        $productOptions .= '<option value="' . $row['id'] . '" data-metres="' . $row['metres'] . '" data-weight-per-metre="' . $row['weight_per_metre'] . '">' . $productDisplay . '</option>';
    }
} else {
    $productOptions = '<option value="" disabled>No products available</option>';
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch trip ID and total weight from the form
    $trip_id = $_POST['trip_id'];
    $total_weight = $_POST['total_weight_of_products'];
    $products = json_decode($_POST['products_data'], true); // Decode the JSON data

    if ($trip_id && $total_weight && count($products) > 0) {
        // Insert trip load into the database
        $insertTripLoad = $conn->prepare("INSERT INTO load_trip (trip_id, total_weight) VALUES (?, ?)");
        $insertTripLoad->bind_param("id", $trip_id, $total_weight);

        if ($insertTripLoad->execute()) {
            $tripLoadId = $insertTripLoad->insert_id; // Get the ID of the newly inserted trip load

            // Insert each product associated with the trip
            $insertProduct = $conn->prepare("INSERT INTO load_trip_products (load_trip_id, product_description, quantity, total_weight) VALUES (?, ?, ?, ?)");
            foreach ($products as $product) {
                $insertProduct->bind_param("iiid", $tripLoadId, $product['id'], $product['quantity'], $product['total_weight']);
                $insertProduct->execute();
            }
            $successMsg = 'Trip loaded successfully with all selected products.';
            $insertProduct->close();
        } else {
            $errorMsg = 'Error: Unable to load the trip. Please try again.';
        }
        $insertTripLoad->close();
    } else {
        $errorMsg = 'Please select a trip and add at least one product before submitting.';
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Load Trip</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/load_trip.css">
</head>
<body>
    <div class="sidebar">
        <a href="dtms_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
    <div class="container-fluid main-content col-md-10">
        <h2 class="text-center text-orange"><i class="fas fa-box"></i> Load Trip</h2>

        <!-- JavaScript code to display success or error messages -->
        <?php if ($successMsg): ?>
            <script>
                alert("<?php echo $successMsg; ?>");
            </script>
        <?php endif; ?>

        <?php if ($errorMsg): ?>
            <script>
                alert("<?php echo $errorMsg; ?>");
            </script>
        <?php endif; ?>

        <form id="loadTripForm" method="post" action="load_trip.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-group">
                <label for="id_trip"><i class="fas fa-cube"></i> Trip</label>
                <select id="id_trip" name="trip_id" class="form-control" onchange="tripSelected()">
                    <option value="" disabled selected>Select a trip</option>
                    <?php echo $tripOptions; ?>
                </select>
            </div>

            <div id="product-section" style="display: none;">
                <h3 class="text-center text-orange">Add Products</h3>
                <div class="form-group">
                    <label for="product_search"><i class="fas fa-search"></i> Search Product</label>
                    <div class="input-group mb-2">
                        <input type="text" id="product_search" class="form-control" placeholder="Search Product by Stock Code" onkeyup="searchProduct()">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary search-button" type="button" onclick="searchProduct()"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <select id="id_product" name="products" class="form-control" onchange="productSelected()">
                        <option value="" disabled selected>Select a product</option>
                        <?php echo $productOptions; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_quantity"><i class="fas fa-calculator"></i> Quantity</label>
                    <input type="number" id="id_quantity" name="quantity" class="form-control" min="1" oninput="calculateTotalWeight()">
                </div>
                <div class="form-group">
                    <label for="id_total_weight"><i class="fas fa-weight"></i> Total Weight</label>
                    <input type="text" id="id_total_weight" name="total_weight" class="form-control" readonly>
                </div>
                <button type="button" class="btn btn-primary bg-orange" onclick="addProduct()">Add Product</button>
            </div>

            <div id="products-list" class="mt-4"></div>

            <div class="form-group">
                <label for="total_weight_of_products"><i class="fas fa-weight-hanging"></i> Total Weight of All Products</label>
                <input type="text" id="total_weight_of_products" name="total_weight_of_products" class="form-control" readonly>
                <input type="hidden" id="products_data" name="products_data">
            </div>

            <button type="submit" class="btn btn-primary bg-orange btn-block mt-4"><i class="fas fa-paper-plane"></i> Submit</button>
        </form>
    </div>

    <script src="js/load_trip.js"></script>
</body>
</html>
