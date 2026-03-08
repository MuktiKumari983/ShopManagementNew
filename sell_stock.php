<?php
require_once 'includes/auth.php';
$id = $_GET['id'] ?? 0;
$stock = $conn->query("SELECT * FROM stock WHERE id=$id")->fetch_assoc();
if (!$stock) {
    header('Location: stock.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sell_qty = $_POST['quantity'];
    if ($sell_qty <= $stock['quantity']) {
        $new_qty = $stock['quantity'] - $sell_qty;
        $conn->query("UPDATE stock SET quantity=$new_qty WHERE id=$id");
        $_SESSION['success'] = "Sold $sell_qty units.";
    } else {
        $_SESSION['error'] = "Insufficient stock! Available: {$stock['quantity']}";
    }
    header('Location: stock.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sell Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container" style="max-width: 500px;">
        <h2>Sell Stock: <?php echo $stock['name']; ?></h2>
        <p>Available Quantity: <?php echo $stock['quantity']; ?> <?php 
            $unit = $conn->query("SELECT unit FROM categories WHERE id={$stock['category_id']}")->fetch_assoc()['unit'];
            echo $unit;
        ?></p>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Quantity to Sell</label>
                <input type="number" step="0.01" name="quantity" class="form-control" max="<?php echo $stock['quantity']; ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Sell</button>
            <a href="stock.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>