<?php
include '../../database/db_connect.php';

// Load category list for dropdown
$categoryQuery = $conn->query("SELECT name FROM category ORDER BY name ASC");
$categories = [];
while ($row = $categoryQuery->fetch_assoc()) {
    $categories[] = $row['name'];
}

// Inventory search
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM inventory WHERE 1";
$params = [];
$types = "";

if ($searchQuery !== '') {
    $sql .= " AND (product_number LIKE ? OR name LIKE ? OR category LIKE ? OR brand LIKE ? OR supplier LIKE ?)";
    $like = "%$searchQuery%";
    $params = [$like, $like, $like, $like, $like];
    $types = "sssss";
}
$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inventory Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Inventory Management</h2>

    <!-- Search + Add Button -->
    <div class="d-flex justify-content-between mb-3">
        <input type="text" id="searchBox" class="form-control w-50" placeholder="Search..." value="<?= htmlspecialchars($searchQuery) ?>">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">Add Inventory</button>
    </div>

    <!-- Inventory Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Product Number</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Supplier</th>
                    <th>Current Stock</th>
                    <th>Total Stock</th>
                    <th>Price</th>
                    <th>Selling Price</th>
                    <th>Date of Arrival</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['product_number']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td><?= htmlspecialchars($row['brand']) ?></td>
                    <td><?= htmlspecialchars($row['supplier']) ?></td>
                    <td><?= $row['current_stock'] ?></td>
                    <td><?= $row['total_stock'] ?></td>
                    <td><?= $row['price'] ?></td>
                    <td><?= $row['selling_price'] ?></td>
                    <td><?= $row['date_of_arrival'] ?></td>
                    <td>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                        <a href="inventory_crud.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="inventory_crud.php" method="POST" onsubmit="return confirm('Are you sure you want to update this item?');">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Inventory</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <div class="mb-2"><label>Product Number</label><input type="text" name="product_number" class="form-control" value="<?= htmlspecialchars($row['product_number']) ?>" required></div>
                                    <div class="mb-2"><label>Name</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required></div>
                                    <div class="mb-2"><label>Description</label><textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($row['description']) ?></textarea></div>

                                    <div class="mb-2">
                                        <label>Category</label>
                                        <select name="category" class="form-select">
                                            <option value="">-- Select Category --</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= htmlspecialchars($cat) ?>" <?= ($row['category'] == $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-2"><label>Brand</label><input type="text" name="brand" class="form-control" value="<?= htmlspecialchars($row['brand']) ?>"></div>
                                    <div class="mb-2"><label>Supplier</label><input type="text" name="supplier" class="form-control" value="<?= htmlspecialchars($row['supplier']) ?>"></div>
                                    <div class="mb-2"><label>Current Stock</label><input type="number" name="current_stock" class="form-control" value="<?= $row['current_stock'] ?>"></div>
                                    <div class="mb-2"><label>Total Stock</label><input type="number" name="total_stock" class="form-control" value="<?= $row['total_stock'] ?>"></div>
                                    <div class="mb-2"><label>Price</label><input type="number" step="0.01" name="price" class="form-control" value="<?= $row['price'] ?>"></div>
                                    <div class="mb-2"><label>Selling Price</label><input type="number" step="0.01" name="selling_price" class="form-control" value="<?= $row['selling_price'] ?>"></div>
                                    <div class="mb-2"><label>Barcode</label><input type="text" name="barcode" class="form-control" value="<?= htmlspecialchars($row['barcode']) ?>"></div>
                                    <div class="mb-2"><label>Date of Arrival</label><input type="date" name="date_of_arrival" class="form-control" value="<?= $row['date_of_arrival'] ?>"></div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" name="update">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="inventory_crud.php" method="POST" onsubmit="return confirm('Are you sure you want to add this item?');">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2"><label>Product Number</label><input type="text" name="product_number" class="form-control" required></div>
                    <div class="mb-2"><label>Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-2"><label>Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>

                    <div class="mb-2">
                        <label>Category</label>
                        <select name="category" class="form-select">
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-2"><label>Brand</label><input type="text" name="brand" class="form-control"></div>
                    <div class="mb-2"><label>Supplier</label><input type="text" name="supplier" class="form-control"></div>
                    <div class="mb-2"><label>Current Stock</label><input type="number" name="current_stock" class="form-control"></div>
                    <div class="mb-2"><label>Total Stock</label><input type="number" name="total_stock" class="form-control"></div>
                    <div class="mb-2"><label>Price</label><input type="number" step="0.01" name="price" class="form-control"></div>
                    <div class="mb-2"><label>Selling Price</label><input type="number" step="0.01" name="selling_price" class="form-control"></div>
                    <div class="mb-2"><label>Barcode</label><input type="text" name="barcode" class="form-control"></div>
                    <div class="mb-2"><label>Date of Arrival</label><input type="date" name="date_of_arrival" class="form-control"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" name="add">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const searchBox = document.getElementById('searchBox');
searchBox.addEventListener('input', function() {
    const q = this.value.trim();
    window.location.href = 'inventory.php' + (q ? '?search=' + encodeURIComponent(q) : '');
});
</script>
</body>
</html>
