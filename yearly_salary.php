<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Yearly Salary</title>
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
                <a href="purchases.php"><i class="bi bi-truck me-2"></i>Purchases</a>
                <a href="yearly_salary.php" class="active"><i class="bi bi-cash-stack me-2"></i>Yearly Salary</a>
                <a href="logout.php" class="mt-5"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            </div>
            <div class="col-md-10 p-4">
                <h2>Yearly Salary Summary</h2>
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-auto">
                        <label>Select Year</label>
                        <select name="year" class="form-control">
                            <?php
                            $current_year = date('Y');
                            for ($y = $current_year; $y >= $current_year-5; $y--) {
                                $selected = (isset($_GET['year']) && $_GET['year'] == $y) ? 'selected' : '';
                                echo "<option value='$y' $selected>$y</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-auto align-self-end">
                        <button type="submit" class="btn btn-primary">Show</button>
                    </div>
                </form>
                
                <?php
                $year = $_GET['year'] ?? date('Y');
                $workers = $conn->query("SELECT * FROM workers ORDER BY name");
                if (!$workers) {
                    echo "<div class='alert alert-danger'>Error fetching workers: " . $conn->error . "</div>";
                } else {
                ?>
                
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Worker</th>
                            <?php for ($m=1; $m<=12; $m++): ?>
                            <th><?php echo date('M', mktime(0,0,0,$m,1)); ?></th>
                            <?php endfor; ?>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($worker = $workers->fetch_assoc()): 
                            $total_year = 0;
                        ?>
                        <tr>
                            <td><?php echo $worker['name']; ?></td>
                            <?php for ($m=1; $m<=12; $m++): 
                                $salary = $conn->query("SELECT net_paid FROM salary_payments WHERE worker_id={$worker['id']} AND year=$year AND month=$m")->fetch_assoc();
                                $amount = $salary['net_paid'] ?? 0;
                                $total_year += $amount;
                            ?>
                            <td>₹<?php echo number_format($amount, 2); ?></td>
                            <?php endfor; ?>
                            <td><strong>₹<?php echo number_format($total_year, 2); ?></strong></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php } ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>