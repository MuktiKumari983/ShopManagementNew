<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .sidebar { min-height: 100vh; background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); }
        .sidebar a { color: #cbd5e1; padding: 15px 20px; display: block; text-decoration: none; }
        .sidebar a:hover { background: #334155; color: white; }
        .sidebar a.active { background: #2563eb; color: white; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 p-0 sidebar">
                <div class="p-4"><h4 class="text-white">⚙️ GE</h4></div>
                <a href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                <a href="stock.php" class="active"><i class="bi bi-box me-2"></i>Stock</a>
                <a href="workers.php"><i class="bi bi-people me-2"></i>Workers</a>
                <a href="billing.php"><i class="bi bi-receipt me-2"></i>Billing</a>
                <a href="quotations.php"><i class="bi bi-file-text me-2"></i>Quotations</a>
                <a href="sites.php"><i class="bi bi-building me-2"></i>Sites</a>
                <a href="purchases.php"><i class="bi bi-truck me-2"></i>Purchases</a>
                <a href="yearly_salary.php"><i class="bi bi-cash-stack me-2"></i>Yearly Salary</a>
                <a href="logout.php" class="mt-5"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            </div>
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between">
                    <h2>Stock Management</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">+ Add New Item</button>
                </div>
                
                <table class="table table-bordered table-hover mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th><th>Name</th><th>Category</th><th>Quantity</th><th>Unit</th><th>Price</th><th>Supplier</th><th>Purchase Date</th><th>Pending Bill</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT s.*, c.name as category_name, c.unit FROM stock s JOIN categories c ON s.category_id = c.id ORDER BY s.id DESC");
                        if (!$result) {
                            echo "<tr><td colspan='10' class='text-danger'>Error: " . $conn->error . "</td></tr>";
                        } else {
                            while($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['category_name']; ?></td>
                            <td class="<?php echo ($row['quantity'] <= $row['min_stock']) ? 'text-danger fw-bold' : ''; ?>"><?php echo $row['quantity']; ?></td>
                            <td><?php echo $row['unit']; ?></td>
                            <td>₹<?php echo $row['price']; ?></td>
                            <td><?php echo $row['supplier']; ?></td>
                            <td><?php echo $row['purchase_date']; ?></td>
                            <td>₹<?php echo $row['pending_bill']; ?></td>
                            <td>
                                <a href="edit_stock.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <a href="sell_stock.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success"><i class="bi bi-cart"></i> Sell</a>
                                <a href="delete_stock.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this item?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Add Stock Modal -->
    <div class="modal fade" id="addStockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="add_stock.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Stock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Item Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Category</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Select</option>
                                <?php
                                $cats = $conn->query("SELECT * FROM categories");
                                while($cat = $cats->fetch_assoc()) {
                                    echo "<option value='{$cat['id']}'>{$cat['name']} ({$cat['unit']})</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Quantity</label>
                            <input type="number" step="0.01" name="quantity" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Price per Unit</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Minimum Stock Alert</label>
                            <input type="number" step="0.01" name="min_stock" class="form-control" value="5">
                        </div>
                        <div class="mb-3">
                            <label>Supplier</label>
                            <input type="text" name="supplier" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Purchase Amount</label>
                            <input type="number" step="0.01" name="purchase_amount" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Pending Bill</label>
                            <input type="number" step="0.01" name="pending_bill" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>