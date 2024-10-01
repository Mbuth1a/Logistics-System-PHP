<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Load Trip</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .text-orange { color: orange; }
        .bg-orange { background-color: orange; }
        .form-group { padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 15px; }
        .sidebar { position: fixed; top: 0; left: 0; width: 220px; height: 100%; background-color: #343a40; padding-top: 20px; }
        .sidebar a { color: #fff; padding: 15px; text-decoration: none; display: block; }
        .sidebar a:hover { background-color: #ff8c00; }
        .main-content { margin-left: 220px; padding: 20px; }
        .back-button { display: inline-block; padding: 10px 15px; background-color: #343a40; color: #fff; border: none; cursor: pointer; }
        .back-button:hover { background-color: #ff8c00; }
        .form-control { background-color: #f9f9f9; color: #333; }
        .search-button:hover { background-color: #ff8c00; }
        .btn-end-trip {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .btn-end-trip:hover {
            background-color: orange;
        }
        .table-container {
            margin-top: 20px;
        }
        .table {
            width: 100%; /* Make table full width */
        }
        @media (max-width: 992px) {
            .sidebar { width: 200px; }
            .main-content { margin-left: 200px; }
        }
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="#" class="back-button" onclick="goToDashboard()">Back</a>
    </div>
    <div class="container-fluid main-content col-md-10">
        <h2 class="text-center text-orange"><i class="fas fa-box"></i> Load Trip</h2>
        
        <!-- Form to Load Trip -->
        <form id="loadTripForm" method="post" action="load_trip.php">
            <input type="hidden" name="csrf_token" value="">
            <div class="form-group">
                <label for="id_trip"><i class="fas fa-cube"></i> Trip</label>
                <select id="id_trip" name="trip" class="form-control" onchange="tripSelected()">
                    <option value="" disabled selected>Select a trip</option>
                    <!-- Fetch trips-->
                </select>
            </div>
            
            <!-- Product Section -->
            <div id="product-section" style="display: none;">
                <h3 class="text-center text-orange">Add Products</h3>
                <div class="form-group">
                    <label for="product_search"><i class="fas fa-search"></i> Search Product</label>
                    <div class="input-group mb-2">
                        <input type="text" id="product_search" class="form-control" placeholder="Search Product">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary search-button" type="button" onclick="searchProduct()"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <select id="id_product" name="products" class="form-control" onchange="productSelected()">
                        <option value="" disabled selected>Search by product code</option>
                        <!-- Display Products and the search functionality -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_quantity"><i class="fas fa-calculator"></i> Quantity</label>
                    <input type="number" id="id_quantity" name="quantity" class="form-control" min="1" oninput="calculateTotalWeight()">
                </div>
                <div class="form-group">
                    <label for="id_total_weight"><i class="fas fa-weight"></i> Total Weight</label>
                    <input type="text" id="id_total_weight" name="total_weight" class="form-control" readonly>
                </div>
                <button type="button" class="btn btn-primary bg-orange" onclick="addProduct()">Add Product</button>
            </div>
            
            <div id="products-list"></div>
            
            <button type="submit" class="btn btn-primary bg-orange btn-block mt-4"><i class="fas fa-paper-plane"></i> Submit</button>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchTrips();
        });

        function fetchTrips() {
            fetch('/api/load_trips.php')
                .then(response => response.json())
                .then(trips => {
                    displayTrips(trips);
                })
                .catch(error => {
                    console.error('Error fetching trips:', error);
                });
        }

        function displayTrips(trips) {
            const tripTableBody = document.getElementById('trip-table-body');
            tripTableBody.innerHTML = '';

            trips.forEach(trip => {
                let productDetails = trip.products.length ? trip.products.map(product => `
                    <div>
                        <strong>${product.product_name}</strong> (Quantity: ${product.quantity}, Weight: ${product.total_weight} kg)
                    </div>
                `).join('') : 'No products loaded';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${trip.vehicle}</td>
                    <td>${trip.day}</td>
                    <td>${trip.date}</td>
                    <td>${trip.time}</td>
                    <td>${trip.description}</td>
                    <td>${trip.from_location}</td>
                    <td>${trip.to_location}</td>
                    <td>${trip.driver_name}</td>
                    <td>${trip.co_driver_name}</td>
                    <td>${trip.est_distance}</td>
                    <td>${productDetails}</td>
                    <td><button class="btn btn-primary btn-end-trip" onclick="endTrip(${trip.id})">End Trip</button></td>
                `;
                tripTableBody.appendChild(tr);
            });
        }

        function endTrip(tripId) {
            fetch(`/api/load_trips/${tripId}/end.php`, { method: 'POST' })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        fetchTrips(); // Refresh the list
                        alert('Trip ended successfully.');
                    } else {
                        alert('Failed to end trip.');
                    }
                })
                .catch(error => {
                    console.error('Error ending trip:', error);
                });
        }

        function searchProduct() {
            const searchQuery = document.getElementById('product_search').value.toLowerCase();
            const productSelect = document.getElementById('id_product');
            const options = productSelect.querySelectorAll('option');
            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                option.style.display = text.includes(searchQuery) ? 'block' : 'none';
            });
        }

        function productSelected() {
            calculateTotalWeight();
        }

        function addProduct() {
            const productSelect = document.getElementById('id_product');
            const quantityInput = document.getElementById('id_quantity');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const productId = selectedOption.value;
            const productText = selectedOption.textContent;
            const quantity = quantityInput.value;
            const weightPerMetre = selectedOption.getAttribute('data-weight');
            const metres = selectedOption.getAttribute('data-metres');
            const totalWeight = (quantity * weightPerMetre * metres).toFixed(2);

            if (productId && quantity > 0) {
                const productSection = document.getElementById('products-list');
                productSection.innerHTML += `<div class="form-group">
                    <input type="hidden" name="products[]" value="${productId}">
                    <p><strong>${productText}</strong> (Quantity: ${quantity}, Total Weight: ${totalWeight} kg)</p>
                </div>`;

                productSelect.value = '';
                quantityInput.value = '';
                document.getElementById('id_total_weight').value = '';
            } else {
                alert('Please select a product and enter a valid quantity.');
            }
        }

        function calculateTotalWeight() {
            const productSelect = document.getElementById('id_product');
            const quantityInput = document.getElementById('id_quantity');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const weightPerMetre = selectedOption.getAttribute('data-weight');
            const metres = selectedOption.getAttribute('data-metres');
            const quantity = quantityInput.value;

            if (quantity && weightPerMetre && metres) {
                const totalWeight = (quantity * weightPerMetre * metres).toFixed(2);
                document.getElementById('id_total_weight').value = totalWeight;
            } else {
                document.getElementById('id_total_weight').value = '';
            }
        }

        function goToDashboard() {
            window.location.href = 'dtms_dashboard.php';
        }
    </script>
</body>
</html>
