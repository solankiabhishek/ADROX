<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Initialize database
require_once 'includes/db_init.php';

$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Simulated user session (in production, use real authentication)
$user_logged_in = true;
$user_name = "John Doe";
$user_email = "john@example.com";
$user_cart_items = 3;

// Get products
$products = get_all_products($conn);
$categories = get_all_categories($conn);

// Get featured products
$featured_products = get_featured_products($conn);

// Current tab
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'shop';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADROX - Shop Premium Products | User Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            color: #2c2c2c;
            min-height: 100vh;
        }

        /* ============================================
           HEADER & NAVIGATION
           ============================================ */

        header {
            background: linear-gradient(90deg, #1a1a1a 0%, #2c2c2c 100%);
            color: white;
            padding: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .top-bar {
            background: rgba(0, 0, 0, 0.5);
            padding: 10px 40px;
            display: flex;
            justify-content: space-between;
            font-size: 0.85em;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .top-bar-left, .top-bar-right {
            display: flex;
            gap: 25px;
        }

        .top-bar a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s;
        }

        .top-bar a:hover {
            color: #E60012;
        }

        .header-main {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            gap: 30px;
        }

        .logo {
            font-size: 1.8em;
            font-weight: 800;
            color: #E60012;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 150px;
        }

        .logo span {
            color: white;
            font-size: 0.8em;
            letter-spacing: 1px;
        }

        .search-bar {
            flex: 1;
            max-width: 500px;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid transparent;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.95);
            font-size: 0.95em;
            transition: all 0.3s;
        }

        .search-bar input:focus {
            outline: none;
            background: white;
            box-shadow: 0 0 0 3px rgba(230, 0, 18, 0.2);
        }

        .search-bar input::placeholder {
            color: #999;
        }

        .header-right {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .user-dropdown:hover {
            background: rgba(230, 0, 18, 0.2);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #E60012, #cc000f);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.1em;
            box-shadow: 0 2px 8px rgba(230, 0, 18, 0.3);
        }

        .user-info {
            text-align: right;
            font-size: 0.9em;
        }

        .user-info small {
            display: block;
            color: #ccc;
            font-size: 0.8em;
        }

        .user-info strong {
            display: block;
            color: white;
            font-size: 0.95em;
        }

        .cart-icon-wrapper {
            position: relative;
            cursor: pointer;
            font-size: 1.8em;
            transition: transform 0.3s;
        }

        .cart-icon-wrapper:hover {
            transform: scale(1.1);
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, #E60012, #cc000f);
            color: white;
            border-radius: 50%;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75em;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(230, 0, 18, 0.3);
        }

        .logout-btn {
            padding: 10px 20px;
            background: linear-gradient(135deg, #E60012, #cc000f);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 0.9em;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 0, 18, 0.3);
        }

        /* ============================================
           NAVIGATION TABS
           ============================================ */

        .nav-tabs {
            background: white;
            display: flex;
            border-bottom: 2px solid #e0e0e0;
            padding: 0 40px;
            gap: 0;
        }

        .nav-tabs a {
            padding: 15px 25px;
            color: #666;
            text-decoration: none;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            font-weight: 500;
        }

        .nav-tabs a.active {
            color: #E60012;
            border-bottom-color: #E60012;
        }

        .nav-tabs a:hover {
            color: #E60012;
        }

        /* ============================================
           FEATURED BANNER
           ============================================ */

        .featured-banner {
            background: linear-gradient(135deg, #E60012 0%, #cc000f 100%);
            color: white;
            padding: 50px 40px;
            margin: 30px 40px;
            border-radius: 15px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            box-shadow: 0 10px 40px rgba(230, 0, 18, 0.2);
        }

        .featured-content h2 {
            font-size: 2.2em;
            margin-bottom: 15px;
            font-weight: 800;
        }

        .featured-content p {
            font-size: 1.1em;
            margin-bottom: 25px;
            opacity: 0.95;
            line-height: 1.6;
        }

        .featured-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.3);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85em;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .featured-btn {
            display: inline-block;
            padding: 14px 35px;
            background: white;
            color: #E60012;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .featured-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 25px rgba(0, 0, 0, 0.3);
        }

        .featured-image {
            text-align: center;
            font-size: 6em;
        }

        /* ============================================
           DASHBOARD CONTAINER
           ============================================ */

        .dashboard-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 25px;
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 40px;
        }

        /* SIDEBAR */
        .sidebar {
            background: white;
            border-radius: 12px;
            padding: 25px;
            height: fit-content;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 200px;
        }

        .sidebar h3 {
            color: #E60012;
            margin-bottom: 20px;
            font-size: 1.15em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-section {
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .sidebar-section:last-child {
            border-bottom: none;
        }

        .sidebar-section label {
            display: block;
            margin-bottom: 12px;
            font-size: 0.9em;
            color: #2c2c2c;
            font-weight: 600;
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: normal;
            cursor: pointer;
            margin: 0;
            transition: color 0.2s;
        }

        .checkbox-group label:hover {
            color: #E60012;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #E60012;
        }

        .price-range {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .price-range input {
            flex: 1;
            padding: 8px 10px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.85em;
            transition: border-color 0.2s;
        }

        .price-range input:focus {
            outline: none;
            border-color: #E60012;
        }

        .price-range-separator {
            color: #ccc;
            font-weight: bold;
        }

        /* MAIN CONTENT */
        .main-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .section-header h2 {
            font-size: 1.9em;
            color: #2c2c2c;
            font-weight: 700;
        }

        .sort-controls {
            display: flex;
            gap: 12px;
        }

        .sort-controls select {
            padding: 10px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            font-weight: 500;
            transition: border-color 0.2s;
            color: #2c2c2c;
        }

        .sort-controls select:hover {
            border-color: #E60012;
        }

        .sort-controls select:focus {
            outline: none;
            border-color: #E60012;
        }

        /* ============================================
           PRODUCTS GRID
           ============================================ */

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 22px;
            margin-top: 20px;
        }

        .product-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            display: flex;
            flex-direction: column;
            height: 100%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }

        .product-card:hover {
            box-shadow: 0 12px 35px rgba(230, 0, 18, 0.15);
            transform: translateY(-8px);
            border-color: #E60012;
        }

        .product-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4em;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }

        .product-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .product-card:hover .product-image::before {
            opacity: 1;
        }

        .product-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: linear-gradient(135deg, #E60012, #cc000f);
            color: white;
            padding: 7px 14px;
            border-radius: 20px;
            font-size: 0.75em;
            font-weight: 700;
            box-shadow: 0 3px 10px rgba(230, 0, 18, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-info {
            padding: 18px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .product-category {
            font-size: 0.75em;
            color: #E60012;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .product-name {
            font-size: 0.95em;
            font-weight: 700;
            color: #2c2c2c;
            margin-bottom: 10px;
            line-height: 1.4;
            height: 2.8em;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-rating {
            display: flex;
            gap: 3px;
            margin-bottom: 10px;
            font-size: 0.9em;
        }

        .star {
            color: #FFB800;
        }

        .product-price-section {
            margin-bottom: 12px;
        }

        .product-original-price {
            font-size: 0.8em;
            color: #999;
            text-decoration: line-through;
            margin-right: 8px;
        }

        .product-discount {
            font-size: 0.8em;
            color: #4CAF50;
            font-weight: 700;
        }

        .product-price {
            font-size: 1.4em;
            font-weight: 800;
            color: #E60012;
            margin-bottom: 12px;
        }

        .buy-btn {
            width: 100%;
            padding: 12px 16px;
            background: linear-gradient(135deg, #E60012, #cc000f);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            transition: all 0.3s;
            font-size: 0.9em;
            box-shadow: 0 3px 10px rgba(230, 0, 18, 0.2);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .buy-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(230, 0, 18, 0.3);
        }

        .buy-btn:active {
            transform: translateY(0);
        }

        /* ============================================
           USER ACCOUNT SECTION
           ============================================ */

        .account-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 40px;
        }

        .account-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .account-section h2 {
            color: #E60012;
            font-size: 1.5em;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .account-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .info-card {
            background: linear-gradient(135deg, #f9f9f9 0%, #f0f0f0 100%);
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #E60012;
        }

        .info-label {
            font-size: 0.85em;
            color: #999;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .info-value {
            font-size: 1.1em;
            color: #2c2c2c;
            font-weight: 700;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            background: linear-gradient(135deg, #E60012, #cc000f);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 0.9em;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 0, 18, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #666, #555);
        }

        /* ============================================
           RESPONSIVE
           ============================================ */

        @media (max-width: 1024px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }

            .featured-banner {
                grid-template-columns: 1fr;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .header-main {
                flex-wrap: wrap;
                padding: 10px 20px;
            }

            .search-bar {
                order: 3;
                flex-basis: 100%;
                max-width: none;
                margin: 10px 0;
            }

            .featured-banner {
                padding: 30px 20px;
                margin: 20px 20px;
            }

            .featured-content h2 {
                font-size: 1.6em;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 15px;
            }

            .dashboard-container {
                padding: 0 20px;
                margin: 20px auto;
            }

            .top-bar {
                padding: 8px 20px;
                flex-direction: column;
                gap: 8px;
            }

            .nav-tabs {
                padding: 0 20px;
                overflow-x: auto;
            }

            .nav-tabs a {
                padding: 12px 18px;
                font-size: 0.9em;
                white-space: nowrap;
            }

            .account-section {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .logo {
                font-size: 1.5em;
                min-width: auto;
            }

            .header-right {
                gap: 15px;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            }

            .featured-banner {
                padding: 25px 15px;
            }

            .featured-image {
                font-size: 3em;
            }
        }
    </style>
</head>
<body>
    <!-- TOP BAR -->
    <div class="top-bar">
        <div class="top-bar-left">
            <a href="#">📞 Hotline 24/7: 088-2458004S</a>
            <a href="#" onclick="alert('Track your orders here'); return false;">📍 Track Order</a>
        </div>
        <div class="top-bar-right">
            <a href="#" onclick="alert('Currency selection'); return false;">💱 USD</a>
            <a href="#" onclick="alert('Language selection'); return false;">🌐 English</a>
        </div>
    </div>

    <!-- HEADER -->
    <header>
        <div class="header-main">
            <div class="logo">🔴 ADROX <span>STORE</span></div>
            
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search for products...">
            </div>

            <div class="header-right">
                <div class="cart-icon-wrapper" onclick="alert('Shopping cart feature coming soon!')">
                    🛒
                    <div class="cart-badge"><?php echo $user_cart_items; ?></div>
                </div>

                <div class="user-dropdown" onclick="alert('User profile menu')">
                    <div class="user-avatar"><?php echo substr($user_name, 0, 1); ?></div>
                    <div class="user-info">
                        <small>Welcome</small>
                        <strong><?php echo explode(' ', $user_name)[0]; ?></strong>
                    </div>
                </div>

                <button class="logout-btn" onclick="if(confirm('Are you sure you want to logout?')) { window.location.href='index.php'; }">🚪 Logout</button>
            </div>
        </div>
    </header>

    <!-- NAVIGATION TABS -->
    <div class="nav-tabs">
        <a href="?tab=shop" class="<?php echo ($current_tab === 'shop') ? 'active' : ''; ?>">🛍️ Shop</a>
        <a href="?tab=orders" class="<?php echo ($current_tab === 'orders') ? 'active' : ''; ?>">📦 My Orders</a>
        <a href="?tab=account" class="<?php echo ($current_tab === 'account') ? 'active' : ''; ?>">👤 My Account</a>
        <a href="?tab=wishlist" class="<?php echo ($current_tab === 'wishlist') ? 'active' : ''; ?>">❤️ Wishlist</a>
    </div>

    <?php if ($current_tab === 'shop'): ?>
        <!-- FEATURED BANNER -->
        <div class="featured-banner">
            <div class="featured-content">
                <div class="featured-badge">🌟 FEATURED COLLECTION</div>
                <h2>Premium Products for You</h2>
                <p>Discover our curated selection of luxury products that combine style, quality, and functionality. Limited time offers available!</p>
                <a href="#products" class="featured-btn">👉 Shop Now</a>
            </div>
            <div class="featured-image">🎁</div>
        </div>

        <!-- DASHBOARD -->
        <div class="dashboard-container">
            <!-- SIDEBAR FILTERS -->
            <div class="sidebar">
                <h3>🔍 Filters</h3>

                <div class="sidebar-section">
                    <label>Categories</label>
                    <div class="checkbox-group">
                        <?php foreach ($categories as $cat): ?>
                            <label>
                                <input type="checkbox" class="category-filter" value="<?php echo htmlspecialchars($cat['name']); ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="sidebar-section">
                    <label>Price Range (₹)</label>
                    <div class="price-range">
                        <input type="number" id="priceMin" placeholder="Min" value="0" min="0">
                        <span class="price-range-separator">—</span>
                        <input type="number" id="priceMax" placeholder="Max" value="10000" min="0">
                    </div>
                    <button class="btn" style="width: 100%; margin-top: 10px; padding: 10px; background: #E60012; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.9em; transition: all 0.3s;" onclick="filterProducts()" onmouseover="this.style.background='#cc000f'" onmouseout="this.style.background='#E60012'">✓ Apply Filter</button>
                    <button class="btn" style="width: 100%; margin-top: 8px; padding: 10px; background: #999; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.9em; transition: all 0.3s;" onclick="resetPriceFilter()" onmouseover="this.style.background='#777' " onmouseout="this.style.background='#999'">✕ Clear</button>
                </div>

                <div class="sidebar-section">
                    <label>Ratings</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" class="rating-filter" value="5"> ⭐⭐⭐⭐⭐ 5 Star</label>
                        <label><input type="checkbox" class="rating-filter" value="4"> ⭐⭐⭐⭐ 4+ Stars</label>
                        <label><input type="checkbox" class="rating-filter" value="3"> ⭐⭐⭐ 3+ Stars</label>
                    </div>
                </div>

                <div class="sidebar-section">
                    <label>Availability</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" class="availability-filter" value="in-stock" checked> ✓ In Stock</label>
                        <label><input type="checkbox" class="availability-filter" value="upcoming"> ⏳ Upcoming</label>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <div class="main-content">
                <div class="section-header">
                    <h2 id="productCount">🛍️ Shop Products</h2>
                    <div class="sort-controls">
                        <select id="sortBy" onchange="sortProducts()">
                            <option value="">Sort by: Newest</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="rating">Best Rating</option>
                        </select>
                    </div>
                </div>

                <!-- PRODUCTS GRID -->
                <div class="products-grid" id="productsGrid">
                    <?php 
                    if (!empty($products)) {
                        foreach ($products as $product) {
                            $discount = rand(5, 20);
                            $original_price = $product['price'] + ($product['price'] * 0.2);
                            ?>
                            <div class="product-card" data-category="<?php echo htmlspecialchars($product['category']); ?>" data-price="<?php echo $product['price']; ?>">
                                <div class="product-image">
                                    📦
                                    <?php if ($product['is_featured']): ?>
                                        <div class="product-badge">⭐ Featured</div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                                    <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                    <div class="product-rating">
                                        <span class="star">★</span>
                                        <span class="star">★</span>
                                        <span class="star">★</span>
                                        <span class="star">★</span>
                                        <span class="star">★</span>
                                    </div>
                                    <div class="product-price-section">
                                        <span class="product-original-price">₹<?php echo number_format($original_price, 2); ?></span>
                                        <span class="product-discount">-<?php echo $discount; ?>%</span>
                                    </div>
                                    <div class="product-price">₹<?php echo number_format($product['price'], 2); ?></div>
                                    <a href="<?php echo htmlspecialchars($product['affiliate_link']); ?>" target="_blank" class="buy-btn">🛒 Buy Now</a>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #999;"><p style="font-size: 1.1em;">No products available</p></div>';
                    }
                    ?>
                </div>
            </div>
        </div>

    <?php elseif ($current_tab === 'account'): ?>
        <!-- ACCOUNT SECTION -->
        <div class="account-container">
            <div class="account-section">
                <h2>👤 Account Information</h2>
                <div class="account-info">
                    <div class="info-card">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?php echo $user_name; ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Email Address</div>
                        <div class="info-value"><?php echo $user_email; ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Account Status</div>
                        <div class="info-value" style="color: #4CAF50;">✓ Active</div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Member Since</div>
                        <div class="info-value">Jan 2024</div>
                    </div>
                </div>
                <div class="btn-group">
                    <button class="btn" onclick="alert('Edit profile feature')">✏️ Edit Profile</button>
                    <button class="btn" onclick="alert('Change password feature')">🔐 Change Password</button>
                    <button class="btn btn-secondary" onclick="alert('Notification settings')">🔔 Notifications</button>
                </div>
            </div>

            <div class="account-section">
                <h2>📍 Address Book</h2>
                <div class="account-info">
                    <div class="info-card">
                        <div class="info-label">Default Shipping Address</div>
                        <div class="info-value">Not Set</div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Default Billing Address</div>
                        <div class="info-value">Not Set</div>
                    </div>
                </div>
                <div class="btn-group">
                    <button class="btn" onclick="alert('Add new address')">+ Add Address</button>
                    <button class="btn btn-secondary" onclick="alert('Manage addresses')">📋 Manage</button>
                </div>
            </div>

            <div class="account-section">
                <h2>🔒 Security & Privacy</h2>
                <div class="account-info">
                    <div class="info-card">
                        <div class="info-label">Last Login</div>
                        <div class="info-value">Today</div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Two-Factor Authentication</div>
                        <div class="info-value">Disabled</div>
                    </div>
                </div>
                <div class="btn-group">
                    <button class="btn" onclick="alert('Enable 2FA')">🛡️ Enable 2FA</button>
                    <button class="btn btn-secondary" onclick="alert('Download data')">📥 Download Data</button>
                </div>
            </div>
        </div>

    <?php elseif ($current_tab === 'orders'): ?>
        <!-- ORDERS SECTION -->
        <div class="account-container">
            <div class="account-section">
                <h2>📦 My Orders</h2>
                <div style="text-align: center; padding: 40px; color: #999;">
                    <p style="font-size: 2em; margin-bottom: 15px;">📭</p>
                    <p style="font-size: 1.1em;">No orders yet</p>
                    <p style="margin-top: 10px;">Start shopping to see your orders here</p>
                    <a href="?tab=shop" class="btn" style="margin-top: 20px; display: inline-block;">🛍️ Continue Shopping</a>
                </div>
            </div>
        </div>

    <?php elseif ($current_tab === 'wishlist'): ?>
        <!-- WISHLIST SECTION -->
        <div class="account-container">
            <div class="account-section">
                <h2>❤️ My Wishlist</h2>
                <div style="text-align: center; padding: 40px; color: #999;">
                    <p style="font-size: 2em; margin-bottom: 15px;">💔</p>
                    <p style="font-size: 1.1em;">Your wishlist is empty</p>
                    <p style="margin-top: 10px;">Add your favorite products to your wishlist</p>
                    <a href="?tab=shop" class="btn" style="margin-top: 20px; display: inline-block;">👉 Browse Products</a>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            filterProducts();
        });

        // Category filter
        document.querySelectorAll('.category-filter').forEach(checkbox => {
            checkbox.addEventListener('change', filterProducts);
        });

        // Price filter - now uses buttons, but allow Enter key
        document.getElementById('priceMin').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') filterProducts();
        });
        document.getElementById('priceMax').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') filterProducts();
        });

        // Rating filter
        document.querySelectorAll('.rating-filter').forEach(checkbox => {
            checkbox.addEventListener('change', filterProducts);
        });

        // Availability filter
        document.querySelectorAll('.availability-filter').forEach(checkbox => {
            checkbox.addEventListener('change', filterProducts);
        });

        function filterProducts() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const selectedCategories = Array.from(document.querySelectorAll('.category-filter:checked')).map(cb => cb.value);
            const minPrice = parseFloat(document.getElementById('priceMin').value) || 0;
            const maxPrice = parseFloat(document.getElementById('priceMax').value) || Infinity;
            
            const allProducts = document.querySelectorAll('.product-card');
            let visibleCount = 0;
            
            allProducts.forEach(product => {
                const name = product.querySelector('.product-name').textContent.toLowerCase();
                const category = product.querySelector('.product-category').textContent.toLowerCase();
                const price = parseFloat(product.dataset.price);
                
                const matchesSearch = name.includes(query) || category.includes(query);
                const matchesCategory = selectedCategories.length === 0 || selectedCategories.some(cat => category.includes(cat.toLowerCase()));
                const matchesPrice = price >= minPrice && price <= maxPrice;
                
                if (matchesSearch && matchesCategory && matchesPrice) {
                    product.style.display = 'block';
                    visibleCount++;
                } else {
                    product.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                const emptyState = document.createElement('div');
                emptyState.style.cssText = 'grid-column: 1/-1; text-align: center; padding: 60px 20px; color: #999;';
                emptyState.innerHTML = '<p style="font-size: 1.2em; margin-bottom: 10px;">😕 No products found</p><p style="font-size: 0.95em;">Try adjusting your filters</p>';
                emptyState.id = 'emptyState';
                const grid = document.getElementById('productsGrid');
                const existingEmpty = grid.querySelector('#emptyState');
                if (existingEmpty) existingEmpty.remove();
                grid.appendChild(emptyState);
            } else {
                const emptyState = document.getElementById('emptyState');
                if (emptyState) emptyState.remove();
            }
        }

        function resetPriceFilter() {
            document.getElementById('priceMin').value = '0';
            document.getElementById('priceMax').value = '10000';
            filterProducts();
        }

        function sortProducts() {
            const sortBy = document.getElementById('sortBy').value;
            const grid = document.getElementById('productsGrid');
            const products = Array.from(document.querySelectorAll('.product-card'));
            
            products.sort((a, b) => {
                if (sortBy === 'price-low') {
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                } else if (sortBy === 'price-high') {
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                }
                return 0;
            });
            
            products.forEach(product => grid.appendChild(product));
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>


        /* ============================================
           HEADER & TOP NAV
           ============================================ */

        .top-nav {
            background: white;
            padding: 12px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            font-size: 0.9em;
        }

        .top-nav-left, .top-nav-right {
            display: flex;
            gap: 30px;
        }

        .top-nav a {
            text-decoration: none;
            color: #666;
            transition: color 0.3s;
        }

        .top-nav a:hover {
            color: #E60012;
        }

        .track-order-btn {
            padding: 6px 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.85em;
            transition: 0.3s;
        }

        .track-order-btn:hover {
            background: #45a049;
        }

        /* MAIN HEADER */
        header {
            background: white;
            padding: 15px 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.8em;
            font-weight: 700;
            color: #E60012;
        }

        .search-bar {
            flex: 1;
            max-width: 400px;
            margin: 0 40px;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        .search-bar input:focus {
            outline: none;
            border-color: #E60012;
        }

        .header-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #E60012;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .cart-icon {
            position: relative;
            cursor: pointer;
            font-size: 1.5em;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #E60012;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8em;
            font-weight: bold;
        }

        /* ============================================
           SIDEBAR + MAIN
           ============================================ */

        .dashboard-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 20px;
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 40px;
        }

        .sidebar {
            background: white;
            border-radius: 10px;
            padding: 20px;
            height: fit-content;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .sidebar h3 {
            color: #E60012;
            margin-bottom: 15px;
            font-size: 1.1em;
        }

        .sidebar-section {
            margin-bottom: 25px;
        }

        .sidebar-section label {
            display: block;
            margin-bottom: 10px;
            font-size: 0.9em;
            color: #666;
            font-weight: 600;
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #E60012;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            font-weight: normal;
            cursor: pointer;
        }

        .price-range {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .price-range input {
            width: 60px;
            padding: 6px;
            border: 1px solid #e0e0e0;
            border-radius: 3px;
        }

        /* ============================================
           MAIN CONTENT
           ============================================ */

        .main-content {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
        }

        .section-header h2 {
            font-size: 1.8em;
            color: #2c2c2c;
        }

        .sort-controls {
            display: flex;
            gap: 10px;
        }

        .sort-controls select {
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            background: white;
            cursor: pointer;
        }

        /* ============================================
           PRODUCTS GRID
           ============================================ */

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .product-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
            cursor: pointer;
        }

        .product-card:hover {
            box-shadow: 0 5px 20px rgba(230, 0, 18, 0.1);
            transform: translateY(-5px);
        }

        .product-image {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3em;
            position: relative;
            overflow: hidden;
        }

        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #E60012;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
        }

        .product-info {
            padding: 15px;
        }

        .product-category {
            font-size: 0.8em;
            color: #E60012;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .product-name {
            font-size: 1em;
            font-weight: 600;
            color: #2c2c2c;
            margin-bottom: 8px;
            line-height: 1.3;
            height: 2.6em;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-rating {
            display: flex;
            gap: 2px;
            margin-bottom: 10px;
            font-size: 0.9em;
        }

        .star {
            color: #FFB800;
        }

        .product-price {
            font-size: 1.3em;
            font-weight: 700;
            color: #E60012;
            margin-bottom: 10px;
        }

        .product-original-price {
            font-size: 0.85em;
            color: #999;
            text-decoration: line-through;
            margin-right: 10px;
        }

        .product-discount {
            font-size: 0.85em;
            color: #4CAF50;
            font-weight: 600;
        }

        .add-to-cart-btn {
            width: 100%;
            padding: 10px;
            background: #E60012;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }

        .add-to-cart-btn:hover {
            background: #cc000f;
        }

        /* ============================================
           FEATURED SECTION
           ============================================ */

        .featured-section {
            background: linear-gradient(135deg, #E60012 0%, #cc000f 100%);
            color: white;
            padding: 40px;
            border-radius: 10px;
            margin-bottom: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
        }

        .featured-content h3 {
            font-size: 2em;
            margin-bottom: 15px;
        }

        .featured-content p {
            font-size: 1.1em;
            margin-bottom: 20px;
            opacity: 0.95;
        }

        .featured-btn {
            display: inline-block;
            padding: 12px 30px;
            background: white;
            color: #E60012;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: 0.3s;
        }

        .featured-btn:hover {
            transform: scale(1.05);
        }

        /* ============================================
           RESPONSIVE
           ============================================ */

        @media (max-width: 1024px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }

        @media (max-width: 768px) {
            header {
                padding: 10px 20px;
            }

            .header-content {
                flex-wrap: wrap;
            }

            .search-bar {
                max-width: 100%;
                margin: 10px 0;
                order: 3;
                flex-basis: 100%;
            }

            .featured-section {
                grid-template-columns: 1fr;
            }

            .top-nav-left {
                flex-direction: column;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- TOP NAVIGATION -->
    <div class="top-nav">
        <div class="top-nav-left">
            <a href="#">📞 Hotline 24/7: 088-2458004S</a>
            <button class="track-order-btn">📍 Track Order</button>
        </div>
        <div class="top-nav-right">
            <a href="#">USD ▼</a>
            <a href="#">🇬🇧 ENG ▼</a>
        </div>
    </div>

    <!-- HEADER -->
    <header>
        <div class="header-content">
            <div class="logo">🔴 ADROX</div>
            
            <div class="search-bar">
                <input type="text" placeholder="Search products..." id="searchInput">
            </div>

            <div class="header-right">
                <div class="user-section">
                    <div class="user-avatar">J</div>
                    <div>
                        <small style="color: #999;">Hello,</small><br>
                        <strong><?php echo $user_name; ?></strong>
                    </div>
                </div>
                
                <div class="cart-icon" onclick="alert('Coming soon: Shopping cart feature')">
                    🛒
                    <div class="cart-badge"><?php echo $user_cart_items; ?></div>
                </div>

                <button onclick="alert('Logout')" style="padding: 8px 15px; background: #E60012; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">Logout</button>
            </div>
        </div>
    </header>

    <!-- FEATURED SECTION -->
    <div style="max-width: 1400px; margin: 30px auto; padding: 0 40px;">
        <div class="featured-section">
            <div class="featured-content">
                <h3>🌟 Premium Collection</h3>
                <p>Discover our curated selection of luxury products combining style and functionality.</p>
                <a href="#" class="featured-btn">Shop Now</a>
            </div>
            <div style="text-align: center; font-size: 4em;">🎁</div>
        </div>
    </div>

    <!-- DASHBOARD -->
    <div class="dashboard-container">
        <!-- SIDEBAR FILTERS -->
        <div class="sidebar">
            <h3>🔍 Filters</h3>

            <div class="sidebar-section">
                <label>Categories</label>
                <div class="checkbox-group">
                    <?php foreach ($categories as $cat): ?>
                        <label>
                            <input type="checkbox" value="<?php echo htmlspecialchars($cat['name']); ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="sidebar-section">
                <label>Price Range</label>
                <div class="price-range">
                    <input type="number" placeholder="Min" value="0">
                    <span>-</span>
                    <input type="number" placeholder="Max" value="10000">
                </div>
            </div>

            <div class="sidebar-section">
                <label>Ratings</label>
                <div class="checkbox-group">
                    <label><input type="checkbox"> ⭐⭐⭐⭐⭐ 5 Star</label>
                    <label><input type="checkbox"> ⭐⭐⭐⭐ 4+ Star</label>
                    <label><input type="checkbox"> ⭐⭐⭐ 3+ Star</label>
                </div>
            </div>

            <div class="sidebar-section">
                <label>Availability</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" checked> In Stock</label>
                    <label><input type="checkbox"> Upcoming</label>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="section-header">
                <h2>🛍️ Shop Products</h2>
                <div class="sort-controls">
                    <select>
                        <option>Sort by: Newest</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                        <option>Best Rating</option>
                    </select>
                </div>
            </div>

            <!-- PRODUCTS GRID -->
            <div class="products-grid">
                <?php 
                if (!empty($products)) {
                    foreach ($products as $product) {
                        $discount = rand(5, 20);
                        $original_price = $product['price'] + ($product['price'] * 0.2);
                        ?>
                        <div class="product-card">
                            <div class="product-image">
                                📦
                                <?php if ($product['is_featured']): ?>
                                    <div class="product-badge">⭐ Featured</div>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                                <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                <div class="product-rating">
                                    <span class="star">★</span>
                                    <span class="star">★</span>
                                    <span class="star">★</span>
                                    <span class="star">★</span>
                                    <span class="star">★</span>
                                </div>
                                <div>
                                    <span class="product-original-price">₹<?php echo number_format($original_price, 2); ?></span>
                                    <span class="product-discount">-<?php echo $discount; ?>%</span>
                                </div>
                                <div class="product-price">₹<?php echo number_format($product['price'], 2); ?></div>
                                <a href="<?php echo htmlspecialchars($product['affiliate_link']); ?>" target="_blank" class="add-to-cart-btn">🛒 Buy Now</a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>No products found</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const products = document.querySelectorAll('.product-card');
            
            products.forEach(product => {
                const name = product.querySelector('.product-name').textContent.toLowerCase();
                const category = product.querySelector('.product-category').textContent.toLowerCase();
                
                if (name.includes(query) || category.includes(query)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        });

        // Category filter
        document.querySelectorAll('.checkbox-group input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Filter logic can be added here
                console.log('Filter changed');
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>
