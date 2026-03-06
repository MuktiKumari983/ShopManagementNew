<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Site Management</title>
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
                <a href="sites.php" class="active"><i class="bi bi-building me-2"></i>Sites</a>
                <a href="purchases.php"><i class="bi bi-truck me-2"></i>Purchases</a>
                <a href="yearly_salary.php"><i class="bi bi-cash-stack me-2"></i>Yearly Salary</a>
                <a href="logout.php" class="mt-5"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            </div>
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between">
                    <h2>Site Management</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSiteModal">+ Add New Site</button>
                </div>
                
                <table class="table table-bordered mt-3">
                    <thead class="table-dark">
                        <tr><th>ID</th><th>Site Name</th><th>Contact</th><th>Estimate</th><th>Balance</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $sites = $conn->query("SELECT * FROM sites ORDER BY id DESC");
                        if (!$sites) {
                            echo "<tr><td colspan='7' class='text-danger'>Error: " . $conn->error . "</td></tr>";
                        } else {
                            while($site = $sites->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $site['id']; ?></td>
                            <td><?php echo $site['name']; ?></td>
                            <td><?php echo $site['contact_person']; ?><br><small><?php echo $site['contact_phone']; ?></small></td>
                            <td>₹<?php echo $site['estimated_amount']; ?></td>
                            <td>₹<?php echo $site['balance_amount']; ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $site['status']=='Ongoing'?'warning':($site['status']=='Completed'?'success':'secondary'); 
                                ?>"><?php echo $site['status']; ?></span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal<?php echo $site['id']; ?>">Add Payment</button>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSiteModal<?php echo $site['id']; ?>">Edit</button>
                                <a href="delete_site.php?id=<?php echo $site['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this site?')">Delete</a>
                            </td>
                        </tr>
                        <!-- Payment Modal -->
                        <div class="modal fade" id="paymentModal<?php echo $site['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="add_site_payment.php" method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header"><h5>Add Payment for <?php echo $site['name']; ?></h5></div>
                                        <div class="modal-body">
                                            <input type="hidden" name="site_id" value="<?php echo $site['id']; ?>">
                                            <div class="mb-3">
                                                <label>Amount</label>
                                                <input type="number" step="0.01" name="amount" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Payment Date</label>
                                                <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Note</label>
                                                <input type="text" name="note" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Add Payment</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Edit Site Modal -->
                        <div class="modal fade" id="editSiteModal<?php echo $site['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="edit_site.php" method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header"><h5>Edit Site</h5></div>
                                        <div class="modal-body">
                                            <input type="hidden" name="site_id" value="<?php echo $site['id']; ?>">
                                            <div class="mb-3">
                                                <label>Site Name</label>
                                                <input type="text" name="name" class="form-control" value="<?php echo $site['name']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Address</label>
                                                <textarea name="address" class="form-control"><?php echo $site['address']; ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label>Contact Person</label>
                                                <input type="text" name="contact_person" class="form-control" value="<?php echo $site['contact_person']; ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label>Contact Phone</label>
                                                <input type="text" name="contact_phone" class="form-control" value="<?php echo $site['contact_phone']; ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label>Start Date</label>
                                                <input type="date" name="start_date" class="form-control" value="<?php echo $site['start_date']; ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label>Estimated Amount</label>
                                                <input type="number" step="0.01" name="estimated_amount" class="form-control" value="<?php echo $site['estimated_amount']; ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label>Balance Amount</label>
                                                <input type="number" step="0.01" name="balance_amount" class="form-control" value="<?php echo $site['balance_amount']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label>Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="Ongoing" <?php echo $site['status']=='Ongoing'?'selected':''; ?>>Ongoing</option>
                                                    <option value="Completed" <?php echo $site['status']=='Completed'?'selected':''; ?>>Completed</option>
                                                    <option value="Pending" <?php echo $site['status']=='Pending'?'selected':''; ?>>Pending</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php 
                            endwhile;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Add Site Modal -->
    <div class="modal fade" id="addSiteModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="add_site.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header"><h5>Add New Site</h5></div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Site Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Address</label>
                            <textarea name="address" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Contact Person</label>
                            <input type="text" name="contact_person" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Contact Phone</label>
                            <input type="text" name="contact_phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Estimated Amount</label>
                            <input type="number" step="0.01" name="estimated_amount" class="form-control" value="0">
                        </div>
                        <div class="mb-3">
                            <label>Balance Amount</label>
                            <input type="number" step="0.01" name="balance_amount" class="form-control" value="0">
                        </div>
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="Ongoing">Ongoing</option>
                                <option value="Completed">Completed</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Site</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>