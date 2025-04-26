<?php
session_start();
require_once 'db.php';

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Set default service type if not provided
$selected_service = $_GET['service'] ?? 'wash_iron';

// Fetch categories
$category_stmt = $pdo->query("SELECT DISTINCT category_name FROM category");
$categories = $category_stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch items with category and price for selected service
$item_stmt = $pdo->prepare("SELECT p.id, p.name, c.category_name, 
    p.price_wash_fold, p.price_wash_iron, p.price_steam_iron, p.price_dry_clean
    FROM price_list p 
    JOIN category c ON p.category_id = c.category_id");
$item_stmt->execute();
$items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);

// Set active category
$active_category = $_GET['category'] ?? $categories[0] ?? '';

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['item_id'], $_POST['service_type'])) {
        // Add to cart
        $item_id = $_POST['item_id'];
        $service_type = $_POST['service_type'];

        if (!isset($_SESSION['cart'][$item_id])) {
            $_SESSION['cart'][$item_id] = [
                'qty' => 1,
                'service_type' => $service_type
            ];
        } else {
            $_SESSION['cart'][$item_id]['qty']++;
        }
        exit; // For AJAX
    }

    // Handle cart quantity changes
    if (isset($_POST['cart_action'], $_POST['item_id'])) {
        $item_id = $_POST['item_id'];

        if ($_POST['cart_action'] === 'remove') {
            unset($_SESSION['cart'][$item_id]);
        } elseif ($_POST['cart_action'] === 'decrease') {
            if (isset($_SESSION['cart'][$item_id])) {
                if ($_SESSION['cart'][$item_id]['qty'] > 1) {
                    $_SESSION['cart'][$item_id]['qty']--;
                } else {
                    unset($_SESSION['cart'][$item_id]);
                }
            }
        }

        header("Location: place_order.php?service=$selected_service&category=$active_category");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Techs - Place Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2575fc;
            --secondary: #6a11cb;
            --accent: #ff5e62;
            --light: #f8f9fa;
            --dark: #212529;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
        }

        /* Premium Header */
        .navbar {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 8px 15px !important;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin: 0 5px;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white !important;
        }

        .nav-link i {
            margin-right: 8px;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 8px;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        /* Premium Sidebar */
        .sidebar {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.05);
            margin-right: 20px;
            height: fit-content;
        }

        .sidebar-section {
            margin-bottom: 25px;
        }

        .sidebar-title {
            color: var(--dark);
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }

        .sidebar-title i {
            margin-right: 10px;
            color: var(--primary);
        }

        /* Premium Service Buttons */
        .service-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .service-btn {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-align: left;
            display: flex;
            align-items: center;
        }

        .service-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            color: white;
        }

        .service-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            box-shadow: 0 4px 20px rgba(37, 117, 252, 0.3);
        }

        .service-btn i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        /* Premium Category Buttons */
        .category-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .category-btn {
            background: white;
            border: 1px solid #e0e0e0;
            color: #555;
            padding: 10px 18px;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .category-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
            color: var(--primary);
        }

        .category-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 15px rgba(37, 117, 252, 0.2);
        }

        .category-btn.active::before {
            content: '';
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: var(--secondary);
            border-radius: 50%;
            border: 2px solid white;
        }

        /* Premium Item Cards */
        .item-card {
            background: white;
            border: none;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            height: 100%;
        }

        .item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .item-name {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .item-category {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .item-price {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.2rem;
            margin: 15px 0;
        }

        .add-to-cart-btn {
            background: var(--primary);
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .add-to-cart-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        /* Premium Cart */
        .cart-container {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.05);
            height: fit-content;
        }

        .cart-title {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .cart-title i {
            margin-right: 10px;
            color: var(--primary);
        }

        .cart-item {
            border: none;
            border-radius: 8px;
            margin-bottom: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .cart-item-name {
            font-weight: 600;
        }

        .cart-item-service {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .cart-item-qty {
            font-weight: 600;
            color: var(--dark);
        }

        .cart-item-price {
            font-weight: 600;
            color: var(--primary);
        }

        .cart-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .cart-action-btn {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }

        .decrease-btn {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: var(--dark);
        }

        .decrease-btn:hover {
            background: #e9ecef;
        }

        .remove-btn {
            background: #fff0f0;
            border: 1px solid #ffdddd;
            color: #dc3545;
        }

        .remove-btn:hover {
            background: #ffdddd;
        }

        .cart-total {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--dark);
        }

        .checkout-btn {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            border: none;
            padding: 12px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        /* Premium Modal */
        .modal-header {
            border-bottom: none;
            padding-bottom: 0;
        }

        .modal-title {
            font-weight: 600;
            color: var(--dark);
        }

        .form-label {
            font-weight: 500;
            color: var(--dark);
        }

        .payment-methods {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }

        .payment-methods .form-check {
            padding: 8px 15px;
            border-radius: 6px;
            background: white;
            margin-bottom: 8px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .payment-methods .form-check:hover {
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .payment-methods .form-check-input:checked~.form-check-label {
            color: var(--primary);
            font-weight: 500;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .sidebar {
                margin-right: 0;
                margin-bottom: 30px;
            }

            .category-buttons {
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <!-- Premium Header -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="image/logo.png" alt="Laundry Techs">
                Laundry Techs
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="services.php">
                            <i class="fas fa-concierge-bell"></i> Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_orders.php">
                            <i class="fas fa-clipboard-list"></i> My Orders
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item position-relative">
                        <a class="nav-link" href="#">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <?php if (!empty($_SESSION['cart'])): ?>
                                <span class="cart-count"><?= array_sum(array_column($_SESSION['cart'], 'qty')) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item  position-relative">
                        <a class="nav-link" href="profile.php" id="profile">
                            <img src="image/user_avatar.png" class="user-avatar" alt="User">
                            <?= htmlspecialchars($_SESSION['name'] ?? 'Account') ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row">
            <!-- Premium Sidebar -->
            <div class="col-lg-3">
                <div class="sidebar sticky-top" style="top: 20px;">
                    <!-- Service Selection -->
                    <div class="sidebar-section">
                        <h4 class="sidebar-title"><i class="fas fa-sliders-h"></i>Services</h4>
                        <div class="service-buttons">
                            <a href="?service=wash_fold&category=<?= $active_category ?>"
                                class="service-btn <?= $selected_service === 'wash_fold' ? 'active' : '' ?>">
                                Wash & Fold
                            </a>
                            <a href="?service=wash_iron&category=<?= $active_category ?>"
                                class="service-btn <?= $selected_service === 'wash_iron' ? 'active' : '' ?>">
                                Wash & Iron
                            </a>
                            <a href="?service=steam_iron&category=<?= $active_category ?>"
                                class="service-btn <?= $selected_service === 'steam_iron' ? 'active' : '' ?>">
                                Steam Iron
                            </a>
                            <a href="?service=dry_clean&category=<?= $active_category ?>"
                                class="service-btn <?= $selected_service === 'dry_clean' ? 'active' : '' ?>">
                                Dry Clean
                            </a>
                        </div>
                    </div>

                    <!-- Category Selection -->
                    <div class="sidebar-section">
                        <h4 class="sidebar-title"><i class="fas fa-tags"></i>Categories</h4>
                        <div class="category-buttons">
                            <?php foreach ($categories as $category): ?>
                                <a href="?service=<?= $selected_service ?>&category=<?= urlencode($category) ?>"
                                    class="category-btn <?= $active_category === $category ? 'active' : '' ?>">
                                    <?= htmlspecialchars($category) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-6">
                <div class="mb-4">
                    <h3 class="fw-bold"><?= htmlspecialchars($active_category) ?></h3>
                    <p class="text-muted"><?= ucfirst(str_replace('_', ' ', $selected_service)) ?> Service</p>
                </div>

                <div class="row">
                    <?php foreach ($items as $item): ?>
                        <?php if ($item['category_name'] === $active_category): ?>
                            <div class="col-md-6 mb-4">
                                <div class="item-card">
                                    <h5 class="item-name"><?= htmlspecialchars($item['name']) ?></h5>
                                    <p class="item-category"><?= htmlspecialchars($item['category_name']) ?></p>
                                    <p class="item-price">₹
                                        <?=
                                        match ($selected_service) {
                                            'wash_fold' => $item['price_wash_fold'],
                                            'wash_iron' => $item['price_wash_iron'],
                                            'steam_iron' => $item['price_steam_iron'],
                                            'dry_clean' => $item['price_dry_clean'],
                                            default => 0
                                        }
                                        ?>
                                    </p>
                                    <form method="post" class="add-to-cart-form">
                                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="service_type" value="<?= $selected_service ?>">
                                        <button type="submit" class="add-to-cart-btn">
                                            <i class="fas fa-plus me-2"></i>Add to Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Premium Cart -->
            <div class="col-lg-3">
                <div class="cart-container sticky-top" style="top: 20px;">
                    <h5 class="cart-title"><i class="fas fa-shopping-basket"></i> Your Basket</h5>

                    <?php if (empty($_SESSION['cart'])): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-basket fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Your basket is empty</p>
                        </div>
                    <?php else: ?>
                        <div class="mb-4">
                            <?php
                            $grand_total = 0;
                            foreach ($_SESSION['cart'] as $item_id => $cart_item):
                                $stmt = $pdo->prepare("SELECT * FROM price_list WHERE id = ?");
                                $stmt->execute([$item_id]);
                                $item = $stmt->fetch(PDO::FETCH_ASSOC);
                                if ($item):
                                    $qty = $cart_item['qty'] ?? 1;
                                    $stype = $cart_item['service_type'] ?? 'wash_iron';
                                    $price = $item['price_' . $stype] * $qty;
                                    $grand_total += $price;
                            ?>
                                    <div class="cart-item p-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
                                                <div class="cart-item-service"><?= ucfirst(str_replace('_', ' ', $stype)) ?></div>
                                            </div>
                                            <div class="text-end">
                                                <div class="cart-item-qty">x<?= $qty ?></div>
                                                <div class="cart-item-price">₹<?= $price ?></div>
                                            </div>
                                        </div>
                                        <div class="cart-actions">
                                            <form method="post">
                                                <input type="hidden" name="item_id" value="<?= $item_id ?>">
                                                <input type="hidden" name="cart_action" value="decrease">
                                                <button type="submit" class="cart-action-btn decrease-btn">
                                                    <i class="fas fa-minus"></i> Decrease
                                                </button>
                                            </form>
                                            <form method="post">
                                                <input type="hidden" name="item_id" value="<?= $item_id ?>">
                                                <input type="hidden" name="cart_action" value="remove">
                                                <button type="submit" class="cart-action-btn remove-btn">
                                                    <i class="fas fa-trash"></i> Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                            <?php endif;
                            endforeach; ?>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="cart-total">Total:</span>
                            <span class="cart-total">₹<?= $grand_total ?></span>
                        </div>

                        <button class="checkout-btn" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                            <i class="fas fa-credit-card me-2"></i> Proceed to Checkout
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<!-- Premium Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Your Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="payment.php" method="post">
                <div class="modal-body">
                    <div class="mb-4">
                        <h6 class="fw-bold">Customer Information</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Name:</span>
                            <span><?= htmlspecialchars(ucfirst($_SESSION['name'] ?? 'Guest')) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Phone:</span>
                            <span><?= htmlspecialchars($_SESSION['phone'] ?? 'N/A') ?></span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pickup Date</label>
                        <input type="date" name="pickup_date" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pickup Time</label>
                        <input type="time" name="pickup_time" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Delivery Address</label>
                        <textarea name="pickup_address" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <!-- Payment Method Selection -->
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <div class="payment-methods">
                          <!--  <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="gpay" value="gpay" required>
                                <label class="form-check-label" for="gpay">
                                    <img src="image/gpay.png" alt="Google Pay" style="height: 24px; margin-left: 10px;">
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="paytm" value="paytm">
                                <label class="form-check-label" for="paytm">
                                    <img src="image/paytm.jpeg" alt="Paytm" style="height: 24px; margin-left: 10px;">
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="phonepe" value="phonepe">
                                <label class="form-check-label" for="phonepe">
                                    <img src="image/phonepay.png" alt="PhonePe" style="height: 24px; margin-left: 10px;">
                                </label>
                            </div>-->
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod">
                                <label class="form-check-label" for="cod">
                                    <i class="fas fa-money-bill-wave ms-2 me-2"></i> Cash on Delivery
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- UPI ID Field (shown only when UPI payment is selected) -->
                    <div class="mb-3" id="upiIdField" style="display: none;">
                        <label class="form-label">UPI ID</label>
                        <input type="text" name="upi_id" class="form-control" placeholder="Enter your UPI ID">
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="termsCheck" required>
                        <label class="form-check-label" for="termsCheck">
                            I agree to the <a href="#">terms and conditions</a>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle me-2"></i> Confirm Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // AJAX for adding to cart
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                fetch('place_order.php', {
                    method: 'POST',
                    body: new FormData(this)
                }).then(() => {
                    location.reload(); // Refresh to update cart
                });
            });
        });

        // Set minimum date for pickup (today)
        document.querySelector('input[name="pickup_date"]').min = new Date().toISOString().split('T')[0];
    </script>
    <script>
    // Show UPI ID field when UPI payment is selected
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const upiIdField = document.getElementById('upiIdField');
        if (this.value !== 'cod') {
            upiIdField.style.display = 'block';
            upiIdField.querySelector('input').required = true;
        } else {
            upiIdField.style.display = 'none';
            upiIdField.querySelector('input').required = false;
        }
    });
});

// Set minimum date for pickup (today)
document.querySelector('input[name="pickup_date"]').min = new Date().toISOString().split('T')[0];
    </script>
</body>

</html>