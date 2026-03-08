<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quote_no = $_POST['quote_no'];
    $customer_name = $_POST['customer_name'];
    $customer_gst = $_POST['customer_gst'] ?? null;
    $valid_till = $_POST['valid_till'];
    $subtotal = $_POST['subtotal'];
    $gst = $_POST['gst'];
    $total = $_POST['total'];
    $stock_ids = $_POST['stock_id'];
    $qtys = $_POST['qty'];
    $prices = $_POST['price'];
    
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("INSERT INTO customers (name, gst) VALUES (?, ?)");
        $stmt->bind_param("ss", $customer_name, $customer_gst);
        $stmt->execute();
        $customer_id = $conn->insert_id;
        
        $stmt = $conn->prepare("INSERT INTO quotations (quote_no, customer_id, valid_till, subtotal, gst_amount, total) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissdd", $quote_no, $customer_id, $valid_till, $subtotal, $gst, $total);
        $stmt->execute();
        $quote_id = $conn->insert_id;
        
        for ($i = 0; $i < count($stock_ids); $i++) {
            $stock_id = $stock_ids[$i];
            $qty = $qtys[$i];
            $price = $prices[$i];
            $item_total = $qty * $price;
            
            $stmt = $conn->prepare("INSERT INTO quotation_items (quotation_id, stock_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiddd", $quote_id, $stock_id, $qty, $price, $item_total);
            $stmt->execute();
        }
        
        $conn->commit();
        $_SESSION['success'] = "Quotation created successfully!";
        header("Location: view_quotation.php?id=$quote_id");
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: create_quotation.php");
    }
}
?>