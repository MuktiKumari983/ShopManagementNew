<?php
require_once 'includes/config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['document'])) {
    $worker_id = $_POST['worker_id'];
    $target_dir = "assets/uploads/";
    $file_name = time() . '_' . basename($_FILES["document"]["name"]);
    $target_file = $target_dir . $file_name;
    
    if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
        $conn->query("UPDATE workers SET document_path='$target_file' WHERE id=$worker_id");
        $_SESSION['success'] = "Document uploaded successfully.";
    } else {
        $_SESSION['error'] = "Failed to upload document.";
    }
}
header('Location: workers.php');
?>