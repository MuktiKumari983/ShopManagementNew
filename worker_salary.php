<?php
require_once 'includes/auth.php';
$worker_id = $_GET['id'] ?? 0;
$worker = $conn->query("SELECT * FROM workers WHERE id=$worker_id")->fetch_assoc();
if (!$worker) {
    header('Location: workers.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_advance'])) {
    $amount = $_POST['amount'];
    $advance_date = $_POST['advance_date'];
    $note = $_POST['note'];
    $stmt = $conn->prepare("INSERT INTO advances (worker_id, amount, advance_date, note) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $worker_id, $amount, $advance_date, $note);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_salary'])) {
    $year = $_POST['year'];
    $month = $_POST['month'];
    $present_days = $_POST['present_days'];
    $absent_days = $_POST['absent_days'];
    $gross_salary = $_POST['gross_salary'];
    $advance_deduction = $_POST['advance_deduction'];
    $net_paid = $_POST['net_paid'];
    $payment_date = $_POST['payment_date'];
    
    $stmt = $conn->prepare("INSERT INTO salary_payments (worker_id, year, month, present_days, absent_days, gross_salary, advance_deduction, net_paid, payment_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Paid')");
    $stmt->bind_param("iiiidddds", $worker_id, $year, $month, $present_days, $absent_days, $gross_salary, $advance_deduction, $net_paid, $payment_date);
    $stmt->execute();
}

$month_start = date('Y-m-01');
$month_end = date('Y-m-t');
$advances = $conn->query("SELECT SUM(amount) as total FROM advances WHERE worker_id=$worker_id AND advance_date BETWEEN '$month_start' AND '$month_end'");
$advance_total = $advances->fetch_assoc()['total'] ?? 0;

$present = $conn->query("SELECT COUNT(*) as c FROM attendance WHERE worker_id=$worker_id AND status='Present' AND attendance_date BETWEEN '$month_start' AND '$month_end'")->fetch_assoc()['c'];
$absent = date('t') - $present;
$daily_wage = $worker['monthly_salary'] / date('t');
$gross = $present * $daily_wage;
$net = $gross - $advance_total;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Worker Salary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2>Salary for <?php echo $worker['name']; ?></h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Current Month Summary</div>
                    <div class="card-body">
                        <p>Present Days: <?php echo $present; ?></p>
                        <p>Absent Days: <?php echo $absent; ?></p>
                        <p>Monthly Salary: ₹<?php echo $worker['monthly_salary']; ?></p>
                        <p>Daily Wage: ₹<?php echo number_format($daily_wage, 2); ?></p>
                        <p>Gross Salary: ₹<?php echo number_format($gross, 2); ?></p>
                        <p>Advance Taken: ₹<?php echo number_format($advance_total, 2); ?></p>
                        <p><strong>Net Payable: ₹<?php echo number_format($net, 2); ?></strong></p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">Add Advance</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="add_advance" value="1">
                            <div class="mb-3">
                                <label>Amount</label>
                                <input type="number" step="0.01" name="amount" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Date</label>
                                <input type="date" name="advance_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Note</label>
                                <input type="text" name="note" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-warning">Add Advance</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Pay Salary</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="pay_salary" value="1">
                            <div class="mb-3">
                                <label>Year</label>
                                <input type="number" name="year" class="form-control" value="<?php echo date('Y'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Month (1-12)</label>
                                <input type="number" name="month" class="form-control" value="<?php echo date('n'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Present Days</label>
                                <input type="number" name="present_days" class="form-control" value="<?php echo $present; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label>Absent Days</label>
                                <input type="number" name="absent_days" class="form-control" value="<?php echo $absent; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label>Gross Salary</label>
                                <input type="number" step="0.01" name="gross_salary" class="form-control" value="<?php echo $gross; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label>Advance Deduction</label>
                                <input type="number" step="0.01" name="advance_deduction" class="form-control" value="<?php echo $advance_total; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label>Net Paid</label>
                                <input type="number" step="0.01" name="net_paid" class="form-control" value="<?php echo $net; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label>Payment Date</label>
                                <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-success">Pay Salary</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <a href="workers.php" class="btn btn-secondary mt-3">Back</a>
    </div>
</body>
</html>