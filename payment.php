<?php
session_start();
require_once 'db.php';

// Check if cart exists and is not empty
if (empty($_SESSION['cart'])) {
    header("Location: place_order.php");
    exit();
}

// Process the form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Insert order information
        $stmt = $pdo->prepare("
            INSERT INTO orders (
                customer_id, 
                customer_name, 
                customer_email, 
                customer_phone, 
                total_price, 
                pickup_date, 
                pickup_time, 
                pickup_address, 
                status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");

        // Calculate total amount
        $total = 0;
        foreach ($_SESSION['cart'] as $item_id => $cart_item) {
            $stmt_item = $pdo->prepare("SELECT price_{$cart_item['service_type']} FROM price_list WHERE id = ?");
            $stmt_item->execute([$item_id]);
            $price = $stmt_item->fetchColumn();
            $total += $price * $cart_item['qty'];
        }

        // Get customer details from session
        $customer_id = $_SESSION['user_id'] ?? null;
        $customer_name = $_SESSION['name'] ?? 'Guest';
        $customer_email = $_SESSION['email'] ?? null;
        $customer_phone = $_SESSION['phone'] ?? null;

        $stmt->execute([
            $customer_id,
            $customer_name,
            $customer_email,
            $customer_phone,
            $total,
            $_POST['pickup_date'],
            $_POST['pickup_time'],
            $_POST['pickup_address']
        ]);

        $order_id = $pdo->lastInsertId();

        // Insert order items
        $stmt_items = $pdo->prepare("
            INSERT INTO order_items (
                order_id, 
                item_name, 
                quantity, 
                price
            ) VALUES (?, ?, ?, ?)
        ");

        foreach ($_SESSION['cart'] as $item_id => $cart_item) {
            // Get item details
            $stmt_item = $pdo->prepare("SELECT name, price_{$cart_item['service_type']} FROM price_list WHERE id = ?");
            $stmt_item->execute([$item_id]);
            $item = $stmt_item->fetch(PDO::FETCH_ASSOC);

            $stmt_items->execute([
                $order_id,
                $item['name'],
                $cart_item['qty'],
                $item['price_' . $cart_item['service_type']]
            ]);
        }

        // Insert payment information
        $payment_method = $_POST['payment_method'];
        $payment_map = [
            'gpay' => 'upi',
            'paytm' => 'upi',
            'phonepe' => 'upi',
            'cod' => 'Cash'
        ];
        $payment_type = $payment_map[$payment_method] ?? 'Cash';

        $stmt_payment = $pdo->prepare("
            INSERT INTO payments (
                order_id, 
                amount, 
                payment_method
            ) VALUES (?, ?, ?)
        ");
        $stmt_payment->execute([
            $order_id,
            $total,
            $payment_type
        ]);

        $pdo->commit();

        // Clear the cart
        unset($_SESSION['cart']);

        // Redirect to order confirmation page
        header("Location: order_confirmation.php?order_id=$order_id");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        // Handle error (log it and show user-friendly message)
        error_log("Order processing error: " . $e->getMessage());
        $_SESSION['error'] = "There was an error processing your order. Please try again.";
        header("Location: place_order.php");
        exit();
    }
} else {
    header("Location: place_order.php");
    exit();
}