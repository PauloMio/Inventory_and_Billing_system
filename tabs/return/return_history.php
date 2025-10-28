<?php
include '../../database/db_connect.php';

// Fetch all returns ordered by created_at DESC (default: latest first)
$result = $conn->query("SELECT * FROM returns ORDER BY created_at DESC");
$returns = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Return History</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8f9fa; }
.card { border-radius: 10px; }
.table th, .table td { vertical-align: middle; }
.sort-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
</style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Return History</h2>
        <a href="return.php" class="btn btn-secondary">← Back to Return</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            Return Records
        </div>
        <div class="card-body">
            
            <!-- Search and Sort Controls -->
            <div class="sort-controls mb-3">
                <input type="text" id="searchInput" class="form-control w-50" placeholder="Search by Transaction ID...">

                <select id="sortOrder" class="form-select w-auto">
                    <option value="desc" selected>Latest to Oldest</option>
                    <option value="asc">Oldest to Latest</option>
                </select>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle" id="returnsTable">
                    <thead class="table-secondary">
                        <tr>
                            <th>#</th>
                            <th>Transaction ID</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Remarks</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="returnsBody">
                        <?php if (count($returns) > 0): ?>
                            <?php foreach ($returns as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($row['transaction_ID']) ?></td>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td>₱<?= number_format($row['price'], 2) ?></td>
                                <td><?= $row['quantity'] ?></td>
                                <td><?= htmlspecialchars($row['remarks']) ?></td>
                                <td data-date="<?= $row['created_at'] ?>"><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center text-muted">No return records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// --- Search by Transaction ID
document.getElementById('searchInput').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#returnsTable tbody tr');

    rows.forEach(row => {
        const transactionID = row.children[1].textContent.toLowerCase();
        row.style.display = transactionID.includes(filter) ? '' : 'none';
    });
});

// --- Sorting by Date
document.getElementById('sortOrder').addEventListener('change', function() {
    const order = this.value;
    const tbody = document.getElementById('returnsBody');
    const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => r.children.length > 1);

    rows.sort((a, b) => {
        const dateA = new Date(a.querySelector('[data-date]').dataset.date);
        const dateB = new Date(b.querySelector('[data-date]').dataset.date);
        return order === 'asc' ? dateA - dateB : dateB - dateA;
    });

    // Reorder the rows in the table
    tbody.innerHTML = '';
    rows.forEach((row, i) => {
        row.children[0].textContent = i + 1; // reindex
        tbody.appendChild(row);
    });
});
</script>
</body>
</html>
