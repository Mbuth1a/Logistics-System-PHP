<?php
// Start the session to keep track of form submission
session_start();

// Include the existing database connection file
include('connection.php');

$reportData = [];
$startDate = '';
$endDate = '';

// Clear session data on page load to ensure the report does not persist after reload
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    unset($_SESSION['report_generated']);
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['report_generated'] = true;
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
        while ($row = $result->fetch_assoc()) {
            $reportData[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Report Generator</title>
    <style>
        body {
            display: flex;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #222; /* Dark background color */
            color: #fff; /* Light text color */
        }
        .sidebar {
            width: 200px;
            background-color: #333; /* Darker sidebar background */
            color: #fff;
            padding: 15px;
            position: fixed;
            height: 100%;
            overflow: auto;
        }
        .sidebar button {
            background-color: #f44336; /* Red background */
            color: white; /* White text color */
            border: none;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            width: 100%;
        }
        .sidebar button:hover {
            background-color: #ffa500; /* Orange background on hover */
        }
        .main-content {
            margin-left: 220px;
            padding: 15px;
            flex: 1;
            background-color: #3f4f4f; /* Slightly lighter dark background */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            color: #fff; /* White text color for table */
        }
        table, th, td {
            border: 1px solid #666; /* Darker border color */
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #555; /* Dark background for headers */
        }
        button {
            margin-top: 20px;
            background-color: #ffa500; /* Orange background */
            color: #fff; /* White text color */
            border: none;
            padding: 10px;
            cursor: pointer;
        }
        button:hover {
            background-color: red; /* Darker orange on hover */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <button onclick="window.location.href='dtms_dashboard.php'">Back to Dashboard</button>
    </div>
    <div class="main-content">
        <h1>Generate Vehicle Report</h1>
        <form id="report-form" method="POST" action="">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
            <br><br>
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required>
            <br><br>
            <button type="submit">Generate Report</button>
        </form>

        <h2>Report:</h2>
        <div id="report-results">
            <?php
            if (isset($_SESSION['report_generated']) && !empty($reportData)) {
                echo "<strong>Report Period:</strong> $startDate to $endDate";
                echo "<table>
                        <thead>
                            <tr>
                                <th>Vehicle Registration Number</th>
                                <th>Total Fuel Consumed (liters)</th>
                                <th>Total Driver Expenses</th>
                                <th>Total Co-Driver Expenses</th>
                                <th>Total Load Carried (kg)</th>
                                <th>Total Garage Expenses</th>
                                <th>Total Distance Covered (km)</th>
                                <th>Consumption per Kilometer (liters/km)</th>
                            </tr>
                        </thead>
                        <tbody>";

                foreach ($reportData as $report) {
                    echo "<tr>
                            <td>{$report['vehicle_regno']}</td>
                            <td>{$report['total_fuel_consumed']}</td>
                            <td>{$report['total_driver_expenses']}</td>
                            <td>{$report['total_co_driver_expenses']}</td>
                            <td>{$report['total_load_carried']}</td>
                            <td>{$report['total_garage_expenses']}</td>
                            <td>{$report['total_distance_covered']}</td>
                            <td>" . number_format($report['consumption_per_km'], 2) . "</td>
                          </tr>";
                }

                echo "</tbody></table>";
                echo "<form method='POST' action='export_report.php' style='display: inline;'>
                        <input type='hidden' name='start_date' value='$startDate'>
                        <input type='hidden' name='end_date' value='$endDate'>
                        <button type='submit' name='export_csv'>Export Report as CSV</button>
                      </form>";
            } elseif (isset($_SESSION['report_generated'])) {
                echo '<p>No data found for the selected date range.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
