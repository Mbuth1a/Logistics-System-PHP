// JavaScript function to populate the day field based on the selected date
function populateDay() {
    var dateInput = document.getElementById('date').value;
    var dayInput = document.getElementById('day');
    
    if (dateInput) {
        var date = new Date(dateInput);
        var options = { weekday: 'long' }; // Display day in full format
        var day = new Intl.DateTimeFormat('en-US', options).format(date);
        dayInput.value = day;
    } else {
        dayInput.value = ''; // Clear the day field if no date is selected
    }
}