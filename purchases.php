<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Purchase Records</title>
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
                <a href="stock.php"><i class="bi bi-box me-2"></i>Stock</a>
                <a href="workers.php"><i class="bi bi-people me-2"></i>Workers</a>
                <a href="billing.php"><i class="bi bi-receipt me-2"></i>Billing</a>
                <a href="quotations.php"><i class="bi bi-file-text me-2"></i>Quotations</a>
                <a href="sites.php"><i class="bi bi-building me-2"></i>Sites</a>
                <a href="purchases.php" class="active"><i class="bi bi-truck me-2"></i>Purchases</a>
                <a href="yearly_salary.php"><i class="bi bi-cash-stack me-2"></i>Yearly Salary</a>
                <a href="logout.php" class="mt-5"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            </div>
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between">
                    <h2>Purchase Records</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPurchaseModal">+ Record New Purchase</button>
                </div>
                
                <table class="table table-bordered mt-3">
                    <thead class="table-dark">
                        <tr><th>ID</th><th>Item</th><th>Quantity</th><th>Price</th><th>Total</th><th>Supplier</th><th>Date</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $purchases = $conn->query("SELECT p.*, s.name as item_name FROM purchases p JOIN stock s ON p.stock_id = s.id ORDER BY p.id DESC");
                        if (!$purchases) {
                            echo "<tr><td colspan='8' class='text-danger'>Error: " . $conn->error . "</td></tr>";
                        } else {
                            while($p = $purchases->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $p['id']; ?></td>
                            <td><?php echo $p['item_name']; ?></td>
                            <td><?php echo $p['quantity']; ?></td>
                            <td>₹<?php echo $p['purchase_price']; ?></td>
                            <td>₹<?php echo $p['total_amount']; ?></td>
                            <td><?php echo $p['supplier']; ?></td>
                            <td><?php echo $p['purchase_date']; ?></td>
                            <td><span class="badge bg-<?php echo $p['payment_status']=='Paid'?'success':'warning'; ?>"><?php echo $p['payment_status']; ?></span></td>
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
    
    <!-- Add Purchase Modal -->
    <div class="modal fade" id="addPurchaseModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="add_purchase.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header"><h5>Record New Purchase</h5></div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Item</label>
                            <select name="stock_id" class="form-control" required>
                                <option value="">Select Item</option>
                                <?php
                                $items = $conn->query("SELECT * FROM stock");
                                while($item = $items->fetch_assoc()) {
                                    echo "<option value='{$item['id']}'>{$item['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Quantity</label>
                            <input type="number" step="0.01" name="quantity" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Purchase Price (per unit)</label>
                            <input type="number" step="0.01" name="purchase_price" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Supplier</label>
                            <input type="text" name="supplier" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Payment Status</label>
                            <select name="payment_status" class="form-control">
                                <option value="Pending">Pending</option>
                                <option value="Paid">Paid</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Record Purchase</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>