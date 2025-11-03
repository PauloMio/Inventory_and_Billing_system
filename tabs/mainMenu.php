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

.menu-btn img {
    width: 75px;
    height: 75px;
    margin-bottom: 15px;
    filter: drop-shadow(0px 0px 8px rgba(255, 255, 255, 0.4));
}

.menu-btn span {
    font-size: 1.1rem;
    font-weight: 500;
    letter-spacing: 0.5px;
}

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

/* === Floating Mic Button === */
.voice-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background-color: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.3);
    color: #fff;
    border-radius: 50%;
    width: 65px;
    height: 65px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}
.voice-btn:hover {
    background-color: rgba(255,255,255,0.4);
    transform: scale(1.1);
}

/* Toast display */
.toast-msg {
    position: fixed;
    bottom: 110px;
    right: 30px;
    background: rgba(0,0,0,0.7);
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

@media (max-width: 576px) {
    .menu-btn { padding: 25px; }
    .menu-btn img { width: 60px; height: 60px; }
    .menu-btn span { font-size: 1rem; }
    .logout-btn { width: 100%; }
}
</style>
</head>
<body>

<div class="menu-container">
    <div class="header">
        <h1>High Intensity</h1>
        <h4>Inventory and Billing System</h4>
    </div>

    <div class="menu-grid">
        <a href="inventory/inventory.php" class="menu-btn">
            <img src="../images/icons/inventory_White.png" alt="Inventory Icon">
            <span>Inventory</span>
        </a>

        <a href="billing/billing.php" class="menu-btn">
            <img src="../images/icons/billing_White.png" alt="Billing Icon">
            <span>Billing</span>
        </a>

        <a href="return/return.php" class="menu-btn">
            <img src="../images/icons/return_White.png" alt="Return Icon">
            <span>Return</span>
        </a>

        <a href="admin/admin.php" class="menu-btn">
            <img src="../images/icons/wrench_White.png" alt="Admin Icon">
            <span>Admin</span>
        </a>
    </div>

    <a href="logout.php" class="logout-btn mt-4">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
</div>

<!-- Voice Command Button -->
<div class="voice-btn" id="voiceBtn" title="Click to Speak">
    <i class="fa-solid fa-microphone"></i>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast-msg"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const voiceBtn = document.getElementById('voiceBtn');
const toast = document.getElementById('toast');

function showToast(message) {
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2500);
}

voiceBtn.addEventListener('click', () => {
    if (!('webkitSpeechRecognition' in window)) {
        showToast("Voice recognition not supported in this browser.");
        return;
    }

    const recognition = new webkitSpeechRecognition();
    recognition.lang = 'en-US';
    recognition.start();
    showToast("Listening...");

    recognition.onresult = (event) => {
        const transcript = event.results[0][0].transcript.toLowerCase();
        showToast(`Heard: ${transcript}`);

        if (transcript.includes("inventory")) {
            window.location.href = "inventory/inventory.php";
        } else if (transcript.includes("billing")) {
            window.location.href = "billing/billing.php";
        } else if (transcript.includes("return")) {
            window.location.href = "return/return.php";
        } else if (transcript.includes("admin")) {
            window.location.href = "admin/admin.php";
        } else if (transcript.includes("logout")) {
            window.location.href = "logout.php";
        } else {
            showToast("Command not recognized.");
        }
    };

    recognition.onerror = () => showToast("Error capturing voice.");
});
</script>
</body>
</html>
