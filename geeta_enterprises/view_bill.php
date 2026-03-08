<?php
require_once 'includes/auth.php';
$id = $_GET['id'] ?? 0;
$bill = $conn->query("SELECT b.*, c.name as customer_name FROM bills b LEFT JOIN customers c ON b.customer_id = c.id WHERE b.id=$id")->fetch_assoc();
if (!$bill) {
    header('Location: billing.php');
    exit();
}
$items = $conn->query("SELECT bi.*, s.name as item_name FROM bill_items bi JOIN stock s ON bi.stock_id = s.id WHERE bi.bill_id=$id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Bill</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body class="p-4">
    <div class="container">
        <div class="card" id="billCard">
            <div class="card-header bg-primary text-white">
                <h3>GEETA ENTERPRISES - Tax Invoice</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <p><strong>Bill No:</strong> <?php echo $bill['bill_no']; ?></p>
                        <p><strong>Date:</strong> <?php echo $bill['bill_date']; ?></p>
                        <p><strong>Customer:</strong> <?php echo $bill['customer_name']; ?></p>
                        <p><strong>Customer GST:</strong> <?php echo $bill['customer_gst'] ?: 'N/A'; ?></p>
                    </div>
                    <div class="col-6 text-end">
                        <p><strong>GSTIN:</strong> 07AABCU9603R1ZM (Your GST)</p>
                    </div>
                </div>
                
                <table class="table table-bordered">
                    <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                    <tbody>
                        <?php while($item = $items->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $item['item_name']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>₹<?php echo $item['price']; ?></td>
                            <td>₹<?php echo $item['total']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <div class="row">
                    <div class="col-6">
                        <div id="qrcode"></div>
                        <p>Scan to pay</p>
                    </div>
                    <div class="col-6">
                        <table class="table">
                            <tr><td>Subtotal</td><td>₹<?php echo $bill['subtotal']; ?></td></tr>
                            <tr><td>GST (18%)</td><td>₹<?php echo $bill['gst_amount']; ?></td></tr>
                            <tr><th>Total</th><th>₹<?php echo $bill['total']; ?></th></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <button class="btn btn-primary" onclick="downloadBillImage()">📸 Download as Image</button>
            <button class="btn btn-success" onclick="shareBillImage()">📱 Share Image on WhatsApp</button>
            <a href="billing.php" class="btn btn-secondary">Back to Bills</a>
        </div>
    </div>
    
    <script>
    var qrcode = new QRCode(document.getElementById("qrcode"), {
        text: "upi://pay?pa=yourupi@okhdfcbank&pn=Geeta%20Enterprises&am=<?php echo $bill['total']; ?>&cu=INR",
        width: 128,
        height: 128
    });
    
    function downloadBillImage() {
        const billElement = document.getElementById('billCard');
        html2canvas(billElement, {
            scale: 2,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'bill_<?php echo $bill['bill_no']; ?>.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        }).catch(error => {
            console.error('Error generating image:', error);
            alert('Failed to generate image. Please try again.');
        });
    }
    
    function shareBillImage() {
        const billElement = document.getElementById('billCard');
        html2canvas(billElement, {
            scale: 2,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            canvas.toBlob(blob => {
                if (navigator.share && navigator.canShare && navigator.canShare({ files: [new File([blob], 'bill.png', { type: 'image/png' })] })) {
                    navigator.share({
                        title: 'Bill <?php echo $bill['bill_no']; ?>',
                        text: 'Total: ₹<?php echo $bill['total']; ?>',
                        files: [new File([blob], 'bill.png', { type: 'image/png' })]
                    }).catch(err => console.log('Share cancelled:', err));
                } else {
                    const url = URL.createObjectURL(blob);
                    const whatsappUrl = `https://wa.me/?text=${encodeURIComponent('Check this bill: ' + url)}`;
                    window.open(whatsappUrl, '_blank');
                }
            }, 'image/png');
        }).catch(error => {
            console.error('Error generating image:', error);
            alert('Failed to generate image.');
        });
    }
    </script>
</body>
</html>