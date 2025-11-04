<?php
include '../security_check.php';
include '../../database/db_connect.php';

// ===== READ USERS with search =====
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT * FROM users WHERE 1";
$params = [];
$types = "";

if ($searchQuery !== '') {
    $sql .= " AND (name LIKE ? OR email LIKE ? OR department LIKE ?)";
    $likeQuery = "%$searchQuery%";
    $params = [$likeQuery, $likeQuery, $likeQuery];
    $types = "sss";
}

$sql .= " ORDER BY id DESC";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Fetch departments for dropdown
$deptResult = $conn->query("SELECT * FROM department ORDER BY department ASC");
$departments = [];
while ($row = $deptResult->fetch_assoc()) {
    $departments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { background-color: #f8f9fa; font-family: 'Poppins', sans-serif; }
.modal-header { background-color: #343a40; color: white; }
.table th, .table td { vertical-align: middle; }
.eye-toggle { cursor: pointer; position: absolute; right: 15px; top: 35px; color: #6c757d; }
.position-relative { position: relative; }

/* Floating mic button */
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

/* Toast message for feedback */
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
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>User Management</h2>
    </div>

    <div class="d-flex justify-content-between mb-3">
        <a href="admin.php" class="btn btn-secondary ms-2"><i class="fa-solid fa-arrow-left"></i> Back to Admin Menu</a>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="fa-solid fa-user-plus"></i> Add New User</button>
    </div>

        <!-- Search Users -->
    <div class="mb-3">
        <input type="text" id="searchBox" class="form-control" placeholder="Search by Name, Email, or Department..." value="<?= htmlspecialchars($searchQuery) ?>">
    </div>


    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['department']) ?></td>
                        <td><?= $row['status'] ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $row['id'] ?>">Edit</button>
                            <a href="user_function.php?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editUserModal<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="user_function.php" method="POST">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Name</label>
                                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Email</label>
                                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>" required>
                                        </div>
                                        <div class="mb-3 position-relative">
                                            <label>Password (leave blank to keep current)</label>
                                            <input type="password" name="password" class="form-control" id="editPassword<?= $row['id'] ?>">
                                            <i class="fa-solid fa-eye eye-toggle" onclick="togglePassword('editPassword<?= $row['id'] ?>')"></i>
                                        </div>
                                        <div class="mb-3">
                                            <label>Department</label>
                                            <select name="department" class="form-select" required>
                                                <option value="" disabled>Select Department</option>
                                                <?php foreach($departments as $dept): ?>
                                                    <option value="<?= htmlspecialchars($dept['department']) ?>" <?= $dept['department'] == $row['department'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($dept['department']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label>Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="Active" <?= $row['status']=='Active'?'selected':'' ?>>Active</option>
                                                <option value="Inactive" <?= $row['status']=='Inactive'?'selected':'' ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="edit_user" class="btn btn-primary">Update User</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php if ($result->num_rows == 0): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">No users found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="user_function.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3 position-relative">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" id="addPassword" required>
                        <i class="fa-solid fa-eye eye-toggle" onclick="togglePassword('addPassword')"></i>
                    </div>
                    <div class="mb-3">
                        <label>Department</label>
                        <select name="department" class="form-select" required>
                            <option value="" disabled selected>Select Department</option>
                            <?php foreach($departments as $dept): ?>
                                <option value="<?= htmlspecialchars($dept['department']) ?>"><?= htmlspecialchars($dept['department']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_user" class="btn btn-success">Add User</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Toast + Mic Button -->
<div id="toast" class="toast-msg"></div>
<button id="voiceBtn" class="voice-btn" title="Voice Navigation">
    <i class="fa-solid fa-microphone"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// === Toast function ===
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
        showToast("Voice recognition not supported on this browser.");
        return;
    }

    const recognition = new webkitSpeechRecognition();
    recognition.lang = 'en-US';
    recognition.start();
    showToast("Listening...");

    recognition.onresult = (event) => {
        const command = event.results[0][0].transcript.toLowerCase();
        showToast(`Heard: ${command}`);

        if (command.includes("admin") || command.includes("menu") || command.includes("back")) {
            window.location.href = "admin.php";
        } else {
            showToast("Command not recognized. Try saying 'Admin menu' or 'Back'.");
        }
    };

    recognition.onerror = () => showToast("Error recognizing voice command.");
});

// --- Instant Search ---
const searchBox = document.getElementById('searchBox');
searchBox.addEventListener('input', function() {
    const query = this.value.trim();
    const params = new URLSearchParams();
    if(query !== '') params.append('search', query);
    window.location.href = 'user.php?' + params.toString();
});

</script>
</body>
</html>
