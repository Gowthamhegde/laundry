<?php
session_start();
// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit();
}

// Database connection
require_once 'db.php';

// Fetch user's orders from database
$user_id = $_SESSION['user_id'];
$orders = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching orders: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Laundry Techs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4a6bff;
            --primary-light: #eef1ff;
            --secondary: #ff7e4a;
            --dark: #2a2a2a;
            --light: #f8f9fa;
            --gray: #6c757d;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --border-radius: 12px;
            --box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }
        
        .header {
            background-color: var(--primary);
            box-shadow: var(--box-shadow);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo {
            height: 40px;
            width: auto;
        }
        
        .brand-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-light);
            letter-spacing: -0.5px;
        }
        
        .nav-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            font-size: 1rem;
            transition: var(--transition);
            padding: 8px 12px;
            border-radius: 8px;
        }
        
        .nav-links a:hover {
            color: var(--primary);
            background-color: var(--primary-light);
        }
        
        .nav-links a.active {
            color: var(--primary-light);
            font-weight: 600;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
            overflow: hidden;
        }
        
        .user-avatar:hover {
            transform: scale(1.05);
        }
        
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 5%;
        }
        
        .page-title {
            font-size: 2rem;
            margin-bottom: 30px;
            color: var(--dark);
            position: relative;
            display: inline-block;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 4px;
            background-color: var(--primary);
            border-radius: 2px;
        }
        
        .orders-list {
            display: grid;
            gap: 20px;
        }
        
        .order-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            transition: var(--transition);
            border-left: 4px solid var(--primary);
        }
        
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .order-id {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary);
        }
        
        .order-id span {
            color: var(--dark);
        }
        
        .order-date {
            color: var(--gray);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .order-date i {
            font-size: 0.8rem;
        }
        
        .order-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .order-details {
            margin-top: 20px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px dashed #eee;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-name {
            font-weight: 500;
            color: var(--dark);
        }
        
        .item-quantity {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .item-price {
            font-weight: 600;
            color: var(--dark);
        }
        
        .order-total {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .total-label {
            font-size: 1rem;
            color: var(--gray);
        }
        
        .total-amount {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .no-orders {
            text-align: center;
            padding: 50px 20px;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        .no-orders i {
            font-size: 3rem;
            color: var(--gray);
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .no-orders h3 {
            color: var(--dark);
            margin-bottom: 10px;
        }
        
        .no-orders p {
            color: var(--gray);
            margin-bottom: 20px;
        }
        
        .shop-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }
        
        .shop-btn:hover {
            background-color: #3a5bef;
            transform: translateY(-2px);
        }
        
        .error {
            background-color: #f8d7da;
            color: var(--danger);
            padding: 20px;
            border-radius: var(--border-radius);
            text-align: center;
            margin-bottom: 30px;
            border-left: 4px solid var(--danger);
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-links {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
                gap: 15px;
            }
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .order-status {
                align-self: flex-start;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo-container">
            <img src="image/logo.png" alt="Laundry Techs Logo" class="logo">
            <span class="brand-name">Laundry Techs</span>
        </div>
        
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="services.php">Services</a>
            <a href="orders.php" class="active">My Orders</a>
            <a href="profile.php"><?php echo htmlspecialchars(ucfirst($_SESSION['name'])) ?>  </a>
            <div class="user-avatar" onclick="window.location.href='profile.php'">
                <i class="fas fa-user"></i>
            </div>
        </nav>
    </header>
    
    <div class="container">
        <h1 class="page-title">My Orders</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php elseif (empty($orders)): ?>
            <div class="no-orders">
                <i class="fas fa-box-open"></i>
                <h3>No Orders Yet</h3>
                <p>You haven't placed any orders with us yet.</p>
                <a href="shop.php" class="shop-btn">Browse Services</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">Order <span>#LDTS<?php echo htmlspecialchars($order['id']); ?></span></div>
                            <div class="order-date">
                                <i class="far fa-calendar-alt"></i>
                                <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                            </div>
                            <div class="order-status status-<?php echo strtolower($order['status']); ?>">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </div>
                        </div>
                        
                        <div class="order-details">
                            <?php 
                            // Fetch order items
                            $order_items = [];
                            try {
                                $stmt = $pdo->prepare("SELECT * from order_items WHERE order_id = ?");
                                $stmt->execute([$order['id']]);
                                $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "<div class='error'>Error fetching order items.</div>";
                            }
                            
                            foreach ($order_items as $item): ?>
                                <div class="order-item">
                                    <div>
                                        <div class="item-name"><?php echo htmlspecialchars($item['item_name']); ?></div>
                                        <div class="item-quantity">Quantity: <?php echo $item['quantity']; ?></div>
                                    </div>
                                    <div class="item-price">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-total">
                            <div class="total-label">Total Amount:</div>
                            <div class="total-amount">₹<?php echo number_format($order['total_price'], 2); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>