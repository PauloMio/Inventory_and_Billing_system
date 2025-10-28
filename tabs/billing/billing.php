<?php
include '../../database/db_connect.php';

// --- Generate next transaction ID
$transaction_ID = 1;
$result = $conn->query("SELECT transaction_ID FROM customer_info ORDER BY id ASC");
$existing_ids = [];
while ($row = $result->fetch_assoc()) {
    $existing_ids[] = (int) $row['transaction_ID'];
}

// Find first available number
while (in_array($transaction_ID, $existing_ids)) {
    $transaction_ID++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Billing System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4 text-center">Billing System</h2>

    <!-- Customer Info -->
    <form action="billing_function.php" method="POST" onsubmit="return validateForm();">
        <input type="hidden" name="transaction_ID" value="<?= $transaction_ID ?>">

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-dark text-white">
                Customer Information
                <span class="float-end">Transaction ID: <strong><?= $transaction_ID ?></strong></span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Contact Number</label>
                        <input type="text" name="cp_number" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <!-- Barcode Search -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-dark text-white">Product Lookup</div>
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <input type="text" id="barcodeInput" class="form-control" placeholder="Enter Barcode and press Enter">
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-success" id="clearTable">Clear Table</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Table -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">Purchased Products <span class="text-danger">*</span></div>
            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle" id="productTable">
                    <thead class="table-secondary">
                        <tr>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <div class="text-end">
                    <h5 class="fw-bold">Total: ₱<span id="sumTotal">0.00</span></h5>
                </div>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">Payment Details</div>
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <label>Payment Amount (₱) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="payment" id="paymentInput" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Change (₱)</label>
                        <input type="text" id="changeInput" class="form-control" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end">
            <button class="btn btn-primary px-4" name="save_transaction">Save Transaction</button>
            <a href="load_transaction.php" class="btn btn-info ms-2">Load Previous Transaction</a>
        </div>

        <!-- Hidden JSON field for products -->
        <input type="hidden" name="product_data" id="productData">
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// --- Elements
const barcodeInput = document.getElementById('barcodeInput');
const productTable = document.querySelector('#productTable tbody');
const sumTotalEl = document.getElementById('sumTotal');
const productDataInput = document.getElementById('productData');
const paymentInput = document.getElementById('paymentInput');
const changeInput = document.getElementById('changeInput');
let products = [];

// --- Fetch product by barcode
barcodeInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const barcode = barcodeInput.value.trim();
        if (!barcode) return;
        fetch(`billing_function.php?barcode=${barcode}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) addProduct(data.product);
                else alert('Product not found!');
                barcodeInput.value = '';
            });
    }
});

// --- Add product
function addProduct(product) {
    const existing = products.find(p => p.barcode === product.barcode);
    if (existing) {
        existing.quantity += 1;
        existing.total_price = existing.quantity * existing.price;
    } else {
        product.quantity = 1;
        product.total_price = product.price;
        products.push(product);
    }
    renderTable();
}

// --- Render table
function renderTable() {
    productTable.innerHTML = '';
    let sumTotal = 0;
    products.forEach((p, i) => {
        sumTotal += p.total_price;
        const row = `
            <tr>
                <td>${p.name}</td>
                <td>₱${p.price.toFixed(2)}</td>
                <td><input type="number" min="1" value="${p.quantity}" class="form-control form-control-sm" onchange="updateQty(${i}, this.value)"></td>
                <td>₱${p.total_price.toFixed(2)}</td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(${i})">Remove</button></td>
            </tr>`;
        productTable.insertAdjacentHTML('beforeend', row);
    });
    sumTotalEl.textContent = sumTotal.toFixed(2);
    productDataInput.value = JSON.stringify(products);
    calculateChange();
}

// --- Update quantity
function updateQty(index, qty) {
    qty = parseInt(qty);
    if (qty < 1) qty = 1;
    products[index].quantity = qty;
    products[index].total_price = qty * products[index].price;
    renderTable();
}

// --- Remove product
function removeProduct(index) {
    if (confirm('Remove this product?')) {
        products.splice(index, 1);
        renderTable();
    }
}

// --- Clear table
document.getElementById('clearTable').addEventListener('click', () => {
    if (confirm('Clear all products?')) {
        products = [];
        renderTable();
    }
});

// --- Payment and change calculation
paymentInput.addEventListener('input', calculateChange);
function calculateChange() {
    const total = parseFloat(sumTotalEl.textContent);
    const payment = parseFloat(paymentInput.value) || 0;
    const change = payment - total;
    changeInput.value = change >= 0 ? change.toFixed(2) : '0.00';
}

// --- Validate form before submit
function validateForm() {
    if (!products.length) {
        alert('Please add at least one product.');
        return false;
    }
    if (!document.querySelector('input[name="name"]').value.trim()) {
        alert('Please enter customer name.');
        return false;
    }
    const total = parseFloat(sumTotalEl.textContent);
    const payment = parseFloat(paymentInput.value) || 0;
    if (payment < total) {
        alert('Payment must be equal to or greater than total.');
        return false;
    }
    productDataInput.value = JSON.stringify(products);
    return true;
}
</script>
</body>
</html>
