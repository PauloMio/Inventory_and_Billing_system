<?php
include '../../database/db_connect.php';

// ===== READ USERS =====
$sql = "SELECT * FROM users ORDER BY id DESC";
$result = $conn->query($sql);

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
body { background-color: #f8f9fa; }
.modal-header { background-color: #343a40; color: white; }
.table th, .table td { vertical-align: middle; }
.eye-toggle { cursor: pointer; position: absolute; right: 15px; top: 35px; color: #6c757d; }
.position-relative { position: relative; }
</style>
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>User Management</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>
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
                                        <button type="submit" name="update_user" class="btn btn-primary">Update User</button>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
