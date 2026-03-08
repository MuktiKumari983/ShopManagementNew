<?php
require_once 'includes/auth.php';
$id = $_GET['id'] ?? 0;
$quote = $conn->query("SELECT q.*, c.name as customer_name FROM quotations q LEFT JOIN customers c ON q.customer_id = c.id WHERE q.id=$id")->fetch_assoc();
if (!$quote) {
    header('Location: quotations.php');
    exit();
}
$items = $conn->query("SELECT qi.*, s.name as item_name FROM quotation_items qi JOIN stock s ON qi.stock_id = s.id WHERE qi.quotation_id=$id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Quotation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body class="p-4">
    <div class="container">
        <div class="card" id="quoteCard">
            <div class="card-header bg-info text-white">
                <h3>GEETA ENTERPRISES - Quotation</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <p><strong>Quote No:</strong> <?php echo $quote['quote_no']; ?></p>
                        <p><strong>Date:</strong> <?php echo $quote['quote_date']; ?></p>
                        <p><strong>Valid Till:</strong> <?php echo $quote['valid_till']; ?></p>
                        <p><strong>Customer:</strong> <?php echo $quote['customer_name']; ?></p>
                        <p><strong>Customer GST:</strong> <?php echo $quote['customer_gst'] ?: 'N/A'; ?></p>
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
                    <div class="col-6"></div>
                    <div class="col-6">
                        <table class="table">
                            <tr><td>Subtotal</td><td>₹<?php echo $quote['subtotal']; ?></td></tr>
                            <tr><td>GST (18%)</td><td>₹<?php echo $quote['gst_amount']; ?></td></tr>
                            <tr><th>Total</th><th>₹<?php echo $quote['total']; ?></th></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <button class="btn btn-primary" onclick="downloadQuoteImage()">📸 Download as Image</button>
            <button class="btn btn-success" onclick="shareQuoteImage()">📱 Share Image on WhatsApp</button>
            <a href="quotations.php" class="btn btn-secondary">Back to Quotations</a>
            <?php if (!$quote['converted_to_bill']): ?>
            <a href="convert_to_bill.php?quote_id=<?php echo $id; ?>" class="btn btn-success">Convert to Bill</a>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
    function downloadQuoteImage() {
        const quoteElement = document.getElementById('quoteCard');
        html2canvas(quoteElement, {
            scale: 2,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'quotation_<?php echo $quote['quote_no']; ?>.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        }).catch(error => {
            console.error('Error generating image:', error);
            alert('Failed to generate image. Please try again.');
        });
    }
    
    function shareQuoteImage() {
        const quoteElement = document.getElementById('quoteCard');
        html2canvas(quoteElement, {
            scale: 2,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            canvas.toBlob(blob => {
                if (navigator.share && navigator.canShare && navigator.canShare({ files: [new File([blob], 'quotation.png', { type: 'image/png' })] })) {
                    navigator.share({
                        title: 'Quotation <?php echo $quote['quote_no']; ?>',
                        text: 'Total: ₹<?php echo $quote['total']; ?>',
                        files: [new File([blob], 'quotation.png', { type: 'image/png' })]
                    }).catch(err => console.log('Share cancelled:', err));
                } else {
                    const url = URL.createObjectURL(blob);
                    const whatsappUrl = `https://wa.me/?text=${encodeURIComponent('Check this quotation: ' + url)}`;
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