<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Techs - Premium Laundry Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4bbf73;
            --warning: #f0ad4e;
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f9f9f9;
            color: var(--dark);
            line-height: 1.6;
        }

        /* Premium Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5%;
            background: var(--gradient);
            color: white;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .navbar img {
            height: 50px;
            filter: brightness(0) invert(1);
            transition: transform 0.3s ease;
        }

        .navbar img:hover {
            transform: scale(1.05);
        }

        .navbar nav {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            padding: 5px 0;
        }

        .navbar a:hover {
            color: var(--accent);
        }

        .navbar a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: width 0.3s ease;
        }

        .navbar a:hover::after {
            width: 100%;
        }

        .navbar .user-icon {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .navbar .user-icon:hover {
            transform: scale(1.2);
            color: var(--accent);
        }

        /* Hero Section */
        .hero {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 60px 5%;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('./image/slide1.png') no-repeat center/cover;
            color: white;
            text-align: center;
            position: relative;
        }

        .hero-content {
            max-width: 800px;
            z-index: 2;
        }

        .hero h2 {
            font-size: 3.2rem;
            margin-bottom: 20px;
            font-weight: 700;
            line-height: 1.2;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .hero-btn {
            display: inline-block;
            background: var(--accent);
            color: white;
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border: 2px solid transparent;
        }

        .hero-btn:hover {
            background: transparent;
            border-color: var(--accent);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        /* Slideshow */
        .slideshow-container {
            position: relative;
            max-width: 100%;
            overflow: hidden;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .slide {
            display: none;
            width: 100%;
            height: 500px;
            object-fit: cover;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .prev, .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 2rem;
            font-weight: bold;
            padding: 16px;
            cursor: pointer;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            transition: 0.3s;
            z-index: 10;
        }

        .prev:hover, .next:hover {
            background: rgba(0, 0, 0, 0.7);
        }

        .next {
            right: 20px;
        }

        .prev {
            left: 20px;
        }

        /* About Section */
        .about {
            padding: 80px 5%;
            background: white;
            text-align: center;
        }

        .title {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: var(--primary);
            font-weight: 700;
        }

        .subtitle {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 30px;
            font-style: italic;
        }

        #p {
            max-width: 800px;
            margin: 0 auto 50px;
            font-size: 1.1rem;
            color: #555;
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 50px;
        }

        .feature {
            flex: 1 1 250px;
            max-width: 280px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .feature .image {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(67, 97, 238, 0.1);
            border-radius: 50%;
        }

        .feature .image img {
            max-width: 50px;
            max-height: 50px;
        }

        .feature h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: var(--primary);
        }

        .feature p {
            color: #666;
            font-size: 0.95rem;
        }

        /* Services Section */
        .services-section {
            padding: 80px 5%;
            background: #f5f7ff;
            text-align: center;
        }

        .services-section h2 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary);
        }

        .services-section p {
            max-width: 700px;
            margin: 0 auto 50px;
            color: #666;
            font-size: 1.1rem;
        }

        .services {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }

        .service {
            flex: 1 1 250px;
            max-width: 280px;
            background: white;
            padding: 30px 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .service:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .service img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .service:hover img {
            transform: scale(1.1);
        }

        .service h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: var(--primary);
        }

        .service p {
            color: #666;
            font-size: 0.95rem;
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

        /* Responsive Design */
        @media (max-width: 992px) {
            .navbar {
                flex-direction: column;
                padding: 15px;
            }
            
            .navbar h1 {
                margin-bottom: 15px;
            }
            
            .hero h2 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 768px) {
            .hero h2 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .slide {
                height: 400px;
            }
            
            .title {
                font-size: 2rem;
            }
        }

        @media (max-width: 576px) {
            .navbar nav {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }
            
            .hero h2 {
                font-size: 1.8rem;
            }
            
            .hero-btn {
                padding: 12px 25px;
                font-size: 1rem;
            }
            
            .slide {
                height: 300px;
            }
            
            .feature, .service {
                flex: 1 1 100%;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Premium Navbar -->
    <header class="navbar">
        <h1><img src="./image/logo.png" alt="Laundry Techs Logo"> Laundry Techs</h1>
        <nav>
            <a href="#services">Services</a>
            <a href="#about-us">About</a>
            <a href="price.php">Price List</a>
            <a href="#contact">Contact</a>
            <a href="sign_in.html">Register</a>
            <a href="admin/adminlogin.php" class="user-icon"><i class="fa-solid fa-circle-user"></i></a>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h2>Fresh  Fast  Flawless </h2>
            <p>Your clothes deserve the best care. Experience premium laundry services with Laundry Techs.</p>
            <a href="login.php" class="hero-btn">Place Your Order</a>
        </div>
    </section>

    <!-- Slideshow -->
    <section class="slideshow-container">
        <div class="slideshow-container">
            <div class="slide fade"><img src="./image/slide1.png" alt="Laundry Service"></div>
            <div class="slide fade"><img src="./image/slide2.png" alt="Dry Cleaning"></div>
            <div class="slide fade"><img src="./image/slide3.png" alt="Professional Service"></div>
            <div class="slide fade"><img src="./image/slide4.jpg" alt="Happy Customers"></div>
            <a class="prev" onclick="changeSlide(-1)">❮</a>
            <a class="next" onclick="changeSlide(1)">❯</a>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about-us">
        <div class="container">
            <h1 class="title">Why Laundry Techs?</h1>
            <p class="subtitle">(Why not, actually?)</p>
            <p id="p">We pick up and deliver at your doorstep… and that too at amazingly affordable prices. Everyday laundry, next-day delivery! Couldn't get better than that.</p>

            <div class="features">
                <div class="feature">
                    <div class="image"><img src="./image/AtBengaluru.png" alt="Location"></div>
                    <h3>At Hubli-Dharawad</h3>
                    <p>A professional laundry service near you that delivers high standards of quality, care, and service.</p>
                </div>
                <div class="feature">
                    <div class="image"><img src="./image/Atyourdoor.png" alt="At Your Door"></div>
                    <h3>At your door</h3>
                    <p>Pickup and drop right at your doorstep—no waiting or chasing required!</p>
                </div>
                <div class="feature">
                    <div class="image"><img src="./image/Atyourservice.png" alt="Service"></div>
                    <h3>At your service</h3>
                    <p>24-hour laundry service means exactly that—fast, reliable, and clockwork efficient.</p>
                </div>
                <div class="feature">
                    <div class="image"><img src="./image/Atirrisistableprices.png" alt="Affordable Prices"></div>
                    <h3>At irresistible prices</h3>
                    <p>Super affordable rates with reprocessing or refund options—because we care!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section" id="services">
        <h2>Our Services</h2>
        <p>Daily wear, occasionals or household – we handle it all with precision.</p>
        <div class="services">
            <div class="service">
                <img src="./image/wash_fold.png" alt="Wash Fold">
                <h3>Wash & Fold</h3>
                <p>Everyday care with folding done just right.</p>
            </div>
            <div class="service">
                <img src="./image/wash_iron.png" alt="Wash Iron">
                <h3>Wash & Iron</h3>
                <p>Freshly washed and perfectly pressed clothing.</p>
            </div>
            <div class="service">
                <img src="./image/fashion.png" alt="Dry Clean">
                <h3>Dry Cleaning</h3>
                <p>Delicate and designer items cleaned with love.</p>
            </div>
            <div class="service">
                <img src="./image/pickup-truck.png" alt="Pickup & Delivery">
                <h3>Pickup & Delivery</h3>
                <p>Convenient pickup and delivery at your doorstep.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
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
                    <li><a href="#services"><i class="fas fa-chevron-right"></i> Services</a></li>
                    <li><a href="#about-us"><i class="fas fa-chevron-right"></i>About us</a></li>
                    <li><a href="price.php"><i class="fas fa-chevron-right"></i> Pricelist</a></li>
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

    <script>
        // Slideshow functionality
        let slideIndex = 0;
        showSlides();

        function showSlides() {
            let slides = document.getElementsByClassName("slide");
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slideIndex++;
            if (slideIndex > slides.length) {slideIndex = 1}
            slides[slideIndex-1].style.display = "block";
            setTimeout(showSlides, 5000); // Change image every 5 seconds
        }

        function changeSlide(n) {
            slideIndex += n;
            let slides = document.getElementsByClassName("slide");
            if (slideIndex > slides.length) {slideIndex = 1}
            if (slideIndex < 1) {slideIndex = slides.length}
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slides[slideIndex-1].style.display = "block";
        }
    </script>
</body>
</html>