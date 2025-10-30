<?php
include '../../database/db_connect.php';

// ===== CREATE USER =====
if (isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO users (name,email,password,status,created_at) VALUES (?,?,?,?,NOW())");
    $stmt->bind_param("ssss",$name,$email,$password,$status);
    $stmt->execute();
    header("Location: user.php");
    exit;
}

// ===== READ USER (for edit modal) =====
if (isset($_GET['fetch_id'])) {
    $id = $_GET['fetch_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    echo json_encode($result);
    exit;
}

// ===== UPDATE USER =====
if (isset($_POST['edit_user'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $status = $_POST['status'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=?, status=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("ssssi",$name,$email,$passwordHash,$status,$id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, status=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("sssi",$name,$email,$status,$id);
    }
    $stmt->execute();
    header("Location: user.php");
    exit;
}

// ===== DELETE USER =====
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    header("Location: user.php");
    exit;
}
?>
