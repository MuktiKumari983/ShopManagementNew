<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bill_no = $_POST['bill_no'];
    $customer_name = $_POST['customer_name'];
    $customer_gst = $_POST['customer_gst'] ?? null;
    $subtotal = $_POST['subtotal'];
    $gst = $_POST['gst'];
    $total = $_POST['total'];
    $stock_ids = $_POST['stock_id'];
    $qtys = $_POST['qty'];
    $prices = $_POST['price'];
    
    $conn->begin_transaction();
    
    try {
        // Insert or get customer
        $stmt = $conn->prepare("INSERT INTO customers (name, gst) VALUES (?, ?)");
        $stmt->bind_param("ss", $customer_name, $customer_gst);
        $stmt->execute();
        $customer_id = $conn->insert_id;
        
        // Insert bill
        $qr = "upi://pay?pa=yourupi@okhdfcbank&pn=Geeta%20Enterprises&am=$total&cu=INR"; // placeholder
        $stmt = $conn->prepare("INSERT INTO bills (bill_no, customer_id, customer_gst, subtotal, gst_amount, total, qr_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisddds", $bill_no, $customer_id, $customer_gst, $subtotal, $gst, $total, $qr);
        $stmt->execute();
        $bill_id = $conn->insert_id;
        
        // Insert items and update stock
        for ($i = 0; $i < count($stock_ids); $i++) {
            $stock_id = $stock_ids[$i];
            $qty = $qtys[$i];
            $price = $prices[$i];
            $item_total = $qty * $price;
            
            $stmt = $conn->prepare("INSERT INTO bill_items (bill_id, stock_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiddd", $bill_id, $stock_id, $qty, $price, $item_total);
            $stmt->execute();
            
            // Reduce stock
            $conn->query("UPDATE stock SET quantity = quantity - $qty WHERE id = $stock_id");
        }
        
        $conn->commit();
        $_SESSION['success'] = "Bill created successfully!";
        header("Location: view_bill.php?id=$bill_id");
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: create_bill.php");
    }
}
?>