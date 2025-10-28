<?php
include '../../database/db_connect.php';

$transaction_ID = $_GET['transaction_ID'] ?? '';
if ($transaction_ID == '') {
    die("Transaction not found.");
}

// Fetch customer info
$custStmt = $conn->prepare("SELECT * FROM customer_info WHERE transaction_ID = ?");
$custStmt->bind_param("s", $transaction_ID);
$custStmt->execute();
$customer = $custStmt->get_result()->fetch_assoc();

// Fetch purchased products
$prodStmt = $conn->prepare("SELECT * FROM customer_product WHERE transaction_ID = ?");
$prodStmt->bind_param("s", $transaction_ID);
$prodStmt->execute();
$products = $prodStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Receipt</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
@media print {
    .no-print { display: none; }
}
</style>
</head>
<body class="p-4">

<div class="container border p-4">
    <div class="text-center mb-4">
        <h3>Receipt</h3>
        <p>Transaction ID: <strong><?= htmlspecialchars($transaction_ID) ?></strong></p>
    </div>

    <h5>Customer Information</h5>
    <p>
        Name: <?= htmlspecialchars($customer['name']) ?><br>
        Address: <?= htmlspecialchars($customer['address']) ?><br>
        Contact: <?= htmlspecialchars($customer['cp_number']) ?><br>
        Date: <?= date('Y-m-d H:i') ?>
    </p>

    <h5>Purchased Products</h5>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sum_total = 0;
            while($p = $products->fetch_assoc()):
                $sum_total += $p['total_price'];
            ?>
            <tr>
                <td><?= htmlspecialchars($p['product_name']) ?></td>
                <td>₱<?= number_format($p['price'],2) ?></td>
                <td><?= $p['quantity'] ?></td>
                <td>₱<?= number_format($p['total_price'],2) ?></td>
            </tr>
            <?php endwhile; ?>
            <tr class="fw-bold">
                <td colspan="3" class="text-end">Grand Total</td>
                <td>₱<?= number_format($sum_total,2) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print();" class="btn btn-primary">Print Receipt</button>
    </div>
</div>

</body>
</html>
