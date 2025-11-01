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
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Category Management</h2>

    <!-- Add Category -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="name" class="form-control" placeholder="Category Name" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="add_category" class="btn btn-success w-100">Add Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-3">
        <input type="text" id="searchBox" class="form-control" placeholder="Search by name..." value="<?= htmlspecialchars($searchQuery) ?>">
    </div>

    <!-- List Categories -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $categories->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($categories->num_rows == 0): ?>
                    <tr>
                        <td colspan="4" class="text-center">No categories found.</td>
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
