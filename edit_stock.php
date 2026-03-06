<?php
require_once 'includes/auth.php';
$id = $_GET['id'] ?? 0;
$stock = $conn->query("SELECT * FROM stock WHERE id=$id")->fetch_assoc();
if (!$stock) {
    header('Location: stock.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $min_stock = $_POST['min_stock'];
    $supplier = $_POST['supplier'];
    $purchase_date = $_POST['purchase_date'];
    $purchase_amount = $_POST['purchase_amount'];
    $pending_bill = $_POST['pending_bill'];
    
    $stmt = $conn->prepare("UPDATE stock SET name=?, category_id=?, quantity=?, price=?, min_stock=?, supplier=?, purchase_date=?, purchase_amount=?, pending_bill=? WHERE id=?");
    $stmt->bind_param("siddsssddi", $name, $category_id, $quantity, $price, $min_stock, $supplier, $purchase_date, $purchase_amount, $pending_bill, $id);
    $stmt->execute();
    header('Location: stock.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2>Edit Stock Item</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Item Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo $stock['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Category</label>
                <select name="category_id" class="form-control" required>
                    <?php
                    $cats = $conn->query("SELECT * FROM categories");
                    while($cat = $cats->fetch_assoc()) {
                        $selected = ($cat['id'] == $stock['category_id']) ? 'selected' : '';
                        echo "<option value='{$cat['id']}' $selected>{$cat['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Quantity</label>
                <input type="number" step="0.01" name="quantity" class="form-control" value="<?php echo $stock['quantity']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Price</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $stock['price']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Min Stock Alert</label>
                <input type="number" step="0.01" name="min_stock" class="form-control" value="<?php echo $stock['min_stock']; ?>">
            </div>
            <div class="mb-3">
                <label>Supplier</label>
                <input type="text" name="supplier" class="form-control" value="<?php echo $stock['supplier']; ?>">
            </div>
            <div class="mb-3">
                <label>Purchase Date</label>
                <input type="date" name="purchase_date" class="form-control" value="<?php echo $stock['purchase_date']; ?>">
            </div>
            <div class="mb-3">
                <label>Purchase Amount</label>
                <input type="number" step="0.01" name="purchase_amount" class="form-control" value="<?php echo $stock['purchase_amount']; ?>">
            </div>
            <div class="mb-3">
                <label>Pending Bill</label>
                <input type="number" step="0.01" name="pending_bill" class="form-control" value="<?php echo $stock['pending_bill']; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="stock.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>