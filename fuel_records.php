<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fuel Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .sidebar {
            height: 100vh;
            position: fixed;
            width: 200px;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 10px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: orange;
            color: white;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
        }
        .text-red { color: red; }
        .bg-red { background-color: red; color: white; }
        .card-header {
            cursor: pointer;
            background-color: #343a40;
            color: white;
            border: none;
        }
        .card-header:hover {
            background-color: orange;
            color: white;
        }
        .btn-primary:hover {
            background-color: orange;
            border-color: orange;
        }
        .monthly-consumption {
            margin-top: 20px;
        }
        .vehicle-tab {
            cursor: pointer;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 5px;
            background-color: #f8f9fa;
        }
        .vehicle-tab:hover {
            background-color: #ffe5b4;
        }
        .collapse {
            transition: height 0.35s ease;
        }
        .vehicle-details {
            color: black;
            font-size: 1.2em;
            font-weight: bold;
        }
        .form-inline input {
            margin-right: 10px;
        }
        .month-card {
            margin-bottom: 15px;
            cursor: pointer;
        }
        .month-card:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="#" onclick="goBack()">Back to Dashboard</a>
    </div>

    <div class="content">
        <h2 class="text-center">Fuel Management</h2>

        <!-- Search Form -->
        <div class="form-inline mb-4">
            <div class="form-group">
                <label for="startDate" class="mr-2">From:</label>
                <input type="date" id="startDate" class="form-control">
            </div>
            <div class="form-group mx-sm-3">
                <label for="endDate" class="mr-2">To:</label>
                <input type="date" id="endDate" class="form-control">
            </div>
            <div class="form-group mx-sm-3">
                <label for="vehicleSelect" class="mr-2">Vehicle:</label>
                <select id="vehicleSelect" class="form-control">
                    <option value="">All Vehicles</option>
                    <!-- Dynamically populated -->
                </select>
            </div>
            <button class="btn btn-primary" onclick="searchFuelRecords()">Search</button>
        </div>

        <!-- Trips List -->
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vehicle</th>
                    <th>Date</th>
                    <th>From Location</th>
                    <th>To Location</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tripsList">
                <!-- Dynamically populated -->
            </tbody>
        </table>

        <!-- Fuel Records Toggle Card -->
        <div class="card mt-4">
            <div class="card-header" onclick="toggleFuelRecords()">
                <h5 class="card-title">FUEL RECORDS</h5>
            </div>
            <div class="card-body collapse" id="fuelRecords">
                <div id="fuelCards" class="mt-4">
                    <!-- Fuel cards will be dynamically inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Fuel Consumption -->
    <div class="modal fade" id="fuelModal" tabindex="-1" role="dialog" aria-labelledby="fuelModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fuelModalLabel">Add Fuel Consumption</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="fuelForm">
                        <input type="hidden" id="tripId">
                        <div class="form-group">
                            <label for="vehicle">Vehicle</label>
                            <input type="text" class="form-control" id="vehicle" readonly>
                        </div>
                        <div class="form-group">
                            <label for="tripDate">Date</label>
                            <input type="text" class="form-control" id="tripDate" readonly>
                        </div>
                        <div class="form-group">
                            <label for="fromLocation">From</label>
                            <input type="text" class="form-control" id="fromLocation" readonly>
                        </div>
                        <div class="form-group">
                            <label for="toLocation">To</label>
                            <input type="text" class="form-control" id="toLocation" readonly>
                        </div>
                        <div class="form-group">
                            <label for="fuelConsumed">Fuel Consumed (liters)</label>
                            <input type="number" class="form-control" id="fuelConsumed" min="0" step="0.01">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveFuel()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            fetchVehicles();
            searchFuelRecords();
            
            $('#fuelCards').on('click', '.card-header', function() {
                $(this).next('.card-body').collapse('toggle');
            });
        });
        
        function goBack() {
            window.location.href = 'dtms_dashboard.php';  // PHP-friendly URL
        }
        
        function toggleFuelRecords() {
            $('#fuelRecords').collapse('toggle');
        }
        
        function openFuelModal(tripId, vehicle, date, from, to) {
            $('#tripId').val(tripId);
            $('#vehicle').val(vehicle);
            $('#tripDate').val(date);
            $('#fromLocation').val(from);
            $('#toLocation').val(to);
            $('#fuelModal').modal('show');
        }
        
        function saveFuel() {
            let tripId = $('#tripId').val();
            let fuelConsumed = $('#fuelConsumed').val();
        
            $.ajax({
                url: "save_fuel.php",  // PHP endpoint to save fuel
                method: 'POST',
                data: {
                    trip_id: tripId,
                    fuel_consumed: fuelConsumed,
                    csrf_token: ''  // Replace with a function that generates a CSRF token in PHP
                },
                success: function(response) {
                    if (response.success) {
                        $('#fuelModal').modal('hide');
                        searchFuelRecords(); // Refresh fuel records
                    } else {
                        alert(response.error);
                    }
                }
            });
        }
        
        function updateMonthlyConsumption(vehicleRegNo, month, fuelConsumed) {
            const monthCard = $(`#monthlyConsumption-${vehicleRegNo} .month-card:nth-child(${month})`);
            let totalConsumption = 0;
    
            if (monthCard.length) {
                const monthCardBody = monthCard.find('.card-body');
                const currentMonthText = monthCardBody.text().trim();
                
                let currentConsumption = parseFloat(monthCardBody.data('consumption')) || 0;
                totalConsumption = currentConsumption + parseFloat(fuelConsumed);
                monthCardBody.data('consumption', totalConsumption.toFixed(2));
                
                monthCardBody.html(`<h6 class="card-title">${currentMonthText}</h6><p>${totalConsumption.toFixed(2)} liters</p>`);
            } else {
                // If monthCard is not found, create it
                const newMonthCard = `
                    <div class="col-md-3 col-sm-6 month-card text-center">
                        <div class="card bg-light">
                            <div class="card-body" data-consumption="${parseFloat(fuelConsumed).toFixed(2)}">
                                <h6 class="card-title">${getMonthName(month)}</h6>
                                <p>${parseFloat(fuelConsumed).toFixed(2)} liters</p>
                            </div>
                        </div>
                    </div>
                `;
                $(`#monthlyConsumption-${vehicleRegNo}`).find('.row').append(newMonthCard);
            }
        }
    
        // Function to convert month number to month name
        function getMonthName(month) {
            const months = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];
            return months[month - 1];
        }
    
        // Function to handle saving fuel
        function saveFuel() {
            let tripId = $('#tripId').val();
            let fuelConsumed = $('#fuelConsumed').val();
            
            $.ajax({
                url: "save_fuel.php",  // PHP endpoint to save fuel
                method: 'POST',
                data: {
                    trip_id: tripId,
                    fuel_consumed: fuelConsumed,
                    csrf_token: ''  // Replace with PHP CSRF function
                },
                success: function(response) {
                    if (response.success) {
                        $('#fuelModal').modal('hide');
                        searchFuelRecords(); // Refresh fuel records
                        let trip = response.trip;  // Assuming response includes the trip data
                        let tripDate = new Date(trip.date);
                        let month = tripDate.getMonth() + 1; // Months are zero-based
                        let vehicleRegNo = trip.vehicle_regno;
                        updateMonthlyConsumption(vehicleRegNo, month, fuelConsumed);
                    } else {
                        alert(response.error);
                    }
                }
            });
        }
        
        function searchFuelRecords() {
            let startDate = $('#startDate').val();
            let endDate = $('#endDate').val();
            let vehicle = $('#vehicleSelect').val();
        
            $.ajax({
                url: "fetch_fuel_records.php",  // PHP endpoint to fetch fuel records
                method: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    vehicle: vehicle
                },
                success: function(response) {
                    let tripsList = $('#tripsList');
                    tripsList.empty();
                    let fuelCards = $('#fuelCards');
                    fuelCards.empty();
                    let groupedData = {};
        
                    response.trips.forEach(trip => {
                        if (!trip.fuel_consumed) {
                            let row = `
                                <tr data-id="${trip.id}">
                                    <td>${trip.id}</td>
                                    <td>${trip.vehicle}</td>
                                    <td>${trip.date}</td>
                                    <td>${trip.from_location}</td>
                                    <td>${trip.to_location}</td>
                                    <td><button class="btn bg-red" onclick="openFuelModal(${trip.id}, '${trip.vehicle}', '${trip.date}', '${trip.from_location}', '${trip.to_location}')">Assign Fuel</button></td>
                                </tr>
                            `;
                            tripsList.append(row);
                        } else {
                            let tripDate = new Date(trip.date);
                            let month = tripDate.getMonth() + 1; // Months are zero-based
                            if (!groupedData[trip.vehicle]) {
                                groupedData[trip.vehicle] = { fuelRecords: [], monthlyData: {} };
                            }
                            if (!groupedData[trip.vehicle].monthlyData[month]) {
                                groupedData[trip.vehicle].monthlyData[month] = 0;
                            }
                            groupedData[trip.vehicle].monthlyData[month] += parseFloat(trip.fuel_consumed);
                            groupedData[trip.vehicle].fuelRecords.push(trip);
                        }
                    });
        
                    for (const [vehicle, data] of Object.entries(groupedData)) {
                        let card = `
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">${vehicle}</h5>
                                </div>
                                <div class="card-body collapse">
                                    <!-- Table for fuel records -->
                                    <div class="fuel-records-table mb-4">
                                        <h6>Fuel Records</h6>
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Date</th>
                                                    <th>From Location</th>
                                                    <th>To Location</th>
                                                    <th>Fuel Consumed (liters)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${data.fuelRecords.map(trip => `
                                                    <tr>
                                                        <td>${trip.id}</td>
                                                        <td>${trip.date}</td>
                                                        <td>${trip.from_location}</td>
                                                        <td>${trip.to_location}</td>
                                                        <td>${trip.fuel_consumed || 'N/A'}</td>
                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Monthly consumption -->
                                    <div class="monthly-consumption">
                                        <h6>Monthly Consumption</h6>
                                        <div class="row" id="monthlyConsumption-${vehicle}">
                        `;
                        const monthsList = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                        monthsList.forEach((monthName, index) => {
                            let monthNumber = index + 1;
                            let consumption = data.monthlyData[monthNumber] || 0;
                            card += `
                                <div class="col-md-3 col-sm-6 month-card text-center">
                                    <div class="card bg-light">
                                        <div class="card-body" data-consumption="${consumption}">
                                            <h6 class="card-title">${monthName}</h6>
                                            <p>${consumption.toFixed(2)} liters</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        card += `
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        fuelCards.append(card);
                    }
                }
            });
        }
        
        function fetchVehicles() {
            $.ajax({
                url: "fetch_vehicle_list.php",  // PHP endpoint to fetch vehicle list
                method: 'GET',
                success: function(response) {
                    let vehicleSelect = $('#vehicleSelect');
                    vehicleSelect.empty();
                    vehicleSelect.append('<option value="">All Vehicles</option>');
                    response.vehicles.forEach(vehicle => {
                        vehicleSelect.append(`<option value="${vehicle.id}">${vehicle.vehicle_regno}</option>`);
                    });
                }
            });
        }
    </script>
</body>
</html>
