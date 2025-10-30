<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Main Menu - High Intensity</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background-color: #f0f2f5;
    font-family: Arial, sans-serif;
}

.header {
    text-align: center;
    margin-top: 50px;
    margin-bottom: 50px;
}

.header h1 {
    font-size: 3rem;
    font-weight: bold;
    color: #343a40;
}

.header h4 {
    font-size: 1.5rem;
    color: #6c757d;
}

.menu-container {
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
}

.menu-card {
    width: 200px;
    height: 200px;
    background-color: #343a40;
    color: white;
    border-radius: 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
}

.menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
}

.menu-card img {
    width: 80px;
    height: 80px;
    margin-bottom: 15px;
}

.menu-card span {
    font-size: 1.2rem;
    font-weight: bold;
    text-align: center;
}
</style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>High Intensity</h1>
        <h4>Inventory and Billing System</h4>
    </div>

    <div class="menu-container">
        <!-- Inventory Button -->
        <a href="inventory/inventory.php" class="menu-card">
            <img src="../images/icnons/inventory_White.png" alt="Inventory Icon">
            <span>Inventory</span>
        </a>

        <!-- Billing Button -->
        <a href="billing/billing.php" class="menu-card">
            <img src="../images/icons/billing_White.png" alt="Billing Icon">
            <span>Billing</span>
        </a>

        <!-- Return Button -->
        <a href="return/return.php" class="menu-card">
            <img src="../images/icons/return_White.png" alt="Return Icon">
            <span>Return</span>
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
