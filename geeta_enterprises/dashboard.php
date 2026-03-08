<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'includes/auth.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Geeta Enterprises</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .stat-card { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: transform 0.3s; cursor: pointer; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .stat-icon { font-size: 2.5rem; color: #667eea; }
        .trend-up { color: #10b981; }
        .trend-down { color: #ef4444; }
        .low-stock-critical { background: #fee2e2; border-left: 4px solid #ef4444; }
        .recent-item { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        .recent-item:hover { background: #f9fafb; }
        .sidebar { min-height: 100vh; background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); }
        .sidebar a { color: #cbd5e1; padding: 15px 20px; display: block; text-decoration: none; }
        .sidebar a:hover { background: #334155; color: white; }
        .sidebar a.active { background: #2563eb; color: white; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0 sidebar">
                <div class="p-4">
                    <h4 class="text-white">⚙️ GE</h4>
                </div>
                <a href="dashboard.php" class="active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                <a href="stock.php"><i class="bi bi-box me-2"></i>Stock</a>
                <a href="workers.php"><i class="bi bi-people me-2"></i>Workers</a>
                <a href="billing.php"><i class="bi bi-receipt me-2"></i>Billing</a>
                <a href="quotations.php"><i class="bi bi-file-text me-2"></i>Quotations</a>
                <a href="sites.php"><i class="bi bi-building me-2"></i>Sites</a>
                <a href="purchases.php"><i class="bi bi-truck me-2"></i>Purchases</a>
                <a href="yearly_salary.php"><i class="bi bi-cash-stack me-2"></i>Yearly Salary</a>
                <a href="logout.php" class="mt-5"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Dashboard</h2>
                    <div class="text-muted"><?php echo date('l, d F Y'); ?></div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card" onclick="location.href='stock.php'">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Stock Value</h6>
                                    <h3>₹<?php echo number_format(getStockValue($conn), 2); ?></h3>
                                    <span class="trend-up"><i class="bi bi-arrow-up"></i> <?php 
                                        $res = $conn->query("SELECT COUNT(*) as c FROM stock");
                                        echo $res->fetch_assoc()['c']; ?> items</span>
                                </div>
                                <div class="stat-icon"><i class="bi bi-box"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card" onclick="location.href='workers.php'">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Active Workers</h6>
                                    <h3><?php 
                                        $res = $conn->query("SELECT COUNT(*) as c FROM workers");
                                        echo $res->fetch_assoc()['c'];
                                    ?></h3>
                                    <span class="trend-up"><i class="bi bi-arrow-up"></i> +<?php 
                                        $res = $conn->query("SELECT COUNT(*) as c FROM workers WHERE MONTH(joined_date)=MONTH(CURRENT_DATE)");
                                        echo $res->fetch_assoc()['c']; ?> this month</span>
                                </div>
                                <div class="stat-icon"><i class="bi bi-people"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card" onclick="location.href='billing.php'">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Today's Income</h6>
                                    <h3>₹<?php 
                                        $today = date('Y-m-d');
                                        $res = $conn->query("SELECT SUM(total) as total FROM bills WHERE DATE(bill_date)='$today'");
                                        echo number_format($res->fetch_assoc()['total'] ?? 0, 2);
                                    ?></h3>
                                    <span class="trend-up"><i class="bi bi-arrow-up"></i> <?php 
                                        $res = $conn->query("SELECT COUNT(*) as c FROM bills WHERE DATE(bill_date)='$today'");
                                        echo $res->fetch_assoc()['c']; ?> transactions</span>
                                </div>
                                <div class="stat-icon"><i class="bi bi-cash"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card" onclick="location.href='workers.php'">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted">Labour Payable</h6>
                                    <h3>₹<?php 
                                        $res = $conn->query("SELECT SUM(net_paid) as total FROM salary_payments WHERE status='Pending'");
                                        echo number_format($res->fetch_assoc()['total'] ?? 0, 2);
                                    ?></h3>
                                    <span class="trend-down"><i class="bi bi-arrow-down"></i> <?php 
                                        $res = $conn->query("SELECT COUNT(DISTINCT worker_id) as c FROM salary_payments WHERE status='Pending'");
                                        echo $res->fetch_assoc()['c']; ?> workers</span>
                                </div>
                                <div class="stat-icon"><i class="bi bi-person-workspace"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Recent Activity -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-white d-flex justify-content-between">
                                <h5>Recent Activity</h5>
                                <a href="#" class="text-decoration-none">View All →</a>
                            </div>
                            <div class="card-body p-0">
                                <?php
                                $activities = $conn->query("
                                    (SELECT CONCAT('Sale - ', c.name) as descr, total as amount, 'Sales' as type, bill_date as created_at FROM bills JOIN customers c ON bills.customer_id = c.id ORDER BY bill_date DESC LIMIT 3)
                                    UNION ALL
                                    (SELECT CONCAT('Purchase - ', s.name) as descr, purchase_amount as amount, 'Stock Purchase' as type, purchase_date as created_at FROM purchases JOIN stock s ON purchases.stock_id = s.id ORDER BY purchase_date DESC LIMIT 3)
                                    ORDER BY created_at DESC LIMIT 6
                                ");
                                while($act = $activities->fetch_assoc()):
                                ?>
                                <div class="recent-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-light text-dark me-2"><?php echo $act['type']; ?></span>
                                        <strong><?php echo $act['descr']; ?></strong>
                                    </div>
                                    <div>
                                        <span class="fw-bold">₹<?php echo number_format($act['amount'], 2); ?></span>
                                        <small class="text-muted ms-3"><?php echo date('h:i A', strtotime($act['created_at'])); ?></small>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Low Stock Alerts -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5>Low Stock Alerts</h5>
                            </div>
                            <div class="card-body p-0">
                                <?php
                                $lowStock = getLowStockItems($conn, 5);
                                while($item = $lowStock->fetch_assoc()):
                                ?>
                                <div class="recent-item low-stock-critical">
                                    <strong><?php echo $item['name']; ?></strong>
                                    <div class="d-flex justify-content-between">
                                        <span><?php echo $item['quantity']; ?>/<?php echo $item['min_stock']; ?> <?php echo $item['unit']; ?></span>
                                        <span class="badge bg-danger">Critical</span>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Labour Summary Table -->
                <div class="card mt-4">
                    <div class="card-header bg-white d-flex justify-content-between">
                        <h5>Labour Summary</h5>
                        <span>Payable: ₹<?php 
                            $res = $conn->query("SELECT SUM(net_paid) as total FROM salary_payments WHERE status='Pending'");
                            echo number_format($res->fetch_assoc()['total'] ?? 0, 2);
                        ?></span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Worker</th><th>Role</th><th>Attendance</th><th>Salary</th><th>Advance</th><th>Net</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $workers = $conn->query("SELECT w.*, 
                                    (SELECT COUNT(*) FROM attendance WHERE worker_id=w.id AND status='Present' AND MONTH(attendance_date)=MONTH(CURRENT_DATE)) as present,
                                    (SELECT SUM(amount) FROM advances WHERE worker_id=w.id AND MONTH(advance_date)=MONTH(CURRENT_DATE)) as advance_this_month
                                    FROM workers w LIMIT 5");
                                while($w = $workers->fetch_assoc()):
                                    $net = $w['monthly_salary'] - ($w['advance_this_month'] ?? 0);
                                ?>
                                <tr>
                                    <td><i class="bi bi-person-circle me-2"></i><?php echo $w['name']; ?></td>
                                    <td><?php echo $w['role']; ?></td>
                                    <td><?php echo $w['present'] ?? 0; ?>/<?php echo date('t'); ?></td>
                                    <td>₹<?php echo number_format($w['monthly_salary'], 2); ?></td>
                                    <td>₹<?php echo number_format($w['advance_this_month'] ?? 0, 2); ?></td>
                                    <td>₹<?php echo number_format($net, 2); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>