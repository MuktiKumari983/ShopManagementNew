<?php
require_once 'includes/config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $contact_person = $_POST['contact_person'];
    $contact_phone = $_POST['contact_phone'];
    $start_date = $_POST['start_date'];
    $estimated_amount = $_POST['estimated_amount'];
    $balance_amount = $_POST['balance_amount'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("INSERT INTO sites (name, address, contact_person, contact_phone, start_date, estimated_amount, balance_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssdds", $name, $address, $contact_person, $contact_phone, $start_date, $estimated_amount, $balance_amount, $status);
    $stmt->execute();
    header('Location: sites.php');
}
?>