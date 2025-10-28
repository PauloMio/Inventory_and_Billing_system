<?php
include '../../database/db_connect.php';

if (!isset($_GET['transaction_ID'])) {
    die("Transaction ID not specified.");
}

$transaction_ID = $_GET['transaction_ID'];

// Fetch customer info
$stmt = $conn->prepare("SELECT * FROM customer_info WHERE transaction_ID = ?");
$stmt->bind_param("s", $transaction_ID);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

// Fetch purchased products
$stmt = $conn->prepare("SELECT * FROM customer_product WHERE transaction_ID = ?");
$stmt->bind_param("s", $transaction_ID);
$stmt->execute();
$products = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Receipt - Transaction #<?= htmlspecialchars($transaction_ID) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { padding: 20px; }
.table th, .table td { vertical-align: middle; }
@media print {
    .no-print { display: none; }
}
</style>
</head>
<body>

<div class="container">
    <div class="text-center mb-4">
        <h2>Store Name</h2>
        <p>Transaction Receipt</p>
    </div>

    <div class="mb-3">
        <strong>Transaction ID:</strong> <?= htmlspecialchars($transaction_ID) ?><br>
        <strong>Date:</strong> <?= date("Y-m-d H:i", strtotime($customer['created_at'])) ?><br>
        <strong>Customer Name:</strong> <?= htmlspecialchars($customer['name']) ?><br>
        <strong>Address:</strong> <?= htmlspecialchars($customer['address']) ?><br>
        <strong>Contact:</strong> <?= htmlspecialchars($customer['cp_number']) ?>
    </div>

    <div class="table-responsive mb-3">
        <table class="table table-bordered">
            <thead class="table-secondary">
                <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sum_total = 0;
                $i = 1;
                while ($row = $products->fetch_assoc()):
                    $sum_total += $row['total_price'];
                ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td>₱<?= number_format($row['price'],2) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td>₱<?= number_format($row['total_price'],2) ?></td>
                </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="4" class="text-end fw-bold">Grand Total:</td>
                    <td class="fw-bold">₱<?= number_format($sum_total,2) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-4">
        <p>Thank you for your purchase!</p>
        <button onclick="window.print();" class="btn btn-primary no-print">Print Receipt</button>
        <a href="billing.php" class="btn btn-success no-print">New Transaction</a>
        <a href="load_transaction.php" class="btn btn-info no-print">Load Previous Transaction</a>
    </div>
</div>

<script>
window.onload = function() {
    window.print(); // Auto-trigger print preview
}
</script>
</body>
</html>
