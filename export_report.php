<?php
// Include the existing database connection file
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_csv'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    // Fetch data from the database for the selected date range
    $sql = "
        SELECT 
            v.vehicle_regno,
            SUM(f.fuel_consumed) AS total_fuel_consumed,
            SUM(e.driver_expense) AS total_driver_expenses,
            SUM(e.co_driver_expense) AS total_co_driver_expenses,
            SUM(lp.total_weight) AS total_load_carried,
            SUM(g.garage_expense) AS total_garage_expenses,
            SUM(t.actual_distance) AS total_distance_covered,
            IF(SUM(t.actual_distance) > 0, SUM(f.fuel_consumed) / SUM(t.actual_distance), 0) AS consumption_per_km
        FROM trips t
        LEFT JOIN vehicles v ON t.vehicle_id = v.id
        LEFT JOIN fuel f ON t.trip_id = f.trip_id
        LEFT JOIN expenses e ON t.trip_id = e.trip_id
        LEFT JOIN load_trip lp ON t.trip_id = lp.trip_id
        LEFT JOIN garage g ON t.trip_id = g.id
        WHERE t.trip_date BETWEEN '$startDate' AND '$endDate'
        GROUP BY v.vehicle_regno
    ";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $filename = "vehicle_report_" . date('Ymd') . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        // Output the column headings
        fputcsv($output, ['Vehicle Registration Number', 'Total Fuel Consumed (liters)', 'Total Driver Expenses', 'Total Co-Driver Expenses', 'Total Load Carried (kg)', 'Total Garage Expenses', 'Total Distance Covered (km)', 'Consumption per Kilometer (liters/km)']);

        // Output the rows
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['vehicle_regno'],
                $row['total_fuel_consumed'],
                $row['total_driver_expenses'],
                $row['total_co_driver_expenses'],
                $row['total_load_carried'],
                $row['total_garage_expenses'],
                $row['total_distance_covered'],
                number_format($row['consumption_per_km'], 2)
            ]);
        }
        fclose($output);
        exit;
    }
}
