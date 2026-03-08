<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';
$quote_no = generateQuoteNo($conn);
$stock_items = $conn->query("SELECT s.*, c.unit FROM stock s JOIN categories c ON s.category_id = c.id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Quotation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2>Create New Quotation</h2>
        <form method="POST" action="save_quotation.php" id="quotationForm">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Quote No</label>
                    <input type="text" name="quote_no" class="form-control" value="<?php echo $quote_no; ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label>Valid Till</label>
                    <input type="date" name="valid_till" class="form-control" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required>
                </div>
                <div class="col-md-3">
                    <label>Customer Name</label>
                    <input type="text" name="customer_name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>Customer GST (Optional)</label>
                    <input type="text" name="customer_gst" class="form-control">
                </div>
            </div>
            
            <table class="table" id="itemsTable">
                <thead>
                    <tr><th>Item</th><th>Quantity</th><th>Price (Editable)</th><th>Total</th><th></th></tr>
                </thead>
                <tbody id="itemsBody">
                    <tr>
                        <td>
                            <select name="stock_id[]" class="form-control stock-select" required>
                                <option value="">Select Item</option>
                                <?php 
                                $stock_items->data_seek(0);
                                while($item = $stock_items->fetch_assoc()): 
                                ?>
                                <option value="<?php echo $item['id']; ?>" data-price="<?php echo $item['price']; ?>" data-unit="<?php echo $item['unit']; ?>">
                                    <?php echo $item['name']; ?> (<?php echo $item['unit']; ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                        <td><input type="number" step="0.01" name="qty[]" class="form-control qty" required></td>
                        <td><input type="number" step="0.01" name="price[]" class="form-control price" value="" required></td>
                        <td><input type="number" step="0.01" name="item_total[]" class="form-control item-total" readonly></td>
                        <td><button type="button" class="btn btn-danger remove-row">X</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-secondary" id="addRow">+ Add Item</button>
            
            <hr>
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <table class="table">
                        <tr>
                            <td>Subtotal</td>
                            <td><input type="number" name="subtotal" id="subtotal" class="form-control" readonly></td>
                        </tr>
                        <tr>
                            <td>GST (18%)</td>
                            <td><input type="number" name="gst" id="gst" class="form-control" readonly></td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td><input type="number" name="total" id="total" class="form-control" readonly></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <button type="submit" class="btn btn-success">Save Quotation</button>
            <a href="quotations.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    
    <script>
    document.getElementById('addRow').addEventListener('click', function() {
        let tbody = document.getElementById('itemsBody');
        let newRow = tbody.children[0].cloneNode(true);
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        tbody.appendChild(newRow);
    });
    
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('stock-select')) {
            let price = e.target.options[e.target.selectedIndex].dataset.price;
            let row = e.target.closest('tr');
            row.querySelector('.price').value = price;
            calculateRowTotal(row);
        }
        if (e.target.classList.contains('qty') || e.target.classList.contains('price')) {
            let row = e.target.closest('tr');
            calculateRowTotal(row);
        }
    });
    
    function calculateRowTotal(row) {
        let qty = row.querySelector('.qty').value;
        let price = row.querySelector('.price').value;
        let total = qty * price;
        row.querySelector('.item-total').value = total.toFixed(2);
        calculateOverall();
    }
    
    function calculateOverall() {
        let subtotal = 0;
        document.querySelectorAll('.item-total').forEach(input => {
            subtotal += parseFloat(input.value) || 0;
        });
        document.getElementById('subtotal').value = subtotal.toFixed(2);
        let gst = subtotal * 0.18;
        document.getElementById('gst').value = gst.toFixed(2);
        document.getElementById('total').value = (subtotal + gst).toFixed(2);
    }
    
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            if (document.querySelectorAll('#itemsBody tr').length > 1) {
                e.target.closest('tr').remove();
                calculateOverall();
            }
        }
    });
    </script>
</body>
</html>