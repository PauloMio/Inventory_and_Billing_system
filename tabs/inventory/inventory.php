<?php
include '../security_check.php';
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
<style>
.voice-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background-color: rgba(0,0,0,0.6);
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}
.voice-btn:hover {
    background-color: rgba(0,0,0,0.8);
    transform: scale(1.1);
}
.toast-msg {
    position: fixed;
    bottom: 100px;
    right: 30px;
    background: rgba(0,0,0,0.75);
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
</style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Inventory Management</h2>
    <div class="d-flex justify-content-between mb-3">
        <a href="../mainMenu.php" class="btn btn-secondary ms-2">Back to Main Menu</a>
    </div>

    <!-- Search + Add Button -->
    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div class="d-flex align-items-center w-50">
            <input type="text" id="searchBox" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($searchQuery) ?>">
            <button id="micBtn" class="btn btn-dark ms-2"><i class="fa-solid fa-microphone"></i></button>
        </div>
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
                    <div class="mb-2"><label>Date of Arrival</label><input type="date" name="date_of_arrival" class="form-control" required></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" name="add">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Toast -->
<div id="toast" class="toast-msg"></div>

<!-- Floating Voice Button -->
<button class="voice-btn" id="voiceBtn" title="Voice Command">
    <i class="fa-solid fa-microphone"></i>
</button>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const searchBox = document.getElementById('searchBox');
searchBox.addEventListener('input', function() {
    const q = this.value.trim();
    window.location.href = 'inventory.php' + (q ? '?search=' + encodeURIComponent(q) : '');
});

const voiceBtn = document.getElementById('voiceBtn');
const micBtn = document.getElementById('micBtn');
const toast = document.getElementById('toast');

function showToast(message) {
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2500);
}

function startVoiceRecognition(forSearch = false) {
    if (!('webkitSpeechRecognition' in window)) {
        showToast("Voice recognition not supported.");
        return;
    }
    const recognition = new webkitSpeechRecognition();
    recognition.lang = 'en-US';
    recognition.start();
    showToast("Listening...");

    recognition.onresult = (event) => {
        const transcript = event.results[0][0].transcript.toLowerCase();
        showToast(`Heard: ${transcript}`);

        if (forSearch) {
            searchBox.value = transcript;
            const q = transcript.trim();
            window.location.href = 'inventory.php' + (q ? '?search=' + encodeURIComponent(q) : '');
        } else {
            if (transcript.includes("main menu") || transcript.includes("go back")) {
                window.location.href = "../mainMenu.php";
            } else {
                showToast("Command not recognized.");
            }
        }
    };

    recognition.onerror = () => showToast("Error capturing voice.");
}

// Speech-to-text search
micBtn.addEventListener('click', () => startVoiceRecognition(true));

// Navigation voice command
voiceBtn.addEventListener('click', () => startVoiceRecognition(false));
</script>
</body>
</html>
