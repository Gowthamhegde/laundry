<?php
// price.php
include 'db.php';
?>
<!DOCTYPE html>
<html>

<head>
  <title>Laundry Mate – Pricelist</title>
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

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f0f6fc;
    }

    .header {
      background-color: #fbc500;
      padding: 20px;
      font-size: 28px;
      text-align: center;
      font-weight: bold;
    }

    .container {
      display: flex;
      padding: 20px;
    }

    .service-type-buttons {
      display: flex;
      justify-content: center;
      flex-direction: column;
      align-items: center;
      width: 180px;
      margin-right: 20px;
      margin-top: 10px;
    }

    .service-btn {
      background: #fff;
      border: 2px solid #eee;
      padding: 15px;
      margin-bottom: 10px;
      font-size: 14px;
      text-align: center;
      cursor: pointer;
      display: flex;
      align-items: center;
      border-radius: 10px;
      transition: 0.2s;
      padding: 10px 20px;
      background: #fff;
      border: none;
      border-radius: 20px;
      font-weight: bold;
      cursor: pointer;
    }

    .service-btn img {
      width: 24px;
      height: 24px;
      margin-right: 10px;
    }

    .service-btn.active {
      background-color: #0078d4;
      color: #fff;
    }

    .category-buttons {
      display: flex;
      justify-content: center;
      flex-direction: row;
      align-items: center;
      margin-bottom: 20px;
      margin-top: 30px;
      gap: 10px;
    }

    .category-btn {
      padding: 10px 20px;
      background: #fff;
      border: none;
      border-radius: 20px;
      font-weight: bold;
      cursor: pointer;
    }

    .category-btn.active {
      background-color: #0078d4;
      color: #fff;
    }

    .grid-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
      gap: 15px;
      flex: 1;
    }

    .item-card {
      background: #fff;
      border: 1px solid #ccc;
      padding: 15px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .item-card img {
      width: 40px;
      height: 40px;
    }


    .price-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      max-width: 1000px;
      margin: 0 auto;
    }

    .price-card {
      background: white;
      border: 1px solid #ddd;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
      text-align: center;
      transition: transform 0.2s;
    }

    .price-card:hover {
      transform: translateY(-5px);
    }

    .price-card h3 {
      margin: 0;
      font-size: 18px;
      color: #333;
    }

    .price-card p {
      font-size: 16px;
      color: #007bff;
      margin-top: 8px;
      font-weight: bold;
    }

    .c {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: row;
      margin-top: 20px;
    }

    .footer {
      background-color: #fbc500;
      padding: 10px;
      text-align: center;
      font-size: 14px;
      margin-top: 20px;
    }
  </style>
</head>

<body>
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
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="place_order.php">
                            <i class="fas fa-order"></i>Place Order
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="login.php" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="image/user_avatar.png" class="user-avatar" alt="User">
                            <?= htmlspecialchars($_SESSION['name'] ?? 'Account') ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

  <div class="category-buttons">
    <button class="btn category-btn active" data-category="1">MEN</button>
    <button class="btn category-btn" data-category="2">WOMEN</button>
    <button class="btn category-btn" data-category="3">KIDS</button>
    <button class="btn category-btn" data-category="4">HOUSEHOLD</button>
  </div>
  <div class="c">
    <div class="service-type-buttons">
      <button class="btn service-btn active" data-service="wash_fold">Wash + Fold</button>
      <button class="btn service-btn" data-service="wash_iron">Wash + Iron</button>
      <button class="btn service-btn" data-service="steam_iron">Steam Iron</button>
      <button class="btn service-btn" data-service="dry_clean">Dry Clean</button>
    </div>

    <div class="price-container" id="priceList">Loading...</div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    let selectedCategory = '1';
    let selectedService = 'wash_fold';

    function loadPrices() {
      $.ajax({
        url: 'fetch_prices.php',
        method: 'POST',
        data: {
          category: selectedCategory,
          service: selectedService
        },
        success: function(response) {
          $('#priceList').html(response);
        }
      });
    }

    $(document).ready(function() {
      loadPrices();

      $('.category-btn').click(function() {
        $('.category-btn').removeClass('active');
        $(this).addClass('active');
        selectedCategory = $(this).data('category');
        loadPrices();
      });

      $('.service-btn').click(function() {
        $('.service-btn').removeClass('active');
        $(this).addClass('active');
        selectedService = $(this).data('service');
        loadPrices();
      });
    });
  </script>
</body>

</html>