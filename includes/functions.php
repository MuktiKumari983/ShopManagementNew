<?php
function getStockValue($conn) {
    $result = $conn->query("SELECT SUM(quantity * price) as total FROM stock");
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

function getLowStockItems($conn, $limit = 5) {
    return $conn->query("SELECT s.*, c.name as category_name, c.unit 
                         FROM stock s JOIN categories c ON s.category_id = c.id 
                         WHERE s.quantity <= s.min_stock LIMIT $limit");
}

function generateBillNo($conn) {
    $prefix = 'BILL-'.date('Ymd');
    $result = $conn->query("SELECT COUNT(*) as count FROM bills WHERE bill_no LIKE '$prefix%'");
    $row = $result->fetch_assoc();
    $num = $row['count'] + 1;
    return $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);
}

function generateQuoteNo($conn) {
    $prefix = 'QUOTE-'.date('Ymd');
    $result = $conn->query("SELECT COUNT(*) as count FROM quotations WHERE quote_no LIKE '$prefix%'");
    $row = $result->fetch_assoc();
    $num = $row['count'] + 1;
    return $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);
}
?>