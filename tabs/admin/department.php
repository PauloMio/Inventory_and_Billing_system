<?php
include '../../database/db_connect.php';

// ===== CREATE =====
if (isset($_POST['add_department'])) {
    $department = trim($_POST['department']);
    if (!empty($department)) {
        $stmt = $conn->prepare("INSERT INTO department (department, created_at) VALUES (?, NOW())");
        $stmt->bind_param("s", $department);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// ===== DELETE =====
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM department WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ===== READ with search =====
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM department WHERE 1";
$params = [];
$types = "";

if ($searchQuery !== '') {
    $sql .= " AND department LIKE ?";
    $likeQuery = "%$searchQuery%";
    $params = [$likeQuery];
    $types = "s";
}

$sql .= " ORDER BY id DESC";
$stmt = $conn->prepare($sql);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$departments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Department Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Department Management</h2>

    <!-- Add Department -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="department" class="form-control" placeholder="Department Name" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="add_department" class="btn btn-success w-100">Add Department</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-3">
        <input type="text" id="searchBox" class="form-control" placeholder="Search by department..." value="<?= htmlspecialchars($searchQuery) ?>">
    </div>

    <!-- List Departments -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Department</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $departments->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['department']) ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this department?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($departments->num_rows == 0): ?>
                    <tr>
                        <td colspan="3" class="text-center">No departments found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const searchBox = document.getElementById('searchBox');
searchBox.addEventListener('input', function() {
    const query = this.value.trim();
    const params = new URLSearchParams();
    if(query !== '') params.append('search', query);
    window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?' + params.toString();
});
</script>
</body>
</html>
