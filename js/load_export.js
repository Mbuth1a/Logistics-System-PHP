let products = [];

function prepareProductData() {
    document.getElementById('products_data').value = JSON.stringify(products);
}

let totalWeightOfProducts = 0;

function tripSelected() {
    const tripSelect = document.getElementById('id_trip');
    const productSection = document.getElementById('product-section');
    
    productSection.style.display = tripSelect.value ? 'block' : 'none';
}

function searchProduct() {
    const input = document.getElementById('product_search').value.toLowerCase();
    const productSelect = document.getElementById('id_product');
    const options = productSelect.getElementsByTagName('option');

    for (let i = 0; i < options.length; i++) {
        const optionText = options[i].text.toLowerCase();

        if (optionText.includes(input)) {
            options[i].style.display = '';
        } else {
            options[i].style.display = 'none';
        }
    }
}

function productSelected() {
    calculateTotalWeight();
}

function calculateTotalWeight() {
    const productSelect = document.getElementById('id_product');
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    const quantityInput = document.getElementById('id_quantity');
    const totalWeightInput = document.getElementById('id_total_weight');

    const weightPerMetre = parseFloat(selectedOption.dataset.weightPerMetre);
    const metres = parseFloat(selectedOption.dataset.metres);
    const quantity = parseInt(quantityInput.value) || 0;

    const totalWeight = weightPerMetre * metres * quantity;
    totalWeightInput.value = totalWeight.toFixed(2);
}

function addProduct() {
    const productSelect = document.getElementById('id_product');
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    const quantityInput = document.getElementById('id_quantity');
    const totalWeightInput = document.getElementById('id_total_weight');

    const productId = selectedOption.value;
    const productDescription = selectedOption.text;
    const quantity = parseInt(quantityInput.value) || 0;
    const totalWeight = parseFloat(totalWeightInput.value) || 0;

    const existingProduct = products.find(product => product.id === productId);
    if (existingProduct) {
        existingProduct.quantity += quantity;
        existingProduct.total_weight += totalWeight;
    } else {
        products.push({
            id: productId,
            description: productDescription,
            quantity: quantity,
            total_weight: totalWeight
        });
    }

    totalWeightOfProducts += totalWeight;

    document.getElementById('total_weight_of_products').value = totalWeightOfProducts.toFixed(2);
    prepareProductData();

    displayProducts();
}

function displayProducts() {
    const productsList = document.getElementById('products-list');
    productsList.innerHTML = '';

    products.forEach(product => {
        productsList.innerHTML += `
            <div class="alert alert-info">
                ${product.description} - Quantity: ${product.quantity} - Total Weight: ${product.total_weight.toFixed(2)}
            </div>
        `;
    });
}