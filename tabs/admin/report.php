<?php
include '../security_check.php';
include '../../database/db_connect.php';
include 'report_function.php'; // Backend functions for data fetching

$from = $_GET['from_date'] ?? null;
$to = $_GET['to_date'] ?? null;

$inventoryData = getInventory($from, $to);
$billingData = getBillingWithProducts($from, $to);
$returnData = getReturns($from, $to);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reports Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body { background-color: #f0f2f5; font-family: Arial, sans-serif; }
.card { border-radius: 15px; margin-bottom: 30px; }
.card-header { background-color: #343a40; color: #fff; border-radius: 15px 15px 0 0; }
.chart-container { width: 100%; height: 350px; }
.nested-table { margin-left: 40px; margin-top: 10px; }

/* Voice Button */
.voice-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background-color: rgba(0,0,0,0.7);
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
}
.voice-btn:hover {
    background-color: rgba(0,0,0,0.9);
    transform: scale(1.1);
}

/* Toast */
.toast-msg {
    position: fixed;
    bottom: 100px;
    right: 30px;
    background: rgba(0,0,0,0.8);
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 0.9rem;
    opacity: 0;
    transition: opacity 0.4s ease;
}
.toast-msg.show {
    opacity: 1;
}
</style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Reports Dashboard</h2>
    <div class="d-flex justify-content-between mb-3">
        <a href="admin.php" class="btn btn-secondary ms-2">Back to Admin Menu</a>
    </div>

    <!-- Date Filter -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label>From</label>
            <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($from ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label>To</label>
            <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($to ?? '') ?>">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <!-- Inventory Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Inventory</span>
            <button class="btn btn-light btn-sm" onclick="printDiv('inventoryTable')">Print</button>
        </div>
        <div class="card-body">
            <canvas id="inventoryChart" class="chart-container"></canvas>
            <div id="inventoryTable" class="table-responsive mt-3">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Product Name</th><th>Category</th><th>Brand</th>
                            <th>Current Stock</th><th>Total Stock</th><th>Price</th><th>Date of Arrival</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($inventoryData as $inv): ?>
                        <tr>
                            <td><?= htmlspecialchars($inv['name']) ?></td>
                            <td><?= htmlspecialchars($inv['category']) ?></td>
                            <td><?= htmlspecialchars($inv['brand']) ?></td>
                            <td><?= $inv['current_stock'] ?></td>
                            <td><?= $inv['total_stock'] ?></td>
                            <td><?= number_format($inv['price'],2) ?></td>
                            <td><?= $inv['date_of_arrival'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($inventoryData)): ?>
                        <tr><td colspan="8" class="text-center text-muted">No records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Billing Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Billing (Customer Transactions)</span>
            <button class="btn btn-light btn-sm" onclick="printDiv('billingTable')">Print</button>
        </div>
        <div class="card-body">
            <canvas id="billingChart" class="chart-container"></canvas>
            <div id="billingTable" class="table-responsive mt-3">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Transaction ID</th><th>Name</th><th>Payment</th>
                            <th>Change</th><th>Date</th><th>Products Purchased</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($billingData as $bill): ?>
                        <tr>
                            <td><?= htmlspecialchars($bill['transaction_ID']) ?></td>
                            <td><?= htmlspecialchars($bill['name']) ?></td>
                            <td><?= number_format($bill['payment'], 2) ?></td>
                            <td><?= number_format($bill['change_amount'], 2) ?></td>
                            <td><?= $bill['created_at'] ?></td>
                            <td>
                                <?php if (!empty($bill['products'])): ?>
                                    <table class="table table-sm table-bordered nested-table">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($bill['products'] as $product): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($product['product_name']) ?></td>
                                                <td><?= number_format($product['price'], 2) ?></td>
                                                <td><?= $product['quantity'] ?></td>
                                                <td><?= number_format($product['total_price'], 2) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <em>No products recorded</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($billingData)): ?>
                        <tr><td colspan="7" class="text-center text-muted">No billing records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Returns Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Returns</span>
            <button class="btn btn-light btn-sm" onclick="printDiv('returnsTable')">Print</button>
        </div>
        <div class="card-body">
            <canvas id="returnsChart" class="chart-container"></canvas>
            <div id="returnsTable" class="table-responsive mt-3">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Transaction ID</th><th>Product Name</th>
                            <th>Quantity</th><th>Price</th><th>Remarks</th><th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($returnData as $ret): ?>
                        <tr>
                            <td><?= htmlspecialchars($ret['transaction_ID']) ?></td>
                            <td><?= htmlspecialchars($ret['product_name']) ?></td>
                            <td><?= $ret['quantity'] ?></td>
                            <td><?= number_format($ret['price'],2) ?></td>
                            <td><?= htmlspecialchars($ret['remarks']) ?></td>
                            <td><?= $ret['created_at'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($returnData)): ?>
                        <tr><td colspan="7" class="text-center text-muted">No return records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toast" class="toast-msg"></div>

<!-- Voice Button -->
<button class="voice-btn" id="voiceBtn" title="Voice Navigation">
    <i class="fa-solid fa-microphone"></i>
</button>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<script>
// Inventory Chart
const invLabels = <?= json_encode(array_column($inventoryData, 'name')) ?>;
const invData = <?= json_encode(array_column($inventoryData, 'current_stock')) ?>;
new Chart(document.getElementById('inventoryChart').getContext('2d'), {
    type: 'bar',
    data: { labels: invLabels, datasets: [{ label: 'Current Stock', data: invData, backgroundColor: '#4ca1af' }] }
});

// Billing Chart
const billLabels = <?= json_encode(array_column($billingData, 'name')) ?>;
const billData = <?= json_encode(array_column($billingData, 'payment')) ?>;
new Chart(document.getElementById('billingChart').getContext('2d'), {
    type: 'bar',
    data: { labels: billLabels, datasets: [{ label: 'Payments', data: billData, backgroundColor: '#2c3e50' }] }
});

// Returns Chart
const retLabels = <?= json_encode(array_column($returnData, 'product_name')) ?>;
const retData = <?= json_encode(array_column($returnData, 'quantity')) ?>;
new Chart(document.getElementById('returnsChart').getContext('2d'), {
    type: 'bar',
    data: { labels: retLabels, datasets: [{ label: 'Quantity Returned', data: retData, backgroundColor: '#ff6b6b' }] }
});

// Print Function
function printDiv(divId) {
    const content = document.getElementById(divId).innerHTML;
    const myWindow = window.open('', '', 'width=900,height=600');
    myWindow.document.write('<html><head><title>Print</title>');
    myWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">');
    myWindow.document.write('<style>body{padding:20px;font-family:Arial,sans-serif;} h2{text-align:center;margin-bottom:20px;}</style>');
    myWindow.document.write('</head><body>');
    myWindow.document.write('<h2>High Intensity</h2>');
    myWindow.document.write(content);
    myWindow.document.write('</body></html>');
    myWindow.document.close();
    myWindow.print();
}

// --- Voice Navigation ---
const toast = document.getElementById('toast');
function showToast(msg) {
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2500);
}

const voiceBtn = document.getElementById('voiceBtn');
voiceBtn.addEventListener('click', () => {
    if (!('webkitSpeechRecognition' in window)) {
        showToast("Voice recognition not supported.");
        return;
    }
    const recognition = new webkitSpeechRecognition();
    recognition.lang = 'en-US';
    recognition.start();
    showToast("Listening...");

    recognition.onresult = (event) => {
        const transcript = event.results[0][0].transcript.toLowerCase();
        showToast(`Heard: ${transcript}`);
        if (transcript.includes("admin") || transcript.includes("admin menu") || transcript.includes("go to admin")) {
            window.location.href = "admin.php";
        } else {
            showToast("Command not recognized.");
        }
    };
    recognition.onerror = () => showToast("Error capturing voice.");
});
</script>
</body>
</html>
