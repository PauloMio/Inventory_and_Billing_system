<?php
include '../../database/db_connect.php';

// --- SEARCH TRANSACTION ---
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);

    $stmt = $conn->prepare("
        SELECT * FROM customer_info 
        WHERE transaction_ID = ? OR name LIKE CONCAT('%', ?, '%') LIMIT 1
    ");
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $customer = $stmt->get_result()->fetch_assoc();

    if (!$customer) {
        echo json_encode(['success' => false]);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM customer_product WHERE transaction_ID = ?");
    $stmt->bind_param("s", $customer['transaction_ID']);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['success' => true, 'customer' => $customer, 'products' => $products]);
    exit;
}

// --- GET RETURNS LOGS ---
if (isset($_GET['get_returns'])) {
    $transaction_ID = $_GET['get_returns'];
    $stmt = $conn->prepare("SELECT * FROM returns WHERE transaction_ID = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $transaction_ID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p class='text-muted'>No return records found for this transaction.</p>";
        exit;
    }

    echo '<table class="table table-bordered table-striped align-middle">';
    echo '<thead class="table-secondary"><tr>
            <th>#</th><th>Product</th><th>Price</th><th>Returned Qty</th><th>Remarks</th><th>Date</th>
          </tr></thead><tbody>';

    $i = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$i}</td>
                <td>" . htmlspecialchars($row['product_name']) . "</td>
                <td>₱" . number_format($row['price'], 2) . "</td>
                <td>{$row['quantity']}</td>
                <td>" . htmlspecialchars($row['remarks']) . "</td>
                <td>" . date('Y-m-d H:i', strtotime($row['created_at'])) . "</td>
              </tr>";
        $i++;
    }
    echo '</tbody></table>';
    exit;
}

// --- SAVE RETURN ---
if (isset($_POST['save_return'])) {
    $transaction_ID = $_POST['transaction_ID'] ?? null;
    $return_qty = $_POST['return_qty'] ?? [];
    $remarks = $_POST['remarks'] ?? [];

    // Validation: Must have a valid transaction
    if (!$transaction_ID) {
        echo "<script>alert('No transaction selected.'); window.history.back();</script>";
        exit;
    }

    $validEntries = 0;

    foreach ($return_qty as $product_name => $qty) {
        $qty = (int)$qty;
        $remark_text = $remarks[$product_name] ?? '';

        if ($qty <= 0) continue;
        if (empty($remark_text)) {
            echo "<script>alert('All returned items must have a remark.'); window.history.back();</script>";
            exit;
        }

        $stmt = $conn->prepare("SELECT price FROM customer_product WHERE transaction_ID = ? AND product_name = ?");
        $stmt->bind_param("ss", $transaction_ID, $product_name);
        $stmt->execute();
        $prod = $stmt->get_result()->fetch_assoc();
        if (!$prod) continue;

        $stmt = $conn->prepare("
            INSERT INTO returns (transaction_ID, product_name, price, quantity, remarks, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("ssdss", $transaction_ID, $product_name, $prod['price'], $qty, $remark_text);
        $stmt->execute();
        $validEntries++;
    }

    if ($validEntries === 0) {
        echo "<script>alert('No valid return entries found.'); window.history.back();</script>";
        exit;
    }

    echo "<script>
        alert('Return record saved successfully!');
        window.location='return.php';
    </script>";
    exit;
}



?>
