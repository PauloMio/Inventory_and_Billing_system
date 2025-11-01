<?php
include '../security_check.php';
include '../../database/db_connect.php';

function getInventory($from = null, $to = null) {
    global $conn;
    $sql = "SELECT * FROM inventory WHERE 1";
    if ($from && $to) $sql .= " AND date_of_arrival BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    if ($from && $to) $stmt->bind_param("ss", $from, $to);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getBillingWithProducts($from = null, $to = null) {
    global $conn;

    $sql = "SELECT ci.*, cp.product_name, cp.price AS product_price, cp.quantity, cp.total_price 
            FROM customer_info ci
            LEFT JOIN customer_product cp ON ci.transaction_ID = cp.transaction_ID
            WHERE 1";

    if ($from && $to) {
        $sql .= " AND ci.created_at BETWEEN ? AND ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $from, $to);
    } else {
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Group by transaction ID
    $billingData = [];
    while ($row = $result->fetch_assoc()) {
        $transactionID = $row['transaction_ID'];

        if (!isset($billingData[$transactionID])) {
            $billingData[$transactionID] = [
                'id' => $row['id'],
                'transaction_ID' => $row['transaction_ID'],
                'name' => $row['name'],
                'payment' => $row['payment'],
                'change_amount' => $row['change_amount'],
                'created_at' => $row['created_at'],
                'products' => []
            ];
        }

        if (!empty($row['product_name'])) {
            $billingData[$transactionID]['products'][] = [
                'product_name' => $row['product_name'],
                'price' => $row['product_price'],
                'quantity' => $row['quantity'],
                'total_price' => $row['total_price']
            ];
        }
    }

    return $billingData;
}

function getReturns($from = null, $to = null) {
    global $conn;
    $sql = "SELECT * FROM returns WHERE 1";
    if ($from && $to) $sql .= " AND created_at BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    if ($from && $to) $stmt->bind_param("ss", $from, $to);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
