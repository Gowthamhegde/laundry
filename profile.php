<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$success_message = '';
$error_message = '';
$user = [];
$orders = [];
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

try {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update_profile'])) {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            if (empty($name) || empty($email)) {
                throw new Exception("Name and email are required");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }

            $update_stmt = $pdo->prepare("UPDATE customers SET name = ?, email = ?, phone = ? WHERE customer_id = ?");
            $update_stmt->execute([$name, $email, $phone, $user_id]);
            
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $success_message = "Profile updated successfully!";
            $active_tab = 'profile';
        }
    }

    if ($active_tab === 'orders') {
        $orders_stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY pickup_date DESC");
        $orders_stmt->execute([$user_id]);
        $orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Laundry Techs</title>
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
            background: linear-gradient(135deg, #4a6bff 0%, #3aa8ff 50%, #2ce4ff 100%);
            color: white;
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
            filter: brightness(0) invert(1);
        }
        
        .brand-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            letter-spacing: -0.5px;
        }
        
        .nav-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }
        
        .nav-links a {
            text-decoration: none;
            color: white;
            font-weight: 500;
            font-size: 1rem;
            transition: var(--transition);
            padding: 8px 12px;
            border-radius: 8px;
        }
        
        .nav-links a:hover {
            background-color: rgba(255,255,255,0.2);
        }
        
        .nav-links a.active {
            font-weight: 600;
            background-color: rgba(255,255,255,0.15);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
            overflow: hidden;
        }
        
        .user-avatar:hover {
            transform: scale(1.05);
            background-color: rgba(255,255,255,0.4);
        }
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 5%;
        }
        
        .profile-wrapper {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }
        
        .profile-sidebar {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
            height: fit-content;
        }
        
        .profile-main {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
        }
        
        .user-info {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid var(--primary-light);
            margin: 0 auto 15px;
            display: block;
        }
        
        .user-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .user-email {
            color: var(--gray);
            margin-bottom: 15px;
        }
        
        .member-since {
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 10px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            color: var(--dark);
            text-decoration: none;
            border-radius: 8px;
            transition: var(--transition);
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: var(--primary-light);
            color: var(--primary);
        }
        
        .sidebar-menu i {
            width: 20px;
            text-align: center;
        }
        
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 25px;
            color: var(--dark);
            position: relative;
            display: inline-block;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), #2ce4ff);
            border-radius: 2px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }
        
        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: var(--transition);
        }
        
        input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.2);
        }
        
        .btn {
            background: linear-gradient(90deg, var(--primary), #3aa8ff);
            color: white;
            border: none;
            padding: 14px 25px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn:hover {
            background: linear-gradient(90deg, #3a5bef, #2c97e6);
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary-light);
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 25px;
            border-radius: var(--border-radius);
            font-weight: 500;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            color: var(--success);
            border-left: 4px solid var(--success);
        }
        
        .alert-error {
            background-color: #ffebee;
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }
        
        .orders-grid {
            display: grid;
            gap: 20px;
        }
        
        .order-card {
            border: 1px solid #eee;
            border-radius: var(--border-radius);
            padding: 20px;
            transition: var(--transition);
        }
        
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-light);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .order-id {
            font-weight: 700;
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
        
        .status-processing {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .order-details {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        
        .order-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark);
        }
        
        .view-details {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .view-details:hover {
            text-decoration: underline;
        }
        
        .no-orders {
            text-align: center;
            padding: 40px 20px;
        }
        
        .no-orders i {
            font-size: 3rem;
            color: var(--gray);
            opacity: 0.5;
            margin-bottom: 20px;
        }
        
        .no-orders p {
            color: var(--gray);
            margin-bottom: 20px;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
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
            
            .profile-wrapper {
                grid-template-columns: 1fr;
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
            <a href="service.php">Services</a>
            <a href="my_orders.php">My Orders</a>
            <a href="#" class="active"><?php echo (htmlspecialchars(ucfirst($_SESSION['name']))) ?></a>
            <div class="user-avatar" onclick="window.location.href='#'">
                <i class="fas fa-user"></i>
            </div>
        </nav>
    </header>
    
    <div class="container">
        <div class="profile-wrapper">
            <!-- Sidebar -->
            <div class="profile-sidebar">
                <div class="user-info">
                    <img src="image/user_avatar.png" alt="User Avatar" class="avatar">
                    <h2 class="user-name"><?= htmlspecialchars($user['name'] ?? 'User') ?></h2>
                    <p class="user-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                    <p class="member-since">
                        <i class="far fa-calendar-alt"></i> 
                        Member since <?= isset($user['registration_date']) ? date('F Y', strtotime($user['registration_date'])) : 'N/A' ?>
                    </p>
                </div>
                
                <ul class="sidebar-menu">
                    <li><a href="?tab=profile" class="<?= $active_tab === 'profile' ? 'active' : '' ?>"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="?tab=orders" class="<?= $active_tab === 'orders' ? 'active' : '' ?>"><i class="fas fa-clipboard-list"></i> My Orders</a></li>
                    <li><a href="?tab=password" class="<?= $active_tab === 'password' ? 'active' : '' ?>"><i class="fas fa-lock"></i> Change Password</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="profile-main">
                <?php if ($error_message): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
                <?php endif; ?>
                
                <!-- Profile Tab -->
                <div id="profile-tab" class="tab-content <?= $active_tab === 'profile' ? 'active' : '' ?>">
                    <h2 class="section-title">Profile Information</h2>
                    
                    <form method="POST">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                        
                        <button type="submit" class="btn">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>
                
                <!-- Orders Tab -->
                <div id="orders-tab" class="tab-content <?= $active_tab === 'orders' ? 'active' : '' ?>">
                    <h2 class="section-title">My Orders</h2>
                    
                    <?php if (count($orders) > 0): ?>
                        <div class="orders-grid">
                            <?php foreach ($orders as $order): ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <div class="order-id">Order <span>#LDTS<?= htmlspecialchars($order['id']) ?></span></div>
                                        <div class="order-status status-<?= strtolower($order['status']) ?>">
                                            <?= htmlspecialchars($order['status']) ?>
                                        </div>
                                    </div>
                                    
                                    <div class="order-date">
                                        <i class="far fa-calendar-alt"></i>
                                        <?= date('F j, Y', strtotime($order['pickup_date'])) ?>
                                    </div>
                                    
                                    <div class="order-details">
                                        <div class="order-price">₹<?= number_format($order['total_price'], 2) ?></div>
                                        <a href="order_details.php?id=<?= $order['id'] ?>" class="view-details">
                                            View Details <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-orders">
                            <i class="fas fa-clipboard-list"></i>
                            <p>You haven't placed any orders yet.</p>
                            <a href="service.php" class="btn">
                                <i class="fas fa-tshirt"></i> Browse Services
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Change Password Tab -->
                <div id="password-tab" class="tab-content <?= $active_tab === 'password' ? 'active' : '' ?>">
                    <h2 class="section-title">Change Password</h2>
                    
                    <form id="change-password-form">
                        <div class="form-group">
                            <label for="current-password">Current Password</label>
                            <input type="password" id="current-password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new-password">New Password</label>
                            <input type="password" id="new-password" minlength="8" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm-password">Confirm New Password</label>
                            <input type="password" id="confirm-password" minlength="8" required>
                        </div>
                        
                        <button type="submit" class="btn">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password change form submission
        document.getElementById('change-password-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById('current-password').value;
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            
            if (newPassword !== confirmPassword) {
                alert('New passwords do not match');
                return;
            }
            
            // AJAX request to change password
            fetch('change_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    new_password: newPassword
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Password changed successfully!');
                    document.getElementById('change-password-form').reset();
                } else {
                    alert(data.message || 'Error changing password');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    </script>
</body>
</html>