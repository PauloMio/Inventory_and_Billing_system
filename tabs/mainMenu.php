<?php include 'security_check.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Main Menu - High Intensity</title>

<!-- Bootstrap & Font Awesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
/* === Global Layout === */
body {
    height: 100vh;
    margin: 0;
    background: linear-gradient(135deg, #2c3e50, #4ca1af);
    font-family: 'Poppins', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
}

/* === Main Glass Panel === */
.menu-container {
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 50px;
    width: 90%;
    max-width: 950px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease-in-out;
}

.menu-container:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.4);
}

/* === Header Section === */
.header h1 {
    font-weight: 700;
    font-size: 2.5rem;
    color: #fff;
    margin-bottom: 5px;
}

.header h4 {
    font-weight: 400;
    color: #e0e0e0;
    margin-bottom: 40px;
}

/* === Menu Buttons === */
.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 25px;
    justify-content: center;
}

.menu-btn {
    background-color: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 15px;
    padding: 30px 20px;
    color: #fff;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: all 0.3s ease;
}

.menu-btn:hover {
    background-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-5px);
    color: #fff;
    text-decoration: none;
}

/* === Menu Icon === */
.menu-btn img {
    width: 75px;
    height: 75px;
    margin-bottom: 15px;
    filter: drop-shadow(0px 0px 8px rgba(255, 255, 255, 0.4));
}

/* === Menu Text === */
.menu-btn span {
    font-size: 1.1rem;
    font-weight: 500;
    letter-spacing: 0.5px;
}

/* === Footer Text === */
.footer-text {
    margin-top: 40px;
    font-size: 0.9rem;
    color: rgba(255,255,255,0.7);
}

/* === Logout Button === */
.logout-btn {
    margin-top: 30px;
    background-color: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.3);
    color: #fff;
    padding: 12px 30px;
    font-size: 1rem;
    border-radius: 10px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.logout-btn:hover {
    background-color: rgba(255,255,255,0.35);
    color: #fff;
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}

/* === Responsive Adjustments === */
@media (max-width: 576px) {
    .menu-btn {
        padding: 25px;
    }
    .menu-btn img {
        width: 60px;
        height: 60px;
    }
    .menu-btn span {
        font-size: 1rem;
    }
    .logout-btn {
        width: 100%;
    }
}
</style>
</head>
<body>

<!-- === Main Container === -->
<div class="menu-container">

    <!-- Header -->
    <div class="header">
        <h1>High Intensity</h1>
        <h4>Inventory and Billing System</h4>
    </div>

    <!-- Menu Grid -->
    <div class="menu-grid">
        <!-- Inventory -->
        <a href="inventory/inventory.php" class="menu-btn">
            <img src="../images/icons/inventory_White.png" alt="Inventory Icon">
            <span>Inventory</span>
        </a>

        <!-- Billing -->
        <a href="billing/billing.php" class="menu-btn">
            <img src="../images/icons/billing_White.png" alt="Billing Icon">
            <span>Billing</span>
        </a>

        <!-- Return -->
        <a href="return/return.php" class="menu-btn">
            <img src="../images/icons/return_White.png" alt="Return Icon">
            <span>Return</span>
        </a>

        <!-- Admin -->
        <a href="admin/admin.php" class="menu-btn">
            <img src="../images/icons/wrench_White.png" alt="Admin Icon">
            <span>Admin</span>
        </a>
    </div>

    <!-- Logout Button -->
    <a href="logout.php" class="logout-btn mt-4">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
