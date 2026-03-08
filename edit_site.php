<?php
require_once 'includes/config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $site_id = $_POST['site_id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $contact_person = $_POST['contact_person'];
    $contact_phone = $_POST['contact_phone'];
    $start_date = $_POST['start_date'];
    $estimated_amount = $_POST['estimated_amount'];
    $balance_amount = $_POST['balance_amount'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE sites SET name=?, address=?, contact_person=?, contact_phone=?, start_date=?, estimated_amount=?, balance_amount=?, status=? WHERE id=?");
    $stmt->bind_param("sssssddsi", $name, $address, $contact_person, $contact_phone, $start_date, $estimated_amount, $balance_amount, $status, $site_id);
    $stmt->execute();
    header('Location: sites.php');
}
?>