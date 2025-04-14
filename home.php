<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Techs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="slide.css">
    <link rel="txt/javascript" href="slide.js">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: #f9f9f9;
            color: #333;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background-color: #2c3e50;
            color: white;
        }

        .navbar h1 {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar img {
            height: 50px;
        }

        .navbar a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .navbar a:hover {
            color: #1abc9c;
        }

        .hero {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            padding: 60px 20px;
            background: #eaf4ff;
            text-align: center;
        }

        .hero-content {
            max-width: 600px;
        }

        .hero h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 25px;
        }

        .hero a {
            display: inline-block;
            background: #27ae60;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .services-section {
            background: #fff;
            padding: 60px 20px;
            text-align: center;
        }

        .services-section h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .services-section p {
            margin-bottom: 30px;
        }

        .services {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }

        .service {
            background: #f7f7f7;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            width: 240px;
        }

        .service i {
            font-size: 2rem;
            color: #007bff;
            margin-bottom: 10px;
        }

        .service h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        footer {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <header class="navbar">
        <h1><img src="./image/logo.png" alt="Logo"> Laundry Techs</h1>
        <nav>
            <a href="#services">Services</a>
            <a href="price.php">Price list</a>
            <a href="register.php">Register</a>
            <a href="#contact">Contact</a>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h2>Fresh. Fast. Flawless.</h2>
            <p>Your clothes deserve the best care. Experience premium laundry services with Laundry Techs.</p>
            <a href="login.php">Place Your Order</a>
        </div>
    </section>
    <section class="slideshow-container">
        <div class="slide fade">
            <img src="./image/slide1.jpg" alt="Slide 1">
        </div>
        <div class="slide fade">
            <img src="./image/slide2.jpg" alt="Slide 2">
        </div>
        <div class="slide fade">
            <img src="./image/slide3.jpg" alt="Slide 3">
        </div>
        <a class="prev" onclick="plusSlides(-1)">❮</a>
        <a class="next" onclick="plusSlides(1)">❯</a>
    </section>

    <section class="services-section" id="services">
        <h2>Our Services</h2>
        <p>Daily wear, occasionals or household – we handle it all with precision.</p>
        <div class="services">
            <div class="service">
                <i class="fas fa-tshirt"></i>
                <h3>Wash & Fold</h3>
                <p>Everyday care with folding done just right.</p>
            </div>
            <div class="service">
                <i class="fas fa-iron"></i>
                <h3>Wash & Iron</h3>
                <p>Freshly washed and perfectly pressed clothing.</p>
            </div>
            <div class="service">
                <i class="fas fa-pump-soap"></i>
                <h3>Dry Cleaning</h3>
                <p>Delicate and designer items cleaned with love.</p>
            </div>
            <div class="service">
                <i class="fas fa-truck"></i>
                <h3>Pickup & Delivery</h3>
                <p>Convenient pickup and delivery at your doorstep.</p>
            </div>
        </div>
    </section>

    <footer id="contact">
        <p>📍 Prassanna colony, Basaveshwar Nagar , Hubbali | 📞 +91 9844444494 | ✉️ support@laundrytechs.com</p>
        <p>&copy; <?= date('Y') ?> Laundry Techs. All rights reserved.</p>
    </footer>
</body>

</html>