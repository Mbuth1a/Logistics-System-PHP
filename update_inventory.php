<?php
include 'connection.php';

// Fetch product information by ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the query to fetch the product details
    $stmt = $conn->prepare("SELECT * FROM inventorys WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $product_description = $_POST['product_description'];
    $stock_code = $_POST['stock_code'];
    $product = $_POST['product'];
    $unit_of_measure = $_POST['unit_of_measure'];
    $metres = $_POST['metres'];
    $weight_per_metre = $_POST['weight_per_metre'];
    $id = $_POST['id'];  // Hidden input for the product ID

    // Update product details in the database
    $stmt = $conn->prepare("UPDATE inventorys SET product_description = ?, stock_code = ?, product = ?, unit_of_measure = ?, metres = ?, weight_per_metre = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $product_description, $stock_code, $product, $unit_of_measure, $metres, $weight_per_metre, $id);

    if ($stmt->execute()) {
        echo "Product updated successfully.";
    } else {
        echo "Failed to update product: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the inventory page
    header("Location: inventory.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Update Product</h2>
    <form action="update_inventory.php" method="POST">
        <input type="hidden" name="id" value="<?= $product['id'] ?>">
        <div class="form-group">
            <label for="product_description">Description</label>
            <input type="text" class="form-control" id="product_description" name="product_description" value="<?= htmlspecialchars($product['product_description']) ?>" required>
        </div>
        <div class="form-group">
            <label for="stock_code">Stock Code</label>
            <input type="text" class="form-control" id="stock_code" name="stock_code" value="<?= htmlspecialchars($product['stock_code']) ?>" required>
        </div>
        <div class="form-group">
            <label for="product">Product</label>
            <select class="form-control" id="product" name="product" required>
                <option value="">Select a product</option>
                <option value="HDPE" <?= $product['product'] == 'HDPE' ? 'selected' : '' ?>>HDPE</option>
                <option value="PPR" <?= $product['product'] == 'PPR' ? 'selected' : '' ?>>PPR</option>
                <option value="DWC" <?= $product['product'] == 'DWC' ? 'selected' : '' ?>>DWC</option>
            </select>
        </div>
        <div class="form-group">
            <label for="unit_of_measure">Unit of Measure</label>
            <select class="form-control" id="unit_of_measure" name="unit_of_measure" required>
                <option value="">Select unit of measure</option>
                <option value="Rolls" <?= $product['unit_of_measure'] == 'Rolls' ? 'selected' : '' ?>>Rolls</option>
                <option value="Pieces" <?= $product['unit_of_measure'] == 'Pieces' ? 'selected' : '' ?>>Pieces</option>
            </select>
        </div>
        <div class="form-group">
            <label for="metres">Length</label>
            <input type="number" step="0.01" class="form-control" id="metres" name="metres" value="<?= htmlspecialchars($product['metres']) ?>" required>
        </div>
        <div class="form-group">
            <label for="weight_per_metre">Weight/Metre</label>
            <input type="number" step="0.01" class="form-control" id="weight_per_metre" name="weight_per_metre" value="<?= htmlspecialchars($product['weight_per_metre']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
</div>
</body>
</html>
