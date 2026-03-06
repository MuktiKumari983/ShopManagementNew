<?php
require_once 'includes/auth.php';
$worker_id = $_GET['id'] ?? 0;
$worker = $conn->query("SELECT * FROM workers WHERE id=$worker_id")->fetch_assoc();
if (!$worker) {
    header('Location: workers.php');
    exit();
}

// Handle marking attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_attendance'])) {
    $date = $_POST['attendance_date'];
    $status = $_POST['status'];
    // Check if already marked
    $check = $conn->query("SELECT id FROM attendance WHERE worker_id=$worker_id AND attendance_date='$date'");
    if ($check->num_rows > 0) {
        $conn->query("UPDATE attendance SET status='$status' WHERE worker_id=$worker_id AND attendance_date='$date'");
    } else {
        $conn->query("INSERT INTO attendance (worker_id, attendance_date, status) VALUES ($worker_id, '$date', '$status')");
    }
    $_SESSION['success'] = "Attendance marked for $date";
}

// Get current month attendance
$month = date('Y-m');
$attendance = $conn->query("SELECT * FROM attendance WHERE worker_id=$worker_id AND DATE_FORMAT(attendance_date, '%Y-%m') = '$month' ORDER BY attendance_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Worker Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2>Attendance for <?php echo $worker['name']; ?></h2>
        <form method="POST" class="row g-3 mb-4">
            <div class="col-auto">
                <label>Date</label>
                <input type="date" name="attendance_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="col-auto">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                </select>
            </div>
            <div class="col-auto align-self-end">
                <button type="submit" name="mark_attendance" class="btn btn-primary">Mark Attendance</button>
            </div>
        </form>
        
        <h4>Attendance this month</h4>
        <table class="table">
            <thead><tr><th>Date</th><th>Status</th></tr></thead>
            <tbody>
                <?php while($row = $attendance->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['attendance_date']; ?></td>
                    <td><span class="badge bg-<?php echo $row['status']=='Present'?'success':'danger'; ?>"><?php echo $row['status']; ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="workers.php" class="btn btn-secondary">Back</a>
    </div>
</body>
</html>