<?php
session_start();
include '../database/db_connect.php';

$message = '';

// Fetch departments for dropdown
$deptResult = $conn->query("SELECT * FROM department ORDER BY department ASC");
$departments = [];
while ($row = $deptResult->fetch_assoc()) {
    $departments[] = $row;
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $department = trim($_POST['department']);

    if (!empty($name) && !empty($email) && !empty($password) && !empty($department)) {
        // Check if email already exists
        $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $existing = $check->get_result()->fetch_assoc();

        if ($existing) {
            $message = '<div class="alert alert-danger text-center">Email already registered.</div>';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, department, status, created_at) VALUES (?, ?, ?, ?, 'Inactive', NOW())");
            $stmt->bind_param("ssss", $name, $email, $hashedPassword, $department);
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success text-center">Registration successful! Waiting for admin approval.</div>';
            } else {
                $message = '<div class="alert alert-danger text-center">Registration failed. Try again.</div>';
            }
        }
    } else {
        $message = '<div class="alert alert-warning text-center">All fields are required.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - High Intensity</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
    background: linear-gradient(135deg, #2c3e50, #4ca1af);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: Arial, sans-serif;
}
.register-container {
    background: rgba(255, 255, 255, 0.95);
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 450px;
    transition: all 0.3s ease;
}
.register-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
}
.register-header {
    text-align: center;
    margin-bottom: 30px;
}
.register-header h2 {
    font-weight: bold;
    color: #2c3e50;
    letter-spacing: 1px;
}
.register-header h5 {
    color: #6c757d;
}

.btn-register {
    background: linear-gradient(135deg, #2c3e50, #4ca1af);
    color: #fff;
    font-weight: 600;
    border: none;
    transition: all 0.3s ease;
}
.btn-register:hover {
    background: linear-gradient(135deg, #1f2d3a, #3b8ea5);
    color: #fff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(44, 62, 80, 0.4);
}
.form-control:focus {
    box-shadow: 0 0 5px rgba(44, 62, 80, 0.5);
    border-color: #2c3e50;
}

/* Eye icon styling */
.position-relative {
    position: relative;
}
#togglePassword {
    position: absolute;
    top: 38px;
    right: 15px;
    cursor: pointer;
    color: #6c757d;
    transition: color 0.2s ease;
}
#togglePassword:hover {
    color: #2c3e50;
}

/* Login link styling */
.login-link {
    text-align: center;
    margin-top: 15px;
}
.login-link a {
    color: #2c3e50;
    text-decoration: underline;
    font-weight: 500;
}
.login-link a:hover {
    color: #1f2d3a;
}
</style>
</head>
<body>

<div class="register-container">
    <div class="register-header">
        <h2>High Intensity</h2>
        <h5>Inventory and Billing System</h5>
    </div>

    <?php if (!empty($message)) echo $message; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>

        <div class="mb-3 position-relative">
            <label class="form-label">Password</label>
            <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Enter your password" required>
            <i class="fa-solid fa-eye" id="togglePassword"></i>
        </div>

        <div class="mb-3">
            <label class="form-label">Department</label>
            <select name="department" class="form-select" required>
                <option value="" disabled selected>Select Department</option>
                <?php foreach($departments as $dept): ?>
                    <option value="<?= htmlspecialchars($dept['department']) ?>"><?= htmlspecialchars($dept['department']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-register">Register</button>
        </div>
    </form>

    <div class="login-link">
        <p>Already have an account? <a href="../index.php">Login here</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Show/Hide password functionality
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('passwordInput');

togglePassword.addEventListener('click', function () {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
});
</script>

</body>
</html>
