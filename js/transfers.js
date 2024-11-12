// JavaScript function to populate the day field based on the selected date
function populateDay() {
    var dateInput = document.getElementById('transfer_date').value;
    var dayInput = document.getElementById('transfer_day');
    
    if (dateInput) {
        var transfer_date = new Date(dateInput);
        var options = { weekday: 'long' }; // Display day in full format
        var transfer_day = new Intl.DateTimeFormat('en-US', options).format(transfer_date);
        dayInput.value = transfer_day;
    } else {
        dayInput.value = ''; // Clear the day field if no date is selected
    }
}