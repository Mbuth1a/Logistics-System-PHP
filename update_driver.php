<?php
include 'connection.php';

// Fetch driver information by ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM drivers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $driver = $result->fetch_assoc();

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $full_name = $_POST['full_name'];
    $employee_number = $_POST['employee_number'];
    $license_number = $_POST['license_number'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $id = $_POST['id'];  // Hidden input for the driver ID

    // Update driver details in the database
    $stmt = $conn->prepare("UPDATE drivers SET full_name = ?, employee_number = ?, license_number = ?, phone_number = ?, email = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $full_name, $employee_number, $license_number, $phone_number, $email, $id);

    if ($stmt->execute()) {
        echo "Driver updated successfully.";
    } else {
        echo "Failed to update driver: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the manage drivers page
    header("Location: add_driver.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Driver</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel = "stylesheet" href = "css/update_driver.css">
</head>
<body>
<div class="container">
    <h2>Update Driver</h2>
    <form action="update_driver.php" method="POST">
        <input type="hidden" name="id" value="<?= $driver['id'] ?>">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($driver['full_name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="employee_number">Employee Number</label>
            <input type="text" class="form-control" id="employee_number" name="employee_number" value="<?= htmlspecialchars($driver['employee_number']) ?>" required>
        </div>
        <div class="form-group">
            <label for="license_number">License Number</label>
            <input type="text" class="form-control" id="license_number" name="license_number" value="<?= htmlspecialchars($driver['license_number']) ?>" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($driver['phone_number']) ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($driver['email']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Driver</button>
    </form>
</div>

<!-- Dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
