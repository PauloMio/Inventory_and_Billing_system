<?php
include '../../database/db_connect.php';

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortOrder = isset($_GET['sort']) && in_array($_GET['sort'], ['ASC','DESC']) ? $_GET['sort'] : 'DESC';

// Build query
$sql = "SELECT * FROM customer_info WHERE 1";
$params = [];
$types = "";

if ($searchQuery !== '') {
    $sql .= " AND (transaction_ID LIKE ? OR name LIKE ?)";
    $likeQuery = "%$searchQuery%";
    $params = [$likeQuery, $likeQuery];
    $types = "ss";
}

$sql .= " ORDER BY created_at $sortOrder";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo '<tr>
            <td>'.htmlspecialchars($row['transaction_ID']).'</td>
            <td>'.htmlspecialchars($row['name']).'</td>
            <td>'.htmlspecialchars($row['address']).'</td>
            <td>'.htmlspecialchars($row['cp_number']).'</td>
            <td>'.date("Y-m-d H:i", strtotime($row['created_at'])).'</td>
            <td>
                <a href="receipt.php?transaction_ID='.htmlspecialchars($row['transaction_ID']).'" class="btn btn-success btn-sm">View / Print Receipt</a>
            </td>
        </tr>';
    }
} else {
    echo '<tr><td colspan="6" class="text-center">No transactions found.</td></tr>';
}
?>
