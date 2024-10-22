// Function to toggle sub-menu visibility
function toggleSubMenu(menuId) {
    var subMenu = document.getElementById(menuId);

    // Toggle between showing and hiding the submenu
    if (subMenu.style.display === "block") {
        subMenu.style.display = "none";
    } else {
        subMenu.style.display = "block";
    }
}

document.getElementById('drivers-link').addEventListener('click', function() {
    toggleSubMenu('drivers-sub-menu');
});

document.getElementById('codrivers-link').addEventListener('click', function() {
    toggleSubMenu('codrivers-sub-menu');
});

document.getElementById('vehicles-link').addEventListener('click', function() {
    toggleSubMenu('vehicles-sub-menu');
});