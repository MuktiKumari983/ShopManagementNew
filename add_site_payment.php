<?php
require_once 'includes/config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $site_id = $_POST['site_id'];
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $note = $_POST['note'];
    
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO site_payments (site_id, amount, payment_date, note) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $site_id, $amount, $payment_date, $note);
        $stmt->execute();
        
        $conn->query("UPDATE sites SET balance_amount = balance_amount - $amount WHERE id = $site_id");
        
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
    }
    header('Location: sites.php');
}
?>