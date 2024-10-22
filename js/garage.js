   // Function to open the Garage Modal and set the vehicle information
   function openGarageModal(vehicleId, vehicleRegNo) {
    $('#vehicle-id').val(vehicleId); // Set the vehicle ID in the hidden input
    $('#issue-description').val(''); // Clear the issue description field
    $('#garageModal').modal('show'); // Show the modal
}

// Handle form submission for adding a vehicle to the garage
$('#garage-form').on('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    const vehicleId = $('#vehicle-id').val();
    const issueDescription = $('#issue-description').val();

    $.ajax({
        url: 'add_to_garage.php',
        type: 'POST',
        data: {
            vehicle_id: vehicleId,
            issue_description: issueDescription
        },
        success: function(response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert(res.message);
                $('#garageModal').modal('hide'); // Hide the modal on success
                // Remove the added vehicle from the vehicle list and move it to the garage list
                $(`#vehicle-${vehicleId}`).remove();
                refreshGarageList();
            } else {
                alert(res.message);
            }
        },
        error: function() {
            alert('An error occurred while adding the vehicle to the garage.');
        }
    });
});

// Function to open the Checkout Modal with the vehicle information
function checkoutVehicle(vehicleId) {
    $('#checkout-vehicle-id').val(vehicleId); // Set the vehicle ID in the hidden input
    $('#garage-expense').val(''); // Clear the garage expense field
// Clear the notes field
    $('#checkoutModal').modal('show'); // Show the modal
}

// Handle form submission for checking out a vehicle
$('#checkout-form').on('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    const vehicleId = $('#checkout-vehicle-id').val();
    const garageExpense = $('#garage-expense').val();
   

    $.ajax({
        url: 'checkout_vehicle.php',
        type: 'POST',
        data: {
            vehicle_id: vehicleId,
            garage_expense: garageExpense,
            
        },
        success: function(response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert(res.message);
                $('#checkoutModal').modal('hide'); // Hide the modal on success
                // Remove the checked-out vehicle from the garage list
                $(`#garage-row-${vehicleId}`).remove();
            } else {
                alert(res.message);
            }
        },
        error: function() {
            alert('An error occurred while checking out the vehicle.');
        }
    });
});