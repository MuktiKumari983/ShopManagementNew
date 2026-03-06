<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$quote_id = $_GET['quote_id'] ?? 0;
$quote = $conn->query("SELECT * FROM quotations WHERE id=$quote_id")->fetch_assoc();
if (!$quote || $quote['converted_to_bill']) {
    header('Location: quotations.php');
    exit();
}

$items = $conn->query("SELECT * FROM quotation_items WHERE quotation_id=$quote_id");

$conn->begin_transaction();
try {
    $bill_no = generateBillNo($conn);
    $customer_id = $quote['customer_id'];
    
    $stmt = $conn->prepare("INSERT INTO bills (bill_no, customer_id, customer_gst, subtotal, gst_amount, total, qr_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $qr = "upi://pay?pa=yourupi@okhdfcbank&pn=Geeta%20Enterprises&am={$quote['total']}&cu=INR";
    $stmt->bind_param("sisddds", $bill_no, $customer_id, $quote['customer_gst'], $quote['subtotal'], $quote['gst_amount'], $quote['total'], $qr);
    $stmt->execute();
    $bill_id = $conn->insert_id;
    
    while($item = $items->fetch_assoc()) {
        $stock_id = $item['stock_id'];
        $qty = $item['quantity'];
        $price = $item['price'];
        $item_total = $qty * $price;
        
        $stmt = $conn->prepare("INSERT INTO bill_items (bill_id, stock_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiddd", $bill_id, $stock_id, $qty, $price, $item_total);
        $stmt->execute();
        
        $conn->query("UPDATE stock SET quantity = quantity - $qty WHERE id = $stock_id");
    }
    
    $conn->query("UPDATE quotations SET converted_to_bill=$bill_id WHERE id=$quote_id");
    
    $conn->commit();
    $_SESSION['success'] = "Quotation converted to bill successfully!";
    header("Location: view_bill.php?id=$bill_id");
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Conversion failed: " . $e->getMessage();
    header("Location: view_quotation.php?id=$quote_id");
}
?>