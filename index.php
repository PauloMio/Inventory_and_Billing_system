<?php
session_start();
include 'database/db_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        if ($user['status'] !== 'Active') {
            $message = '<div class="alert alert-warning text-center">Account is inactive. Please contact the administrator.</div>';
            $log_attempt = "inactive";
        } else {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['department'] = $user['department'];

                $message = '<div class="alert alert-success text-center">Login successful! Redirecting...</div>';
                $log_attempt = "logged in";

                $log = $conn->prepare("INSERT INTO logs (email, attempt) VALUES (?, ?)");
                $log->bind_param('ss', $email, $log_attempt);
                $log->execute();

                header("refresh:2; url=tabs/mainMenu.php");
            } else {
                $message = '<div class="alert alert-danger text-center">Invalid email or password.</div>';
                $log_attempt = "invalid email,password or account inactive";
            }
        }
    } else {
        $message = '<div class="alert alert-danger text-center">Invalid email or password.</div>';
        $log_attempt = "invalid email,password or account inactive";
    }

    if (isset($log_attempt) && $log_attempt !== 'logged in') {
        $log = $conn->prepare("INSERT INTO logs (email, attempt) VALUES (?, ?)");
        $log->bind_param('ss', $email, $log_attempt);
        $log->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - High Intensity</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
    background: linear-gradient(135deg, #2c3e50, #4ca1af);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}
.login-container {
    background: rgba(255, 255, 255, 0.95);
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 400px;
    transition: all 0.3s ease;
}
.login-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
}
.login-header {
    text-align: center;
    margin-bottom: 30px;
}
.login-header h2 {
    font-weight: bold;
    color: #2c3e50;
    letter-spacing: 1px;
}
.login-header h5 {
    color: #6c757d;
}

/* Login button styling */
.btn-login {
    background: linear-gradient(135deg, #2c3e50, #4ca1af);
    color: #fff;
    font-weight: 600;
    border: none;
    transition: all 0.3s ease;
}
.btn-login:hover {
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
</style>
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <h2>High Intensity</h2>
        <h5>Inventory and Billing System</h5>
    </div>

    <?php if (!empty($message)) echo $message; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>

        <div class="mb-3 position-relative">
            <label class="form-label">Password</label>
            <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Enter your password" required>
            <i class="fa-solid fa-eye" id="togglePassword"></i>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-login">Login</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Show/Hide password functionality
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('passwordInput');

togglePassword.addEventListener('click', function () {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);

    // Toggle eye and eye-slash icon
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
});
</script>

</body>
</html>
