<?php
session_start();
require_once 'db.php';

if (!isset($_GET['order_id'])) {
    header("Location: place_order.php");
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order details
$stmt = $pdo->prepare("
    SELECT o.*, 
           p.payment_method,
           COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN payments p ON o.id = p.order_id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.id = ?
    GROUP BY o.id
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch order items
$stmt_items = $pdo->prepare("
    SELECT * FROM order_items
    WHERE order_id = ?
");
$stmt_items->execute([$order_id]);
$items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Use the same header as place_order.php -->
    
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-check-circle me-2"></i> Order Confirmed</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h5 class="alert-heading">Thank you for your order!</h5>
                            <p>Your order #<?= htmlspecialchars($order['id']) ?> has been placed successfully.</p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Order Summary</h5>
                                <p><strong>Order ID:</strong> #<?= htmlspecialchars($order['id']) ?></p>
                                <p><strong>Order Date:</strong> <?= date('F j, Y, g:i a', strtotime($order['created_at'])) ?></p>
                                <p><strong>Total Items:</strong> <?= htmlspecialchars($order['item_count']) ?></p>
                                <p><strong>Total Amount:</strong> ₹<?= number_format($order['total_price'], 2) ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Delivery Information</h5>
                                <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                                <p><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone']) ?></p>
                                <p><strong>Pickup Date:</strong> <?= date('F j, Y', strtotime($order['pickup_date'])) ?></p>
                                <p><strong>Pickup Time:</strong> <?= htmlspecialchars($order['pickup_time']) ?></p>
                                <p><strong>Payment Method:</strong> 
                                    <?= htmlspecialchars($order['payment_method']) ?>
                                </p>
                            </div>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                                            <td>₹<?= number_format($item['price'], 2) ?></td>
                                            <td>₹<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th>₹<?= number_format($order['total_price'], 2) ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="orders.php" class="btn btn-outline-primary">
                                <i class="fas fa-clipboard-list me-2"></i> View All Orders
                            </a>
                            <a href="place_order.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i> Place Another Order
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>