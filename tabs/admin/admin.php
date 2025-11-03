<?php 
include '../security_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - High Intensity</title>

    <!-- Bootstrap and Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #2c3e50, #4ca1af);
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }

        .admin-container {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 50px;
            width: 90%;
            max-width: 900px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease-in-out;
        }

        .admin-container:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.4);
        }

        .admin-header h1 {
            font-weight: 700;
            font-size: 2.2rem;
            letter-spacing: 1px;
            color: #fff;
        }

        .admin-header h5 {
            font-weight: 400;
            color: #e0e0e0;
            margin-bottom: 40px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 25px;
            justify-content: center;
            margin-bottom: 35px;
        }

        .menu-btn {
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
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
            text-decoration: none;
            color: #fff;
        }

        .menu-btn i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #fff;
        }

        .menu-btn span {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background-color: rgba(255, 255, 255, 0.35);
            color: #fff;
            text-decoration: none;
            transform: translateY(-3px);
        }

        .back-btn i {
            font-size: 1.2rem;
        }

        /* Floating Mic Button */
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

        /* Toast Message */
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
            .menu-btn {
                padding: 20px;
            }
            .menu-btn i {
                font-size: 2rem;
            }
            .menu-btn span {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

    <div class="admin-container">
        <div class="admin-header mb-4">
            <h1>High Intensity</h1>
            <h5>Admin Control Panel</h5>
        </div>

        <div class="menu-grid">
            <a href="category.php" class="menu-btn">
                <i class="fa-solid fa-tags"></i>
                <span>Category</span>
            </a>

            <a href="department.php" class="menu-btn">
                <i class="fa-solid fa-building-user"></i>
                <span>Department</span>
            </a>

            <a href="remarks.php" class="menu-btn">
                <i class="fa-solid fa-comment-dots"></i>
                <span>Remarks</span>
            </a>

            <a href="user.php" class="menu-btn">
                <i class="fa-solid fa-user-gear"></i>
                <span>User</span>
            </a>

            <a href="report.php" class="menu-btn">
                <i class="fa-solid fa-chart-line"></i>
                <span>Reports</span>
            </a>
        </div>

        <a href="../mainMenu.php" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            Back to Main Menu
        </a>
    </div>

    <!-- Floating Mic Button -->
    <div class="voice-btn" id="voiceBtn" title="Click to Speak">
        <i class="fa-solid fa-microphone"></i>
    </div>

    <!-- Toast Message -->
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

                if (transcript.includes("category")) {
                    window.location.href = "category.php";
                } else if (transcript.includes("department")) {
                    window.location.href = "department.php";
                } else if (transcript.includes("remarks") || transcript.includes("remark")) {
                    window.location.href = "remarks.php";
                } else if (transcript.includes("user")) {
                    window.location.href = "user.php";
                } else if (transcript.includes("report") || transcript.includes("reports")) {
                    window.location.href = "report.php";
                } else if (transcript.includes("main menu") || transcript.includes("back")) {
                    window.location.href = "../mainMenu.php";
                } else {
                    showToast("Command not recognized.");
                }
            };

            recognition.onerror = () => showToast("Error capturing voice.");
        });
    </script>
</body>
</html>
