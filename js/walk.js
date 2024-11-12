// JavaScript function to populate the day field based on the selected date
function populateDay() {
    var dateInput = document.getElementById('walk_trip_date').value;
    var dayInput = document.getElementById('walk_trip_day');
    
    if (dateInput) {
        var walk_trip_date = new Date(dateInput);
        var options = { weekday: 'long' }; // Display day in full format
        var walk_trip_day = new Intl.DateTimeFormat('en-US', options).format(walk_trip_date);
        dayInput.value = walk_trip_day;
    } else {
        dayInput.value = ''; // Clear the day field if no date is selected
    }
}