<?php
include '../../database/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Load Transaction</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { padding: 20px; }
</style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Load Transaction</h2>

    <!-- Search and Sort -->
    <div class="row mb-3 g-3">
        <div class="col-md-6">
            <input type="text" id="searchBox" class="form-control" placeholder="Search by Transaction ID or Customer Name">
        </div>
        <div class="col-md-3">
            <select id="sortOrder" class="form-select">
                <option value="DESC" selected>Latest to Oldest</option>
                <option value="ASC">Oldest to Latest</option>
            </select>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Transaction ID</th>
                    <th>Customer Name</th>
                    <th>Address</th>
                    <th>Contact</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="transactionTableBody">
                <!-- Table rows will be loaded here dynamically -->
            </tbody>
        </table>
    </div>

    <a href="billing.php" class="btn btn-secondary mt-3">Back to New Transaction</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchBox = document.getElementById('searchBox');
    const sortOrder = document.getElementById('sortOrder');
    const tableBody = document.getElementById('transactionTableBody');

    async function fetchTransactions() {
        const query = searchBox.value.trim();
        const sort = sortOrder.value;

        const params = new URLSearchParams();
        if(query !== '') params.append('search', query);
        params.append('sort', sort);

        const response = await fetch('load_transaction_data.php?' + params.toString());
        const html = await response.text();
        tableBody.innerHTML = html;
    }

    searchBox.addEventListener('input', fetchTransactions);
    sortOrder.addEventListener('change', fetchTransactions);

    // Initial load of all transactions
    fetchTransactions();
});
</script>

</body>
</html>
