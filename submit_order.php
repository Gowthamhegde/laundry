<?php
// Start output buffering immediately with no whitespace before
ob_start();

// Set headers first
header('Content-Type: application/json');

// Disable error display (enable for debugging if needed)
error_reporting(0);
ini_set('display_errors', 0);

// Start session
session_start();

require 'config.php'; // Ensure this contains your PDO connection

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'redirect' => ''
];

try {
    // Validate required fields
    $required = ['booking_date', 'booking_time', 'pickup_address', 'total_price'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Validate session data
    if (empty($_SESSION['name']) || empty($_SESSION['email']) || empty($_SESSION['phone'])) {
        throw new Exception("Session expired. Please login again.");
    }

    // Validate items
    if (empty($_POST['items']) || !is_array($_POST['items'])) {
        throw new Exception("No items selected for order");
    }

    // Start transaction
    $pdo->beginTransaction();

    // Insert order
    $stmt = $pdo->prepare("INSERT INTO orders 
                          (customer_name, customer_email, customer_phone, total_price, 
                           booking_date, booking_time, pickup_address, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    
    $orderData = [
        $_SESSION['name'],
        $_SESSION['email'],
        $_SESSION['phone'],
        floatval($_POST['total_price']),
        $_POST['booking_date'],
        $_POST['booking_time'],
        $_POST['pickup_address']
    ];
    
    if (!$stmt->execute($orderData)) {
        throw new Exception("Failed to create order record");
    }

    $order_id = $pdo->lastInsertId();

    // Insert order items
    $item_stmt = $pdo->prepare("INSERT INTO order_items 
                               (order_id, item_name, quantity, price) 
                               VALUES (?, ?, ?, ?)");
    
    $hasValidItems = false;
    foreach ($_POST['items'] as $item) {
        $quantity = intval($item['quantity'] ?? 0);
        if ($quantity > 0) {
            $hasValidItems = true;
            $itemData = [
                $order_id,
                $item['name'],
                $quantity,
                floatval($item['price'])
            ];
            
            if (!$item_stmt->execute($itemData)) {
                throw new Exception("Failed to save order items");
            }
        }
    }

    if (!$hasValidItems) {
        throw new Exception("No valid items were selected");
    }

    // Commit transaction
    $pdo->commit();

    // Prepare success response
    $response = [
        'success' => true,
        'message' => 'Order placed successfully',
        'redirect' => 'payment.php?order_id=' . $order_id
    ];

} catch (PDOException $e) {
    // Database errors
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $response['message'] = "Database error: " . $e->getMessage();
    error_log("PDO Error: " . $e->getMessage());

} catch (Exception $e) {
    // Validation/other errors
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $response['message'] = $e->getMessage();
}

// Ensure no output has been sent
ob_end_clean();

// Send JSON response
echo json_encode($response);
exit;