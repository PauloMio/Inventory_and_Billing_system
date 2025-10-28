<?php
include '../../database/db_connect.php';

// --- Search product by barcode
if (isset($_GET['barcode'])) {
    $barcode = $_GET['barcode'];
    $stmt = $conn->prepare("SELECT * FROM inventory WHERE barcode = ?");
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'product' => [
                'barcode' => $row['barcode'],
                'name' => $row['name'],
                'price' => (float)$row['selling_price']
            ]
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// --- Save transaction
if (isset($_POST['save_transaction'])) {
    $transaction_ID = $_POST['transaction_ID'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $cp_number = $_POST['cp_number'];
    $productData = json_decode($_POST['product_data'], true);

    if (!$productData || count($productData) === 0) {
        echo "<script>alert('No products in the bill!'); window.history.back();</script>";
        exit;
    }

    // Insert customer info
    $stmt = $conn->prepare("INSERT INTO customer_info (transaction_ID, name, address, cp_number, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $transaction_ID, $name, $address, $cp_number);
    $stmt->execute();

    // Compute sum_total
    $sum_total = 0;
    foreach ($productData as $p) $sum_total += $p['total_price'];

    // Insert products & update inventory
    foreach ($productData as $p) {
        $stmt = $conn->prepare("INSERT INTO customer_product (transaction_ID, product_name, price, quantity, total_price, sum_total, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssdddd", $transaction_ID, $p['name'], $p['price'], $p['quantity'], $p['total_price'], $sum_total);
        $stmt->execute();

        // Update inventory stock
        $stmt2 = $conn->prepare("UPDATE inventory SET current_stock = current_stock - ? WHERE barcode = ?");
        $stmt2->bind_param("is", $p['quantity'], $p['barcode']);
        $stmt2->execute();
    }

    echo "<script>
        alert('Transaction saved successfully!');
        window.location='receipt.php?transaction_ID={$transaction_ID}';
    </script>";
    exit;
}
?>
