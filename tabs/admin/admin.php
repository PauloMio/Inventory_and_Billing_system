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
        /* Background styling */
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

        /* Container styling */
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

        /* Header titles */
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

        /* Grid layout */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 25px;
            justify-content: center;
            margin-bottom: 35px;
        }

        /* Buttons */
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

        /* Back button */
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

        /* Responsive adjustments */
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
        <!-- Header -->
        <div class="admin-header mb-4">
            <h1>High Intensity</h1>
            <h5>Admin Control Panel</h5>
        </div>

        <!-- Grid Menu -->
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

        <!-- Back to Main Menu Button -->
        <a href="../mainMenu.php" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            Back to Main Menu
        </a>
    </div>

    <!-- Bootstrap Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
