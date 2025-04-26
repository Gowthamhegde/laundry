<?php

require 'config.php';

header('Content-Type: application/json');

// Validate input
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$order_id = $_POST['order_id'];
$payment_method = $_POST['payment_method'] ?? '';
$amount = floatval($_POST['amount'] ?? 0);

// Verify order belongs to user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND customer_email = ?");
$stmt->execute([$order_id, $_SESSION['email']]);
$order = $stmt->fetch();

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Update order with payment info
/*$update = $pdo->prepare("UPDATE orders SET 
                            payment_method = ?, 
                            payment_status = 'completed', 
                            status = 'paid',
                            total_price = ?,
                            updated_at = NOW()
                            WHERE order_id = ?");
    $update->execute([$payment_method, $amount, $order_id]);*/
    
    // Record payment transaction
    $stmt = $pdo->prepare("INSERT INTO payments 
                          (order_id, amount, payment_method, transaction_date) 
                          VALUES (?, ?, ?, NOW())");
    $stmt->execute([$order_id, $amount, $payment_method]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'message' => 'Payment processed successfully'
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Payment processing failed: ' . $e->getMessage()
    ]);
}
?>