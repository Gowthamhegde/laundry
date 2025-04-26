<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Laundry Techs</title>
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
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
            flex: 1;
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
            background: linear-gradient(90deg, #4a6bff, #2ce4ff);
            border-radius: 2px;
        }
        
        .page-description {
            color: var(--gray);
            margin-bottom: 40px;
            max-width: 700px;
            font-size: 1.1rem;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }
        
        .service-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            transition: var(--transition);
            border-top: 4px solid var(--primary);
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        }
        
        .service-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .service-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        .service-description {
            color: var(--gray);
            margin-bottom: 20px;
            flex-grow: 1;
        }
        
        .service-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px dashed #eee;
        }
        
        .service-duration {
            color: var(--gray);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .order-btn {
            background: linear-gradient(90deg, #4a6bff, #3aa8ff);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .order-btn:hover {
            background: linear-gradient(90deg, #3a5bef, #2c97e6);
            transform: translateY(-2px);
        }
        
        .footer {
            background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
            color: white;
            padding: 40px 5%;
            margin-top: 60px;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
        }
        
        .footer-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .footer-logo img {
            height: 30px;
            filter: brightness(0) invert(1);
        }
        
        .footer-logo span {
            font-size: 1.2rem;
            font-weight: 700;
        }
        
        .footer-about {
            max-width: 300px;
        }
        
        .footer-about p {
            color: #b0b0b0;
            margin-top: 15px;
            line-height: 1.6;
        }
        
        .footer-links h3, .footer-contact h3 {
            font-size: 1.1rem;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }
        
        .footer-links h3::after, .footer-contact h3::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, #4a6bff, #2ce4ff);
        }
        
        .footer-links ul {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: #b0b0b0;
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .footer-links a:hover {
            color: white;
            transform: translateX(5px);
        }
        
        .footer-contact p {
            color: #b0b0b0;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-links a {
            color: white;
            background-color: rgba(255,255,255,0.1);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        
        .social-links a:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #b0b0b0;
            font-size: 0.9rem;
        }
        a{
            text-decoration: none;
            color: inherit;
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
            
            .services-grid {
                grid-template-columns: 1fr;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
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
            <a href="service.php" class="active">Services</a>
            <a href="orders.php">My Orders</a>
            <a href="account.php">Account</a>
            <div class="user-avatar" onclick="window.location.href='account.php'">
                <i class="fas fa-user"></i>
            </div>
        </nav>
    </header>
    
    <div class="container">
        <h1 class="page-title">Our Laundry Services</h1>
        <p class="page-description">
            Professional laundry services with quick turnaround times. Select from our range of services below.
        </p>
        
        <div class="services-grid">
            <!-- Wash & Fold Service -->
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-tshirt"></i>
                </div>
                <h3 class="service-name">Wash & Fold</h3>
                <p class="service-description">
                    Professional washing and careful folding of your clothes. Perfect for everyday wear and casual clothing.
                </p>
                <div class="service-meta">
                    <div class="service-duration">
                        <i class="far fa-clock"></i>
                        24 hours turnaround
                    </div>
                    <button class="order-btn" onclick="window.location.href='order.php?service=wash_fold'">
                        <i class="fas fa-shopping-cart"></i> Order
                    </button>
                </div>
            </div>
            
            <!-- Wash & Iron Service -->
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-tshirt"></i>
                </div>
                <h3 class="service-name">Wash & Iron</h3>
                <p class="service-description">
                    Complete care including washing and professional ironing. Ideal for workwear and formal clothing.
                </p>
                <div class="service-meta">
                    <div class="service-duration">
                        <i class="far fa-clock"></i>
                        36 hours turnaround
                    </div>
                    <button class="order-btn" onclick="window.location.href='order.php?service=wash_iron'">
                        <i class="fas fa-shopping-cart"></i> Order
                    </button>
                </div>
            </div>
            
            <!-- Steam Iron Service -->
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-wind"></i>
                </div>
                <h3 class="service-name">Steam Iron</h3>
                <p class="service-description">
                    Professional steam ironing to remove wrinkles and freshen up your clothes without washing.
                </p>
                <div class="service-meta">
                    <div class="service-duration">
                        <i class="far fa-clock"></i>
                        12 hours turnaround
                    </div>
                    <button class="order-btn" onclick="window.location.href='order.php?service=steam_iron'">
                        <i class="fas fa-shopping-cart"></i> Order
                    </button>
                </div>
            </div>
            
            <!-- Dry Cleaning Service -->
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-tshirt"></i>
                </div>
                <h3 class="service-name">Dry Cleaning</h3>
                <p class="service-description">
                    Specialized cleaning for delicate fabrics and special garments that require professional care.
                </p>
                <div class="service-meta">
                    <div class="service-duration">
                        <i class="far fa-clock"></i>
                        48 hours turnaround
                    </div>
                    <button class="order-btn" onclick="window.location.href='order.php?service=dry_clean'">
                        <i class="fas fa-shopping-cart"></i> Order
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-about">
                <div class="footer-logo">
                    <img src="image/logo.png" alt="Laundry Techs">
                    <span>Laundry Techs</span>
                </div>
                <p>
                    Professional laundry services with the latest technology. 
                    We care for your clothes like they're our own.
                </p>
            </div>
            
            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                    <li><a href="services.php"><i class="fas fa-chevron-right"></i> Services</a></li>
                    <li><a href="orders.php"><i class="fas fa-chevron-right"></i> My Orders</a></li>
                    <li><a href="profile.php"><i class="fas fa-chevron-right"></i> My Account</a></li>
                </ul>
            </div>
            
            <div class="footer-contact">
                <h3>Contact Us</h3>
                <p><i class="fas fa-map-marker-alt"></i>Basaveshwar colony vidynagar , Hubblai</p>
                <p><i class="fas fa-phone"></i> +91 98444494</p>
                <p><i class="fas fa-envelope"></i> support@laundrytechs.com</p>
                
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            &copy; <?php echo date('Y'); ?> Laundry Techs. All Rights Reserved.
        </div>
    </footer>
</body>
</html>