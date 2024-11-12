<?php
// Get the raw POST data from the request body
$inputData = file_get_contents('php://input');
$tripData = json_decode($inputData, true);

if ($tripData) {
    require 'connection.php';

    $sql = "";
    if ($tripData['trip_type'] == 'Export') {
        $sql = "INSERT INTO test (trip_type, test_date, proforma_invoice_no, quantity, uom, weight_per_metre, truck_regno, destination, tonnage, customer, truck_no)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    } elseif ($tripData['trip_type'] == 'Walk-in Customers') {
        $sql = "INSERT INTO test (trip_type, test_date, item, quantity, tonnage, client_name)
                VALUES (?, ?, ?, ?, ?, ?)";
    } elseif ($tripData['trip_type'] == 'Transfer') {
        $sql = "INSERT INTO test (trip_type, test_date, customer, item, quantity, weight, vehicle, destination)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unknown trip type']);
        exit;
    }

    $stmt = $conn->prepare($sql);

    if ($tripData['trip_type'] == 'Export') {
        $stmt->bind_param("sssssssssss", $tripData['trip_type'], $tripData['test_date'], $tripData['proforma_invoice_no'], $tripData['quantity'], 
                        $tripData['uom'], $tripData['weight_per_metre'], $tripData['truck_regno'], $tripData['destination'], $tripData['tonnage'], 
                        $tripData['customer'], $tripData['truck_no']);
    } elseif ($tripData['trip_type'] == 'Walk-in Customers') {
        $stmt->bind_param("ssssss", $tripData['trip_type'], $tripData['test_date'], $tripData['item'], $tripData['quantity'], $tripData['tonnage'], 
                        $tripData['client_name']);
    } elseif ($tripData['trip_type'] == 'Transfer') {
        $stmt->bind_param("ssssssss", $tripData['trip_type'], $tripData['test_date'], $tripData['customer'], $tripData['item'], $tripData['quantity'], 
                        $tripData['weight'], $tripData['vehicle'], $tripData['destination']);
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Data inserted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error inserting data', 'error_info' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
