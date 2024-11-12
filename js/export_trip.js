// JavaScript function to populate the day field based on the selected date
function populateDay() {
    var dateInput = document.getElementById('export_date').value;
    var dayInput = document.getElementById('export_day');
    
    if (dateInput) {
        var export_date = new Date(dateInput);
        var options = { weekday: 'long' }; // Display day in full format
        var export_day = new Intl.DateTimeFormat('en-US', options).format(export_date);
        dayInput.value = export_day;
    } else {
        dayInput.value = ''; // Clear the day field if no date is selected
    }
}