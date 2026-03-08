<?php
require_once 'includes/config.php';
$id = $_GET['id'] ?? 0;
$conn->query("DELETE FROM stock WHERE id=$id");
header('Location: stock.php');
?>