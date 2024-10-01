<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Management</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #343a40; /* Dark grey background for the whole page */
            color: black; /* Black text color */
        }
        .sidebar .btn-back {
            background-color:red;
            color: white;
            border: none;
            position: fixed; /* Fixed position for the back button */
            top: 15px;
            left: 15px; /* Ensure it stays within the sidebar */
            width: calc(100% - 30px); /* Adjust width to fit within the sidebar */
        }
        .sidebar .btn-back:hover {
            background-color: orange;
            color: black;
        }
        .content {
            background-color: #495057; /* Slightly lighter grey for the content area */
            padding: 20px;
            border-radius: 5px;
        }
        .modal-content {
            background-color: #343a40; /* Dark grey for modal content */
            color: #ffffff; /* White text color in the modal */
        }
        .form-control, .btn {
            background-color: orange; /* Orange for form controls and buttons */
            color: whitesmoke;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .table th, .table td {
            padding: 10px;
            color: white;
        }
        .table th {
            background-color: orange !important;
            font-weight: bold;
        }
        .collapsible {
            background-color: #777;
            color: white;
            cursor: pointer;
            padding: 10px;
            width: 100%;
            border: none;
            text-align: left;
            outline: none;
            font-size: 15px;
            margin-top: 10px;
        }
        .active, .collapsible:hover {
            background-color: #555;
        }
        .content-history {
            padding: 0 10px;
            display: none;
            overflow: hidden;
            background-color: #f1f1f1;
            color: black;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar">
            <button class="btn btn-back " onclick="window.location.href='dtms_dashboard.php'">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </button>
        </div>
        <!-- Main Content -->
        <div class="col-md-10 content">
            <h1>Garage Management</h1>

            <!-- Search Vehicles -->
            <div class="mb-4">
                <form id="search-form">
                    <div class="form-group">
                        <input type="text" class="form-control" id="search-query" placeholder="Search for vehicles">
                        <button type="submit" class="btn btn-primary mt-2">Search</button>
                    </div>
                </form>
            </div>

            <!-- Vehicle List -->
            <h2>Vehicles</h2>
            <ul id="vehicle-list" class="list-group mb-4"></ul>

            <!-- Vehicles in Garage -->
            <h2>Vehicles in Garage</h2>
            <table id="garage-table" class="table">
                <thead>
                    <tr>
                        <th>Vehicle Registration Number</th>
                        <th>Issue Description</th>
                        <th>Checked In At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="garage-body"></tbody>
            </table>

            <!-- Modals -->
            <div class="modal fade" id="garageModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Vehicle to Garage</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="garage-form">
                                <input type="hidden" id="vehicle-id">
                                <div class="form-group">
                                    <label for="issue-description">Issue Description</label>
                                    <textarea class="form-control" id="issue-description" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Garage History Section -->
            <div class="container col-md-12">
                <h1 class="my-4">Garage History</h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Vehicle Registration Number</th>
                            <th>Issue Description</th>
                            <th>Garage Expense</th>
                            <th>Checked In At</th>
                            <th>Checked Out At</th>
                        </tr>
                    </thead>
                    <tbody id="garage-history-body">
                        <!-- Data will be inserted here by JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Checkout Modal with Expense Field -->
            <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Check Out Vehicle</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p id="checkout-message"></p>
                            <form id="checkout-form">
                                <div class="form-group">
                                    <label for="garage-expense">Garage Expense</label>
                                    <input type="text" class="form-control" id="garage-expense" placeholder="Enter expense amount" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Confirm Check Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        let vehicles = [];
        let garages = [];
        let vehiclesInGarage = new Set();

        // Fetch data and populate vehicles and garage lists
        function fetchData() {
            $.getJSON('get_vehicle_data.php', function(data) {
                vehicles = data.vehicles;
                garages = data.garages;
                vehiclesInGarage = new Set(garages.filter(g => !g.checkedOutAt).map(g => g.vehicleId));
                renderVehicleList();
                renderGarageList();
                renderGarageHistory(); // Populate garage history section
            });
        }

        // Render available vehicles
        function renderVehicleList() {
            const vehicleList = $('#vehicle-list');
            vehicleList.empty();
            vehicles.filter(vehicle => !vehiclesInGarage.has(vehicle.id)).forEach(vehicle => {
                vehicleList.append(`
                    <li class="list-group-item">
                        ${vehicle.regno} - ${vehicle.model} - ${vehicle.type}
                        <button class="btn btn-success float-right ml-2" onclick="addToGarage(${vehicle.id})">Add to Garage</button>
                    </li>
                `);
            });
        }

        // Render active garage list (vehicles currently in the garage)
        function renderGarageList() {
            const garageBody = $('#garage-body');
            garageBody.empty();
            garages.filter(g => !g.checkedOutAt).forEach(garage => {
                const vehicle = vehicles.find(v => v.id === garage.vehicleId);
                garageBody.append(`
                    <tr>
                        <td>${vehicle.regno}</td>
                        <td>${garage.issue}</td>
                        <td>${new Date(garage.checkedInAt).toLocaleString()}</td>
                        <td>
                            <button class="btn btn-danger" onclick="showCheckoutModal(${garage.id})">Check Out</button>
                        </td>
                    </tr>
                `);
            });
        }

        // Collapsible history section logic
        $(document).on('click', '.collapsible', function() {
            $(this).toggleClass('active');
            $(this).next('.content-history').slideToggle();
        });

        $(document).ready(function() {
            // Fetch and display garage history
            $.getJSON('get_garage_history.php', function(data) {
                const historyBody = $('#garage-history-body');
                if (data.length > 0) {
                    data.forEach(record => {
                        historyBody.append(`
                            <tr>
                                <td>${record.vehicle__vehicle_regno}</td>
                                <td>${record.issue_description}</td>
                                <td>${record.garage_expense}</td>
                                <td>${new Date(record.checked_in_at).toLocaleString()}</td>
                                <td>${new Date(record.checked_out_at).toLocaleString()}</td>
                            </tr>
                        `);
                    });
                } else {
                    historyBody.append('<tr><td colspan="5">No vehicles with a garage expense have been checked out yet.</td></tr>');
                }
            });
        });

        // Handle search form submission
        $('#search-form').on('submit', function(event) {
            event.preventDefault();
            const query = $('#search-query').val().toLowerCase();
            const filteredVehicles = vehicles.filter(vehicle => 
                !vehiclesInGarage.has(vehicle.id) && (
                vehicle.regno.toLowerCase().includes(query) ||
                vehicle.model.toLowerCase().includes(query) ||
                vehicle.type.toLowerCase().includes(query)
            ));
            const vehicleList = $('#vehicle-list');
            vehicleList.empty();
            filteredVehicles.forEach(vehicle => {
                vehicleList.append(`
                    <li class="list-group-item">
                        ${vehicle.regno} - ${vehicle.model} - ${vehicle.type}
                        <button class="btn btn-success float-right ml-2" onclick="addToGarage(${vehicle.id})">Add to Garage</button>
                    </li>
                `);
            });
        });

        // Show modal to add vehicle to garage
        window.addToGarage = function(vehicleId) {
            $('#vehicle-id').val(vehicleId);
            $('#garageModal').modal('show');
        };

        // Save vehicle to garage
        $('#garage-form').on('submit', function(event) {
            event.preventDefault();
            const vehicleId = $('#vehicle-id').val();
            const issueDescription = $('#issue-description').val();
            $.post('add_to_garage.php', {
                vehicle_id: vehicleId,
                issue_description: issueDescription
            }, function(response) {
                if (response.success) {
                    fetchData(); // Refresh data after adding to garage
                    $('#garageModal').modal('hide');
                } else {
                    alert('Error adding vehicle to garage.');
                }
            }).fail(function() {
                alert('Error adding vehicle to garage.');
            });
        });

        // Show modal to check out vehicle and add garage expense
        window.showCheckoutModal = function(garageId) {
            const garage = garages.find(g => g.id === garageId);
            const vehicle = vehicles.find(v => v.id === garage.vehicleId);
            $('#checkout-message').text(`Are you sure you want to check out the vehicle ${vehicle.regno}?`);
            $('#checkout-form').off('submit').on('submit', function(event) {
                event.preventDefault();
                const expense = $('#garage-expense').val();
                checkoutVehicle(garageId, expense);
            });
            $('#checkoutModal').modal('show');
        };

        // Check out vehicle with expense
        function checkoutVehicle(garageId, expense) {
            $.post('checkout_vehicle.php', {
                garage_id: garageId,
                garage_expense: expense
            }, function(response) {
                if (response.success) {
                    fetchData(); // Refresh the data to reflect the updated status
                    $('#checkoutModal').modal('hide');
                } else {
                    alert('Error checking out vehicle.');
                }
            }).fail(function() {
                alert('Error checking out vehicle.');
            });
        }

        // Initial data fetch
        fetchData();
    });
</script>

</body>
</html>
