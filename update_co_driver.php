<?php
include 'connection.php';

// Fetch co-driver information by ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the query to fetch the co-driver details
    $stmt = $conn->prepare("SELECT * FROM co_drivers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $co_driver = $result->fetch_assoc();

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $full_name = $_POST['full_name'];
    $employee_number = $_POST['employee_number'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $id = $_POST['id'];  // Hidden input for the co-driver ID

    // Update co-driver details in the database
    $stmt = $conn->prepare("UPDATE co_drivers SET full_name = ?, employee_number = ?, phone_number = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $full_name, $employee_number, $phone_number, $email, $id);

    if ($stmt->execute()) {
        echo "Co-driver updated successfully.";
    } else {
        echo "Failed to update co-driver: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the manage co-drivers page
    header("Location: add_co_driver.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Co-Driver</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/update_co_driver.css">
</head>
<body>
<div class="container">
    <h2>Update Co-Driver</h2>
    <form action="update_co_driver.php" method="POST">
        <!-- Hidden input field to hold the co-driver's ID -->
        <input type="hidden" name="id" value="<?= $co_driver['id'] ?>">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($co_driver['full_name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="employee_number">Employee Number</label>
            <input type="text" class="form-control" id="employee_number" name="employee_number" value="<?= htmlspecialchars($co_driver['employee_number']) ?>" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($co_driver['phone_number']) ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($co_driver['email']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Co-Driver</button>
    </form>
</div>

<!-- Dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
