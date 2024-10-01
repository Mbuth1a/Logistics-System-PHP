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
            background-color: #fff; /* Dark background color */
            color: #fff; /* Light text color */
        }
        .sidebar {
            width: 200px;
            background-color: #222; /* Darker sidebar background */
            color: #fff;
            padding: 15px;
            position: fixed;
            height: 100%;
            overflow: auto;
        }
        .sidebar a {
            color:white; /* Orange text color */
            text-decoration:solid underline;
            display: block;
            padding: 10px;
        }
        .sidebar a:hover {
            background-color: orange; /* Darker hover background */
            color: #fff; /* White text color on hover */
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
            background-color:red; /* Darker orange on hover */
        }
        .back-button {
            background-color: #f44336; /* Red background */
            color: white; /* White text color */
            border: none;
            padding: 10px;
            text-align: center;
            display: block;
            margin-bottom: 20px;
            cursor: pointer;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #ffa500; /* Orange background on hover */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <!-- Sidebar -->
        <button class="btn btn-back " onclick="window.location.href='dtms_dashboard.php'">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </button>
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
          
            ($_SERVER[''] === 'POST') {
                // $startDate = $_POST['start_date'];
                // $endDate = $_POST['end_date'];

                // // Simulated database fetch (replace this with your actual data fetch logic)
                // $reportData = [
                //     // Sample data format
                //     ['vehicle_reg_no' => 'ABC123', 'total_fuel_consumed' => 120, 'total_driver_expenses' => 200, 'total_co_driver_expenses' => 150, 'total_load_carried' => 5000, 'total_garage_expenses' => 100, 'total_distance_covered' => 1000, 'consumption_per_km' => 0.12],
                //     // Add more data as needed
                // ];

                // echo "<strong>Report Period:</strong> $startDate to $endDate";
                // echo "<table>
                //         <thead>
                //             <tr>
                //                 <th>Vehicle</th>
                //                 <th>Total Fuel Consumed (liters)</th>
                //                 <th>Total Driver Expenses</th>
                //                 <th>Total Co-Driver Expenses</th>
                //                 <th>Total Load Carried (kg)</th>
                //                 <th>Total Garage Expenses</th>
                //                 <th>Total Distance Covered (km)</th>
                //                 <th>Consumption per Kilometer (liters/km)</th>
                //             </tr>
                //         </thead>
                //         <tbody>";
                
            //     if (empty($reportData)) {
            //         echo '<tr><td colspan="8">No data found for the selected date range.</td></tr>';
            //     } else {
            //         foreach ($reportData as $report) {
            //             echo "<tr>
            //                     <td>{$report['vehicle_reg_no']}</td>
            //                     <td>{$report['total_fuel_consumed']}</td>
            //                     <td>{$report['total_driver_expenses']}</td>
            //                     <td>{$report['total_co_driver_expenses']}</td>
            //                     <td>{$report['total_load_carried']}</td>
            //                     <td>{$report['total_garage_expenses']}</td>
            //                     <td>{$report['total_distance_covered']}</td>
            //                     <td>" . number_format($report['consumption_per_km'], 2) . "</td>
            //                   </tr>";
            //         }
            //     }

            //     echo "  </tbody>
            //         </table>";
            // }
            // ?>
        </div>
    
        <button id="download-csv" style="display: none;">Download CSV</button>
    </div>
    
    <script>
        document.getElementById('report-form').addEventListener('submit', function(event) {
            event.preventDefault();
    
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            // Use AJAX to fetch the report (assuming you have a separate endpoint to handle this)
            fetch(`/generate-report/?start_date=${startDate}&end_date=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    const reportResultsDiv = document.getElementById('report-results');
                    reportResultsDiv.innerHTML = '';  // Clear previous results

                    // Display the selected date range
                    const dateRangeDiv = document.createElement('div');
                    dateRangeDiv.innerHTML = `<strong>Report Period:</strong> ${startDate} to ${endDate}`;
                    reportResultsDiv.appendChild(dateRangeDiv);
                    
                    // Handle data and table generation as shown above

                    document.getElementById('download-csv').style.display = 'inline-block';
                })
                .catch(error => console.error('Error generating report:', error));
        });

        document.getElementById('download-csv').addEventListener('click', function() {
            // Implement CSV download logic
        });
    </script>
</body>
</html>
