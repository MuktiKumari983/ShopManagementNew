<?php
require_once 'includes/config.php';
$id = $_GET['id'] ?? 0;
$conn->query("DELETE FROM workers WHERE id=$id");
header('Location: workers.php');
?>