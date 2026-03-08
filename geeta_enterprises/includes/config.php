<?php
session_start();
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'geeta_enterprises';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
date_default_timezone_set('Asia/Kolkata');
?>