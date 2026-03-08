<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Billing</title>
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
                <a href="billing.php" class="active"><i class="bi bi-receipt me-2"></i>Billing</a>
                <a href="quotations.php"><i class="bi bi-file-text me-2"></i>Quotations</a>
                <a href="sites.php"><i class="bi bi-building me-2"></i>Sites</a>
                <a href="purchases.php"><i class="bi bi-truck me-2"></i>Purchases</a>
                <a href="yearly_salary.php"><i class="bi bi-cash-stack me-2"></i>Yearly Salary</a>
                <a href="logout.php" class="mt-5"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            </div>
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between">
                    <h2>Bills</h2>
                    <a href="create_bill.php" class="btn btn-primary">+ Create New Bill</a>
                </div>
                <table class="table table-bordered mt-3">
                    <thead class="table-dark">
                        <tr><th>Bill No</th><th>Customer</th><th>Date</th><th>Total</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $bills = $conn->query("SELECT b.*, c.name as customer_name FROM bills b LEFT JOIN customers c ON b.customer_id = c.id ORDER BY b.id DESC");
                        if (!$bills) {
                            echo "<tr><td colspan='5' class='text-danger'>Error: " . $conn->error . "</td></tr>";
                        } else {
                            while($bill = $bills->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $bill['bill_no']; ?></td>
                            <td><?php echo $bill['customer_name']; ?></td>
                            <td><?php echo $bill['bill_date']; ?></td>
                            <td>₹<?php echo $bill['total']; ?></td>
                            <td>
                                <a href="view_bill.php?id=<?php echo $bill['id']; ?>" class="btn btn-sm btn-info">View</a>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>