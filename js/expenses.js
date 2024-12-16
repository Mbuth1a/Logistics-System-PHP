function openAssignExpenseModal(tripData) {
    document.getElementById('modalTripId').textContent = tripData.trip_id;
    document.getElementById('tripId').value = tripData.trip_id;
    new bootstrap.Modal(document.getElementById('assignExpenseModal')).show();
}

function assignExpense() {
    const form = document.getElementById('assignExpenseForm');
    const formData = new FormData(form);

    fetch('expenses.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            
            const tripId = data.trip_id;
            const tripRow = document.getElementById('trip-row-' + tripId);
            tripRow.remove();
            moveToDoneExpenses(data); // Function to update the Done Expenses table
        } else {
            alert(data.message);
        }
    });
}

function moveToDoneExpenses(data) {
    const doneExpensesTableBody = document.getElementById('doneExpensesTableBody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>${data.trip_id}</td>
        <td>${data.trip_date}</td>
        <td>${data.trip_time}</td>
        <td>${data.trip_day}</td>
        <td>${data.trip_description}</td>
        <td>${data.driver_full_name}</td>
        <td>${data.co_driver_full_name}</td>
        <td>${data.vehicle_regno}</td>
        <td>${data.from_location}</td>
        <td>${data.stops}</td>
        <td>${data.to_location}</td>
        <td>${data.driver_expense}</td>
        <td>${data.co_driver_expense}</td>
    `;
    doneExpensesTableBody.appendChild(newRow);
}