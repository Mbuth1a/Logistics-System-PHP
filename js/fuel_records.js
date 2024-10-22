function openFuelModal(tripId, vehicle, tripDate, fromLocation, toLocation) {
    $('#tripId').val(tripId);
    $('#vehicle').val(vehicle);
    $('#tripDate').val(tripDate);
    $('#fromLocation').val(fromLocation);
    $('#toLocation').val(toLocation);
    $('#fuelModal').modal('show');
}

$(document).ready(function() {
    $('#saveFuelBtn').click(function() {
        const tripId = $('#tripId').val();
        const fuelConsumed = $('#fuelConsumed').val();
        const csrfToken = $('#csrfToken').val();

        $.ajax({
            url: 'save_fuel.php',
            type: 'POST',
            data: {
                tripId: tripId,
                fuelConsumed: fuelConsumed,
                csrfToken: csrfToken
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert('Fuel record saved successfully');
                    $('#fuelModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while saving the fuel record');
            }
        });
    });
});