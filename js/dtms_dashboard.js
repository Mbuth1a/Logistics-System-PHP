
    // Event listener for opening the modal and setting the trip ID
    $(document).on('click', '[data-target="#endTripModal"]', function () {
        // Get the trip ID from the button's data attribute
        var tripId = $(this).data('trip-id');
        // Set the trip ID in the hidden input field in the modal
        $('#trip-id').val(tripId);
    });

    // Event listener for the End Trip form submission
    $('#submit-end-trip').on('click', function () {
        // Get the values from the form
        var tripId = $('#trip-id').val();
        var endOdometer = $('#end-odometer').val();

        // Perform an AJAX request to send data to the PHP file for processing
        $.ajax({
            url: 'end_trip.php', // The PHP file that will handle the request
            type: 'POST',
            data: {
                trip_id: tripId,
                end_odometer: endOdometer
            },
            success: function (response) {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    alert('Trip ended successfully!');
                    location.reload(); // Reload the page to refresh the ongoing trips table
                } else {
                    alert('Error: ' + jsonResponse.message);
                }
            },
            error: function () {
                // Handle error - display an error message to the user
                alert('An error occurred while ending the trip. Please try again.');
            }
        });
    });