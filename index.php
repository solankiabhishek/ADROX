<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADROX - Luxury Meets Technology | Premium Affiliate Products</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Three.js for 3D Animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
</head>
<body>
    <!-- HEADER & NAVIGATION -->
    <header>
        <nav>
            <div class="logo">
                <img src="assets/images/logo.png" alt="ADROX Logo" style="height: 50px;">
            </div>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#featured">Featured</a></li>
                <li><a href="#categories">Categories</a></li>
                <li><a href="user-dashboard.php">🛍️ Shop</a></li>
                <li><a href="my-orders.php">📦 Orders</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- HERO SECTION with 3D Animation -->
    <section class="hero" id="home">
        <div id="canvas-container"></div>
        <div class="hero-content">
            <h1>ADROX</h1>
            <p>Luxury Meets Technology</p>
            <a href="user-dashboard.php" class="hero-btn pulse">Start Shopping</a>
        </div>
    </section>

    <!-- FEATURED PRODUCTS -->
    <section class="featured-section" id="featured">
        <h2 class="section-title">Featured Products</h2>
        <p class="section-subtitle">Curated Premium Affiliate Products</p>
        
        <div class="products-grid" id="featuredProducts">
            <!-- Products will be loaded here via JavaScript -->
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                <p style="font-size: 1.2em; color: #999;">Loading premium products...</p>
            </div>
        </div>
    </section>

    <!-- CATEGORIES -->
    <section class="categories-section" id="categories">
        <h2 class="section-title">Browse Categories</h2>
        <p class="section-subtitle">Find exactly what you're looking for</p>
        
        <div class="categories-grid">
            <div class="category-card animate-on-scroll" onclick="loadProducts('Mobile Covers')">
                <div class="category-icon">📱</div>
                <div class="category-name">Mobile Covers</div>
                <div class="category-count">Premium phone protection</div>
            </div>
            <div class="category-card animate-on-scroll" onclick="loadProducts('Car Key Covers')">
                <div class="category-icon">🔑</div>
                <div class="category-name">Car Key Covers</div>
                <div class="category-count">Stylish key protection</div>
            </div>
            <div class="category-card animate-on-scroll" onclick="loadProducts('Accessories')">
                <div class="category-icon">✨</div>
                <div class="category-name">Accessories</div>
                <div class="category-count">Luxury lifestyle items</div>
            </div>
            <div class="category-card animate-on-scroll" onclick="loadProducts('Tech Gadgets')">
                <div class="category-icon">⚡</div>
                <div class="category-name">Tech Gadgets</div>
                <div class="category-count">Latest technology</div>
            </div>
        </div>
    </section>

    <!-- ALL PRODUCTS -->
    <section class="featured-section" style="background: white;">
        <h2 class="section-title">All Products</h2>
        <p class="section-subtitle">Complete collection of premium items</p>
        
        <div class="products-grid" id="productsContainer">
            <!-- Products will be loaded here via JavaScript -->
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section class="about-section" id="about">
        <div class="about-content">
            <div class="about-text">
                <h2>About ADROX</h2>
                <p>At ADROX, we believe that luxury and technology should go hand in hand. We curate the finest affiliate products that combine style, functionality, and innovation.</p>
                
                <p>Our mission is to provide you with premium products that elevate your lifestyle while maintaining affordable prices through our affiliate partnerships.</p>
                
                <ul class="about-features">
                    <li>Premium Quality Products</li>
                    <li>Best Affiliate Prices</li>
                    <li>Fast & Secure Checkout</li>
                    <li>Trusted Partners</li>
                    <li>24/7 Customer Support</li>
                </ul>
            </div>
            <div style="text-align: center;">
                <svg width="300" height="300" viewBox="0 0 300 300" style="opacity: 0.8;">
                    <circle cx="150" cy="150" r="100" fill="none" stroke="#E60012" stroke-width="2"/>
                    <circle cx="150" cy="150" r="80" fill="none" stroke="#d4a574" stroke-width="2"/>
                    <circle cx="150" cy="150" r="60" fill="none" stroke="#E60012" stroke-width="2"/>
                    <text x="150" y="160" text-anchor="middle" fill="#E60012" font-size="24" font-weight="bold">ADROX</text>
                </svg>
            </div>
        </div>
    </section>

    <!-- CONTACT SECTION -->
    <section class="contact-section" id="contact">
        <h2 class="section-title" style="color: var(--dark-gray);">Get In Touch</h2>
        <p class="section-subtitle">We'd love to hear from you</p>
        
        <form class="contact-form" id="contactForm">
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            
            <button type="submit" class="submit-btn">Send Message</button>
        </form>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About ADROX</h3>
                <p>Premium affiliate products combining luxury and technology. Your trusted partner for quality purchases.</p>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#featured">Featured</a></li>
                    <li><a href="#categories">Categories</a></li>
                    <li><a href="#about">About Us</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Categories</h3>
                <ul>
                    <li><a href="#" onclick="loadProducts('Mobile Covers'); return false;">Mobile Covers</a></li>
                    <li><a href="#" onclick="loadProducts('Car Key Covers'); return false;">Car Key Covers</a></li>
                    <li><a href="#" onclick="loadProducts('Accessories'); return false;">Accessories</a></li>
                    <li><a href="#" onclick="loadProducts('Tech Gadgets'); return false;">Tech Gadgets</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Contact</h3>
                <ul>
                    <li>Email: info@adrox.com</li>
                    <li>Phone: +91-XXXXXXXXXX</li>
                    <li>Available 24/7</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2026 ADROX - Luxury Meets Technology. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="assets/js/3d-animation.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
