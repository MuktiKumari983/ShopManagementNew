<?php require_once 'includes/auth.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Worker Management</title>
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
                <a href="workers.php" class="active"><i class="bi bi-people me-2"></i>Workers</a>
                <a href="billing.php"><i class="bi bi-receipt me-2"></i>Billing</a>
                <a href="quotation.php"><i class="bi bi-file-text me-2"></i>Quotations</a>
                <a href="sites.php"><i class="bi bi-building me-2"></i>Sites</a>
                <a href="purchases.php"><i class="bi bi-truck me-2"></i>Purchases</a>
                <a href="yearly_salary.php"><i class="bi bi-cash-stack me-2"></i>Yearly Salary</a>
                <a href="logout.php" class="mt-5"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            </div>
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between">
                    <h2>Worker Management</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWorkerModal">+ Add Worker</button>
                </div>
                
                <table class="table table-bordered table-hover mt-3">
                    <thead class="table-dark">
                        <tr><th>ID</th><th>Name</th><th>Role</th><th>Phone</th><th>Monthly Salary</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $workers = $conn->query("SELECT * FROM workers ORDER BY id DESC");
                        while($w = $workers->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $w['id']; ?></td>
                            <td><?php echo $w['name']; ?></td>
                            <td><?php echo $w['role']; ?></td>
                            <td><?php echo $w['phone']; ?></td>
                            <td>₹<?php echo $w['monthly_salary']; ?></td>
                            <td>
                                <a href="worker_attendance.php?id=<?php echo $w['id']; ?>" class="btn btn-sm btn-info">Attendance</a>
                                <a href="worker_salary.php?id=<?php echo $w['id']; ?>" class="btn btn-sm btn-success">Salary</a>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#uploadDocModal<?php echo $w['id']; ?>">Upload Doc</button>
                                <a href="delete_worker.php?id=<?php echo $w['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this worker?')">Delete</a>
                            </td>
                        </tr>
                        <!-- Document Upload Modal -->
                        <div class="modal fade" id="uploadDocModal<?php echo $w['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="upload_document.php" method="POST" enctype="multipart/form-data">
                                    <div class="modal-content">
                                        <div class="modal-header"><h5>Upload Document for <?php echo $w['name']; ?></h5></div>
                                        <div class="modal-body">
                                            <input type="hidden" name="worker_id" value="<?php echo $w['id']; ?>">
                                            <input type="file" name="document" class="form-control" required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Upload</button>
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
    </div>
    
    <!-- Add Worker Modal -->
    <div class="modal fade" id="addWorkerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="add_worker.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Worker</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Role</label>
                            <input type="text" name="role" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Aadhar Number</label>
                            <input type="text" name="aadhar" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>PAN</label>
                            <input type="text" name="pan" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Bank Details</label>
                            <textarea name="bank_details" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Monthly Salary</label>
                            <input type="number" step="0.01" name="monthly_salary" class="form-control" value="0">
                        </div>
                        <div class="mb-3">
                            <label>Joined Date</label>
                            <input type="date" name="joined_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Worker</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>