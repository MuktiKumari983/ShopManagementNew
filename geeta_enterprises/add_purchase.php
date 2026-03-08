<?php
require_once 'includes/config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stock_id = $_POST['stock_id'];
    $quantity = $_POST['quantity'];
    $purchase_price = $_POST['purchase_price'];
    $supplier = $_POST['supplier'];
    $purchase_date = $_POST['purchase_date'];
    $payment_status = $_POST['payment_status'];
    $total_amount = $quantity * $purchase_price;
    
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO purchases (stock_id, quantity, purchase_price, total_amount, supplier, purchase_date, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("idddsss", $stock_id, $quantity, $purchase_price, $total_amount, $supplier, $purchase_date, $payment_status);
        $stmt->execute();
        
        $conn->query("UPDATE stock SET quantity = quantity + $quantity WHERE id = $stock_id");
        
        $conn->commit();
        $_SESSION['success'] = "Purchase recorded and stock updated.";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    header('Location: purchases.php');
}
?>