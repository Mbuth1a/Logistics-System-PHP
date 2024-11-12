function openAssignFuelModal(tripData) {
    document.getElementById('modalTripId').textContent = tripData.trip_id;
    document.getElementById('tripId').value = tripData.trip_id;
    new bootstrap.Modal(document.getElementById('assignFuelModal')).show();
}

function assignFuel() {
    const form = document.getElementById('assignFuelForm');
    const formData = new FormData(form);

    fetch('fuel_records.php', {
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
            moveToDoneFuel(data); // Function to update the Done Fuel table
        } else {
            alert(data.message);
        }
    });
}

function moveToDoneFuel(data) {
    const doneFuelTableBody = document.getElementById('doneFuelTableBody');
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
        <td>${data.fuel_consumed}</td>
    `;
    doneFuelTableBody.appendChild(newRow);
}