<?php
require_once 'includes/config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $min_stock = $_POST['min_stock'] ?? 5;
    $supplier = $_POST['supplier'];
    $purchase_date = $_POST['purchase_date'];
    $purchase_amount = $_POST['purchase_amount'];
    $pending_bill = $_POST['pending_bill'] ?? 0;
    
    $stmt = $conn->prepare("INSERT INTO stock (name, category_id, quantity, price, min_stock, supplier, purchase_date, purchase_amount, pending_bill) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siddsssdd", $name, $category_id, $quantity, $price, $min_stock, $supplier, $purchase_date, $purchase_amount, $pending_bill);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Stock added successfully!";
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
    }
    header('Location: stock.php');
}
?>