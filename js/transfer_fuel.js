function openAssignTransferFuelModal(tripData) {
    document.getElementById('modalTripId').textContent = tripData.transfer_id; // Adjusted based on expected ID
    document.getElementById('transferId').value = tripData.transfer_id; // Adjusted here as well
    new bootstrap.Modal(document.getElementById('assignTransferFuelModal')).show();
}


function assignTransferFuel() {
    const form = document.getElementById('assignTransferFuelForm');
    const formData = new FormData(form);

    fetch('transfer_fuel.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            const transferId = data.transfer_id;
            const tripRow = document.getElementById('trip-row-' + transferId);
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
        <td>${data.transfer_id}</td>
        <td>${data.customer_name}</td>
        <td>${data.transfer_date}</td>
        <td>${data.transfer_time}</td>
        <td>${data.transfer_day}</td>
     
        <td>${data.driver}</td>
        
        <td>${data.vehicle}</td>
        
        
        <td>${data.destination}</td>
        <td>${data.fuel_consumed}</td>
    `;
    doneFuelTableBody.appendChild(newRow);
}