<?php
include '../../database/db_connect.php';

// Add Inventory
if(isset($_POST['add'])){
    $stmt = $conn->prepare("INSERT INTO inventory 
(product_number,name,description,category,brand,supplier,current_stock,total_stock,price,selling_price,barcode,date_of_arrival,created_at) 
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,NOW())");
$stmt->bind_param("ssssssiiddss",
    $_POST['product_number'],
    $_POST['name'],
    $_POST['description'],
    $_POST['category'],
    $_POST['brand'],
    $_POST['supplier'],
    $_POST['current_stock'],
    $_POST['total_stock'],
    $_POST['price'],
    $_POST['selling_price'],
    $_POST['barcode'],
    $_POST['date_of_arrival']
);
    $stmt->execute();
    $stmt->close();
    header("Location: inventory.php");
}

// Update Inventory
if(isset($_POST['update'])){
    $stmt = $conn->prepare("UPDATE inventory SET 
    product_number=?, name=?, description=?, category=?, brand=?, supplier=?, current_stock=?, total_stock=?, price=?, selling_price=?, barcode=?, date_of_arrival=?, updated_at=NOW() 
    WHERE id=?");
$stmt->bind_param("ssssssiiddssi",
    $_POST['product_number'],
    $_POST['name'],
    $_POST['description'],
    $_POST['category'],
    $_POST['brand'],
    $_POST['supplier'],
    $_POST['current_stock'],
    $_POST['total_stock'],
    $_POST['price'],
    $_POST['selling_price'],
    $_POST['barcode'],
    $_POST['date_of_arrival'],
    $_POST['id']
);
    $stmt->execute();
    $stmt->close();
    header("Location: inventory.php");
}

// Delete Inventory
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM inventory WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $stmt->close();
    header("Location: inventory.php");
}
?>
