<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1e1e1e;
            color: #f5f5f5;
        }
        .sidebar {
            background-color: #282828;
            padding: 20px;
            height: 100vh;
        }
        .sidebar a {
            color: #f5f5f5;
            text-decoration: none;
            display: block;
            margin-bottom: 20px;
            padding: 10px;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: orange;
        }
        .main-content {
            padding: 20px;
        }
        .btn-assign-expense {
            background-color: orange;
            border: none;
        }
        .btn-assign-expense:hover {
            color: darkorange;
        }
        .modal-header, .modal-footer {
            background-color: #282828;
        }
        .modal-content {
            color: #f5f5f5;
            background-color: #3e3e3e;
        }
        .assigned-expenses-list {
            margin-top: 20px;
            background-color: #282828;
            padding: 10px;
            border-radius: 5px;
        }
        .trip-table {
            margin-top: 20px;
        }
        .trip-table th, .trip-table td {
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="d-flex">
        <div class="sidebar">
            <a href="dtms_dashboard.php">Back to Dashboard</a>
        </div>

        <!-- Main Content -->
        <div class="main-content container-fluid">
            <h1>Assign Expenses to Trips</h1>

            <!-- Trips Table -->
            <div class="trip-table">
                <table class="table table-dark table-striped">
                    <thead>
                        <tr>
                            <th>Trip ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Day</th>
                            <th>Description</th>
                            <th>Driver</th>
                            <th>Co-Driver</th>
                            <th>Vehicle</th>
                            <th>From Location</th>
                            <th>Stops</th>
                            <th>To Location</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tripTableBody">
                        <!-- Trips will be dynamically inserted here -->
                    </tbody>
                </table>
            </div>

            <!-- Assigned Expenses Table -->
            <div class="expenses-table">
                <h4>Assigned Expenses</h4>
                <table class="table table-dark table-striped">
                    <thead>
                        <tr>
                            <th>Expense ID</th>
                            <th>Driver Expense</th>
                            <th>Co-Driver Expense</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="expensesTableBody">
                        <!-- Expenses will be dynamically inserted here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Assign Expense Modal -->
    <div class="modal fade" id="assignExpenseModal" tabindex="-1" aria-labelledby="assignExpenseLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignExpenseLabel">Assign Expense for Trip #<span id="modalTripId"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignExpenseForm">
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value=""> <!-- Assuming a custom function to generate CSRF token -->
                        <!-- Form fields -->
                        <div class="mb-3">
                            <label for="tripDate" class="form-label">Date</label>
                            <input type="text" class="form-control" id="tripDate" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="tripTime" class="form-label">Time</label>
                            <input type="text" class="form-control" id="tripTime" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="tripDay" class="form-label">Day</label>
                            <input type="text" class="form-control" id="tripDay" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="tripDescription" class="form-label">Description</label>
                            <input type="text" class="form-control" id="tripDescription" disabled>
                        </div>
                        <!-- Driver and Driver Expense -->
                        <div class="mb-3">
                            <label for="tripDriver" class="form-label">Driver</label>
                            <input type="text" class="form-control" id="tripDriver" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="driverExpense" class="form-label">Driver Expense</label>
                            <input type="number" class="form-control" id="driverExpense" required>
                        </div>
                        <!-- Co-Driver and Co-Driver Expense -->
                        <div class="mb-3">
                            <label for="tripCoDriver" class="form-label">Co-Driver</label>
                            <input type="text" class="form-control" id="tripCoDriver" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="coDriverExpense" class="form-label">Co-Driver Expense</label>
                            <input type="number" class="form-control" id="coDriverExpense" required>
                        </div>
                        <!-- Remaining fields -->
                        <div class="mb-3">
                            <label for="tripVehicle" class="form-label">Vehicle</label>
                            <input type="text" class="form-control" id="tripVehicle" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="tripFromLocation" class="form-label">From Location</label>
                            <input type="text" class="form-control" id="tripFromLocation" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="tripStops" class="form-label">Stops</label>
                            <input type="text" class="form-control" id="tripStops" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="tripToLocation" class="form-label">To Location</label>
                            <input type="text" class="form-control" id="tripToLocation" disabled>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-assign-expense" onclick="assignExpense()">Assign Expense</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fetch trips and expenses from the server when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            fetchTrips();
            fetchExpenses();
        });

        // Fetch trips from the server
        async function fetchTrips() {
            try {
                const response = await fetch('/api/trips.php'); // Adjusted URL for PHP API
                const trips = await response.json();
                
                // Get the IDs of trips that already have expenses
                const expensesResponse = await fetch('/api/expenses.php'); // Adjusted URL for PHP API
                const expenses = await expensesResponse.json();
                const tripIdsWithExpenses = new Set(expenses.map(expense => expense.trip));

                // Filter out trips that already have assigned expenses
                const availableTrips = trips.filter(trip => !tripIdsWithExpenses.has(trip.id));

                displayTrips(availableTrips);
            } catch (error) {
                console.error('Error fetching trips:', error);
            }
        }

        // Display the fetched trips in the table
        function displayTrips(trips) {
            const tripTableBody = document.getElementById('tripTableBody');
            tripTableBody.innerHTML = ''; // Clear previous content

            trips.forEach(trip => {
                const tripRow = document.createElement('tr');
                tripRow.innerHTML = `
                    <td>${trip.id}</td>
                    <td>${trip.date}</td>
                    <td>${trip.time}</td>
                    <td>${trip.day}</td>
                    <td>${trip.description}</td>
                    <td>${trip.driver}</td>
                    <td>${trip.co_driver}</td>
                    <td>${trip.vehicle}</td>
                    <td>${trip.from_location}</td>
                    <td>${trip.stops ? trip.stops : 'None'}</td>
                    <td><button class="btn btn-assign-expense" onclick="showAssignExpenseModal(${trip.id})">Assign Expense</button></td>
                `;
                tripTableBody.appendChild(tripRow);
            });
        }

        // Fetch expenses from the server
        async function fetchExpenses() {
            try {
                const response = await fetch('/api/expenses.php'); // Adjusted URL for PHP API
                const expenses = await response.json();
                displayExpenses(expenses);
            } catch (error) {
                console.error('Error fetching expenses:', error);
            }
        }

        // Display the fetched expenses in the table
        function displayExpenses(expenses) {
            const expensesTableBody = document.getElementById('expensesTableBody');
            expensesTableBody.innerHTML = ''; // Clear previous content

            expenses.forEach(expense => {
                const expenseRow = document.createElement('tr');
                expenseRow.innerHTML = `
                    <td>${expense.id}</td>
                    <td>${expense.driver_expense}</td>
                    <td>${expense.co_driver_expense}</td>
                    <td>${new Date(expense.created_at).toLocaleDateString()}</td>
                    <td><button class="btn btn-danger" onclick="deleteExpense(${expense.id})">Delete</button></td>
                `;
                expensesTableBody.appendChild(expenseRow);
            });
        }

        // Show Assign Expense Modal with trip details
        function showAssignExpenseModal(tripId) {
            // Fetch trip details based on tripId
            fetch(`/api/trips.php?id=${tripId}`) // Adjusted URL for PHP API
                .then(response => response.json())
                .then(trip => {
                    document.getElementById('modalTripId').textContent = tripId;
                    document.getElementById('tripDate').value = trip.date;
                    document.getElementById('tripTime').value = trip.time;
                    document.getElementById('tripDay').value = trip.day;
                    document.getElementById('tripDescription').value = trip.description;
                    document.getElementById('tripDriver').value = trip.driver;
                    document.getElementById('tripCoDriver').value = trip.co_driver;
                    document.getElementById('tripVehicle').value = trip.vehicle;
                    document.getElementById('tripFromLocation').value = trip.from_location;
                    document.getElementById('tripStops').value = trip.stops ? trip.stops : 'None';
                    document.getElementById('tripToLocation').value = trip.to_location;

                    const assignExpenseModal = new bootstrap.Modal(document.getElementById('assignExpenseModal'));
                    assignExpenseModal.show();
                })
                .catch(error => console.error('Error fetching trip details:', error));
        }

        // Assign an expense to a trip
        async function assignExpense() {
            const tripId = document.getElementById('modalTripId').textContent;
            const driverExpense = document.getElementById('driverExpense').value;
            const coDriverExpense = document.getElementById('coDriverExpense').value;

            try {
                const response = await fetch('/api/expenses.php', { // Adjusted URL for PHP API
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRFToken': getCookie('csrf_token'),
                    },
                    body: JSON.stringify({
                        trip: tripId,
                        driver_expense: driverExpense,
                        co_driver_expense: coDriverExpense,
                    }),
                });
                if (response.ok) {
                    fetchTrips(); // Refresh the trips list to exclude assigned trips
                    fetchExpenses(); // Refresh the expenses list
                    const assignExpenseModal = bootstrap.Modal.getInstance(document.getElementById('assignExpenseModal'));
                    assignExpenseModal.hide();
                } else {
                    console.error('Error assigning expense:', await response.text());
                }
            } catch (error) {
                console.error('Error assigning expense:', error);
            }
        }

        // Delete an expense
        async function deleteExpense(expenseId) {
            try {
                const response = await fetch(`/api/expenses.php?id=${expenseId}`, { // Adjusted URL for PHP API
                    method: 'DELETE',
                    headers: {
                        'X-CSRFToken': getCookie('csrf_token'),
                    },
                });
                if (response.ok) {
                    fetchExpenses(); // Refresh the expenses list
                } else {
                    console.error('Error deleting expense:', await response.text());
                }
            } catch (error) {
                console.error('Error deleting expense:', error);
            }
        }

        // Get CSRF token from cookie
        function getCookie(name) {
            let cookieValue = null;
            if (document.cookie && document.cookie !== '') {
                const cookies = document.cookie.split(';');
                for (let i = 0; i < cookies.length; i++) {
                    const cookie = cookies[i].trim();
                    if (cookie.substring(0, name.length + 1) === (name + '=')) {
                        cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                        break;
                    }
                }
            }
            return cookieValue;
        }
    </script>

</body>
</html>
