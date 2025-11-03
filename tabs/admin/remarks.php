<?php
include '../security_check.php';
include '../../database/db_connect.php';

// ===== CREATE =====
if (isset($_POST['add_remark'])) {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO remarks (name, created_at) VALUES (?, NOW())");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// ===== DELETE =====
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM remarks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ===== READ (with search) =====
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM remarks WHERE 1";
$params = [];
$types = "";

if ($searchQuery !== '') {
    $sql .= " AND name LIKE ?";
    $likeQuery = "%$searchQuery%";
    $params = [$likeQuery];
    $types = "s";
}

$sql .= " ORDER BY id DESC";
$stmt = $conn->prepare($sql);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$remarks = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Remarks Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
body {
    background-color: #f8f9fa;
    font-family: 'Poppins', sans-serif;
}
.card {
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.card-header {
    background-color: #343a40;
    color: white;
    border-radius: 15px 15px 0 0;
    font-weight: 500;
}
.voice-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background-color: rgba(52, 58, 64, 0.85);
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 65px;
    height: 65px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    cursor: pointer;
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
}
.voice-btn:hover {
    background-color: #212529;
    transform: scale(1.1);
}
.toast-msg {
    position: fixed;
    bottom: 110px;
    right: 30px;
    background-color: rgba(0,0,0,0.85);
    color: white;
    padding: 12px 18px;
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
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center mb-4 fw-bold">Remarks Management</h2>

    <div class="d-flex justify-content-between mb-3">
        <a href="admin.php" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Admin Menu
        </a>
    </div>

    <!-- Add Remark -->
    <div class="card mb-4">
        <div class="card-header">Add a New Remark</div>
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="name" class="form-control" placeholder="Enter new remark (e.g., Defective, Wrong item, etc.)" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="add_remark" class="btn btn-success w-100">
                        <i class="fa-solid fa-plus"></i> Add Remark
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body">
            <input type="text" id="searchBox" class="form-control" placeholder="Search remarks..." value="<?= htmlspecialchars($searchQuery) ?>">
        </div>
    </div>

    <!-- List Remarks -->
    <div class="card">
        <div class="card-header">Remarks List</div>
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Remark</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $remarks->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this remark?')">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($remarks->num_rows == 0): ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">No remarks found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast-msg"></div>

<!-- Voice Navigation Button -->
<button id="voiceBtn" class="voice-btn" title="Voice Navigation">
    <i class="fa-solid fa-microphone"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// === Instant Search ===
const searchBox = document.getElementById('searchBox');
searchBox.addEventListener('input', function() {
    const query = this.value.trim();
    const params = new URLSearchParams();
    if (query !== '') params.append('search', query);
    window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?' + params.toString();
});

// === Toast Notification ===
const toast = document.getElementById('toast');
function showToast(message) {
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2500);
}

// === Voice Navigation ===
const voiceBtn = document.getElementById('voiceBtn');
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
        const command = event.results[0][0].transcript.toLowerCase();
        showToast(`Heard: ${command}`);

        // Recognized commands
        if (command.includes("admin") || command.includes("menu") || command.includes("back")) {
            window.location.href = "admin.php";
        } else if (command.includes("remark") || command.includes("remarks")) {
            window.location.href = "remarks.php";
        } else if (command.includes("department")) {
            window.location.href = "department.php";
        } else {
            showToast("Command not recognized. Try saying 'Admin menu' or 'Back'.");
        }
    };

    recognition.onerror = () => {
        showToast("There was an error recognizing your voice.");
    };
});
</script>
</body>
</html>
