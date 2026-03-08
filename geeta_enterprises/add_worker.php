<?php
require_once 'includes/config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $phone = $_POST['phone'];
    $aadhar = $_POST['aadhar'];
    $pan = $_POST['pan'];
    $bank_details = $_POST['bank_details'];
    $monthly_salary = $_POST['monthly_salary'];
    $joined_date = $_POST['joined_date'];
    
    $stmt = $conn->prepare("INSERT INTO workers (name, role, phone, aadhar, pan, bank_details, monthly_salary, joined_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssds", $name, $role, $phone, $aadhar, $pan, $bank_details, $monthly_salary, $joined_date);
    $stmt->execute();
    header('Location: workers.php');
}
?>