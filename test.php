<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Excel Data</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>
</head>
<body>

    <!-- Dropdown for selecting the trip type -->
    <div>
        <label for="tripTypeSelect">Select Trip Type:</label>
        <select id="tripTypeSelect">
            <option value="Export">Export</option>
            <option value="Walk-in Customers">Walk-in Customers</option>
            <option value="Transfer">Transfer</option>
        </select>
    </div>

    <!-- File upload button -->
    <div>
        <input type="file" id="fileInput" accept=".xlsx, .xls" />
        <button onclick="handleFileUpload()">Upload Excel File</button>
    </div>

    <!-- Display error or success message -->
    <div id="errorMessage" style="color: red; display: none;">Error: Unknown trip type or invalid file.</div>
    <div id="successMessage" style="color: green; display: none;">File processed successfully.</div>

    <script>
        let selectedTripType = '';  // Variable to store the selected trip type

        // Function to set the trip type based on dropdown selection
        document.getElementById('tripTypeSelect').addEventListener('change', function() {
            selectedTripType = this.value;
            console.log('Selected trip type:', selectedTripType);  // For debugging
        });

        // Function to handle file upload
        function handleFileUpload() {
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];

            if (!file) {
                alert('Please select a file to upload.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const data = e.target.result;
                const workbook = XLSX.read(data, { type: 'binary' });
                const sheetName = workbook.SheetNames[0];
                const sheet = workbook.Sheets[sheetName];
                const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

                // Call the processData function with the loaded data
                processData(jsonData);
            };

            reader.readAsBinaryString(file);
        }

        // Function to process the Excel data
        function processData(data) {
            // Ensure a trip type is selected
            if (!selectedTripType) {
                document.getElementById('errorMessage').style.display = 'block';
                document.getElementById('errorMessage').textContent = 'Error: Please select a valid trip type.';
                return;
            }

            let headers = data[0];
            let rows = data.slice(1);

            // Log for debugging
            console.log('Headers:', headers);
            console.log('Rows:', rows);

            rows.forEach(row => {
                let tripType = selectedTripType;  // Use the selected trip type

                // Validate row data (if any row is empty or invalid, skip it)
                if (!row || row.length < 1 || !row[0]) {
                    console.error('Invalid row data:', row);
                    return;
                }

                let tripData = {};

                // Process based on the selected trip type
                switch (tripType) {
                    case 'Export':
                        // Process export data
                        tripData = {
                            trip_type: tripType,
                            test_date: row[1],
                            proforma_invoice_no: row[2],
                            quantity: row[3],
                            uom: row[4],
                            weight_per_metre: row[5],
                            truck_regno: row[6],
                            destination: row[7],
                            tonnage: row[8],
                            customer: row[9],
                            truck_no: row[10]
                        };
                        break;
                    case 'Walk-in Customers':
                        // Process walk-in customer data
                        tripData = {
                            trip_type: tripType,
                            test_date: row[1],
                            item: row[2],
                            quantity: row[3],
                            tonnage: row[4],
                            client_name: row[5]
                        };
                        break;
                    case 'Transfer':
                        // Process transfer data
                        tripData = {
                            trip_type: tripType,
                            test_date: row[1],
                            customer: row[2],
                            item: row[3],
                            quantity: row[4],
                            weight: row[5],
                            vehicle: row[6],
                            destination: row[7]
                        };
                        break;
                    default:
                        console.error('Unknown trip type:', tripType);
                        document.getElementById('errorMessage').style.display = 'block';
                        document.getElementById('errorMessage').textContent = 'Error: Unknown trip type.';
                        return;
                }

                // Log trip data for debugging
                console.log('Processed trip data:', tripData);

                // Send the data to the server (via AJAX, for example)
                sendDataToServer(tripData);
            });

            // Show success message
            document.getElementById('successMessage').style.display = 'block';
        }

        // Function to send the processed data to the server (you can implement this as per your needs)
        function sendDataToServer(tripData) {
    // Example of sending data via an AJAX POST request
    fetch('test_data.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(tripData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Server response:', data);
    })
    .catch(error => {
        console.error('Error sending data to server:', error);
    });
}

    </script>

</body>
</html>
