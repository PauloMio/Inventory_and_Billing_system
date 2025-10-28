<?php
include '../../database/db_connect.php';

// Fetch all remarks options
$remarks_result = $conn->query("SELECT id, name FROM remarks ORDER BY name ASC");
$remarks_options = [];
while ($row = $remarks_result->fetch_assoc()) {
    $remarks_options[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Product Return</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8f9fa; }
.card { border-radius: 10px; }
.table th, .table td { vertical-align: middle; }
</style>
</head>
<body>

<div class="container py-5">
    <h2 class="text-center mb-4">Return System</h2>

    <!-- Search Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">Search Transaction</div>
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <input type="text" id="searchInput" class="form-control" placeholder="Enter Transaction ID or Customer Name">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" id="searchBtn">Search</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Info -->
    <div id="transactionInfo" class="mb-4" style="display:none;">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">Transaction Information</div>
            <div class="card-body">
                <p><strong>Transaction ID:</strong> <span id="transID"></span></p>
                <p><strong>Customer:</strong> <span id="custName"></span></p>
                <p><strong>Address:</strong> <span id="custAddress"></span></p>
                <p><strong>Contact:</strong> <span id="custContact"></span></p>
                <p><strong>Date:</strong> <span id="transDate"></span></p>
            </div>
        </div>
    </div>

    <!-- Product Table -->
    <div id="productSection" class="card shadow-sm" style="display:none;">
        <div class="card-header bg-dark text-white">Products Purchased</div>
        <div class="card-body table-responsive">
            <form action="return_function.php" method="POST" id="returnForm">
                <input type="hidden" name="transaction_ID" id="transaction_ID">

                <table class="table table-bordered align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Purchased Qty</th>
                            <th>Return Qty</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="productTable"></tbody>
                </table>

                <div class="text-end">
                    <button type="submit" name="save_return" class="btn btn-success px-4">Save Return</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Return Logs -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-secondary text-white">Return Logs</div>
        <div class="card-body table-responsive" id="returnLogs">
            <p class="text-muted">Search and save a return transaction to view logs here.</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const remarksOptions = <?php echo json_encode($remarks_options); ?>;
let currentTransaction = null;

// SEARCH TRANSACTION
document.getElementById('searchBtn').addEventListener('click', function() {
    const query = document.getElementById('searchInput').value.trim();
    if (!query) {
        alert('Please enter a transaction ID or customer name.');
        return;
    }

    fetch('return_function.php?search=' + encodeURIComponent(query))
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert('Transaction not found.');
            currentTransaction = null;
            return;
        }

        currentTransaction = data.customer.transaction_ID;
        document.getElementById('transactionInfo').style.display = 'block';
        document.getElementById('productSection').style.display = 'block';
        document.getElementById('transID').textContent = data.customer.transaction_ID;
        document.getElementById('custName').textContent = data.customer.name;
        document.getElementById('custAddress').textContent = data.customer.address || '—';
        document.getElementById('custContact').textContent = data.customer.cp_number || '—';
        document.getElementById('transDate').textContent = data.customer.created_at;
        document.getElementById('transaction_ID').value = data.customer.transaction_ID;

        const tbody = document.getElementById('productTable');
        tbody.innerHTML = '';
        data.products.forEach((p, i) => {
            let remarkSelect = '<select name="remarks['+p.product_name+']" class="form-select form-select-sm" required>';
            remarkSelect += '<option value="">--Select Remark--</option>';
            remarksOptions.forEach(r => remarkSelect += `<option value="${r.name}">${r.name}</option>`);
            remarkSelect += '</select>';

            const row = `
                <tr>
                    <td>${i + 1}</td>
                    <td>${p.product_name}</td>
                    <td>₱${parseFloat(p.price).toFixed(2)}</td>
                    <td>${p.quantity}</td>
                    <td><input type="number" min="0" max="${p.quantity}" name="return_qty[${p.product_name}]" class="form-control form-control-sm" required></td>
                    <td>${remarkSelect}</td>
                </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });

        fetch('return_function.php?get_returns=' + encodeURIComponent(data.customer.transaction_ID))
        .then(res => res.text())
        .then(html => document.getElementById('returnLogs').innerHTML = html);
    });
});

// VALIDATE FORM
document.getElementById('returnForm').addEventListener('submit', function(e) {
    if (!currentTransaction) {
        alert('Please search and select a transaction first.');
        e.preventDefault();
        return;
    }

    const qtyInputs = document.querySelectorAll('input[name^="return_qty"]');
    const remarkSelects = document.querySelectorAll('select[name^="remarks"]');

    let hasValid = false;

    for (let i = 0; i < qtyInputs.length; i++) {
        const qty = parseInt(qtyInputs[i].value) || 0;
        const remark = remarkSelects[i].value.trim();

        if (qty > 0) {
            hasValid = true;
            if (!remark) {
                alert('Please select a remark for all items with a return quantity.');
                e.preventDefault();
                return;
            }
        }
    }

    if (!hasValid) {
        alert('Please enter at least one valid return quantity.');
        e.preventDefault();
    }
});
</script>
</body>
</html>
