<?php
include '../security_check.php';
include '../../database/db_connect.php';

// ===== CREATE =====
if (isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO category (name, created_at) VALUES (?, NOW())");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// ===== DELETE =====
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM category WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ===== READ with search =====
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM category WHERE 1";
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
$categories = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Category Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
body {
    background-color: #f0f2f5;
    font-family: Arial, sans-serif;
}
.card {
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.card-header {
    background-color: #343a40;
    color: white;
    border-radius: 15px 15px 0 0;
}
.voice-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    border: none;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}
.voice-btn:hover {
    background-color: rgba(0,0,0,0.9);
    transform: scale(1.1);
}
.toast-msg {
    position: fixed;
    bottom: 100px;
    right: 30px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 10px 20px;
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
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Category Management</h2>

    <div class="d-flex justify-content-between mb-3">
        <a href="admin.php" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Admin Menu
        </a>
    </div>

    <!-- Add Category Form -->
    <div class="card mb-4">
        <div class="card-header">Add a New Category</div>
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="name" class="form-control" placeholder="Enter Category Name" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="add_category" class="btn btn-primary w-100">
                        <i class="fa-solid fa-plus"></i> Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <input type="text" id="searchBox" class="form-control" placeholder="Search by category name..." value="<?= htmlspecialchars($searchQuery) ?>">
        </div>
    </div>

    <!-- Category List -->
    <div class="card">
        <div class="card-header">Category List</div>
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Category Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $categories->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?')">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($categories->num_rows == 0): ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">No categories found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Toast Message -->
<div id="toast" class="toast-msg"></div>

<!-- Voice Button -->
<button class="voice-btn" id="voiceBtn" title="Voice Navigation">
    <i class="fa-solid fa-microphone"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// === Search Filter ===
const searchBox = document.getElementById('searchBox');
searchBox.addEventListener('input', function() {
    const query = this.value.trim();
    const params = new URLSearchParams();
    if (query !== '') params.append('search', query);
    window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?' + params.toString();
});

// === Toast Notification ===
const toast = document.getElementById('toast');
function showToast(msg) {
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2500);
}

// === Voice Command Navigation ===
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
        const transcript = event.results[0][0].transcript.toLowerCase();
        showToast(`Heard: ${transcript}`);

        // Commands
        if (transcript.includes("admin") || transcript.includes("admin menu") || transcript.includes("go to admin")) {
            window.location.href = "admin.php";
        } else if (transcript.includes("back")) {
            window.location.href = "admin.php";
        } else if (transcript.includes("category") || transcript.includes("categories")) {
            window.location.href = "category.php";
        } else {
            showToast("Command not recognized. Try saying 'Admin menu' or 'Back'.");
        }
    };

    recognition.onerror = () => showToast("Error recognizing voice input.");
});
</script>
</body>
</html>
