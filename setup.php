<?php
// ============================================
// Adrox E-Commerce - Setup & Initialization
// ============================================

// This file initializes the database and creates necessary tables
// Access this file once via browser: http://localhost/adrox/setup.php

require_once 'includes/config.php';

// Create connection
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✓ Database created/verified</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating database: " . $conn->error . "</p>";
}

// Select database
$conn->select_db(DB_NAME);

// Create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS products (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        description LONGTEXT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        category VARCHAR(100) NOT NULL,
        image_url VARCHAR(500),
        affiliate_link VARCHAR(1000) NOT NULL,
        is_featured BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS categories (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL UNIQUE,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS admin_users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message LONGTEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($tables as $table) {
    if ($conn->query($table) === TRUE) {
        echo "<p style='color: green;'>✓ Table created/verified</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating table: " . $conn->error . "</p>";
    }
}

// Insert sample categories (if not exists)
$categories = ['Mobile Covers', 'Car Key Covers', 'Accessories', 'Tech Gadgets'];
foreach ($categories as $cat) {
    $cat_escaped = $conn->real_escape_string($cat);
    $check = $conn->query("SELECT id FROM categories WHERE name = '$cat_escaped'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO categories (name, description) VALUES ('$cat_escaped', 'Premium $cat_escaped collection')");
        echo "<p style='color: green;'>✓ Category '$cat' inserted</p>";
    }
}

// Insert admin user (if not exists)
$admin_check = $conn->query("SELECT id FROM admin_users WHERE username = 'admin'");
if ($admin_check->num_rows == 0) {
    $conn->query("INSERT INTO admin_users (username, password, email) VALUES ('admin', 'admin123', 'admin@adrox.com')");
    echo "<p style='color: green;'>✓ Admin user created (username: admin, password: admin123)</p>";
}

// Insert sample products
$sample_products = [
    ['Premium Red Mobile Cover', 'Luxury red mobile cover with advanced protection', 299.00, 'Mobile Covers', 'https://affiliate.link/mobile-cover-red', 1],
    ['Leather Car Key Cover', 'Premium leather car key cover with elegant design', 499.00, 'Car Key Covers', 'https://affiliate.link/car-key-cover', 1],
    ['Wireless Charger Pad', 'Fast wireless charging pad with LED indicator', 1999.00, 'Tech Gadgets', 'https://affiliate.link/wireless-charger', 1],
];

$product_check = $conn->query("SELECT COUNT(*) as count FROM products");
$result = $product_check->fetch_assoc();

if ($result['count'] == 0) {
    foreach ($sample_products as $product) {
        $name = $conn->real_escape_string($product[0]);
        $description = $conn->real_escape_string($product[1]);
        $price = $product[2];
        $category = $conn->real_escape_string($product[3]);
        $link = $conn->real_escape_string($product[4]);
        $featured = $product[5];
        
        $conn->query("INSERT INTO products (name, description, price, category, image_url, affiliate_link, is_featured) 
                    VALUES ('$name', '$description', $price, '$category', '', '$link', $featured)");
        echo "<p style='color: green;'>✓ Sample product inserted: $product[0]</p>";
    }
}

echo "<hr style='margin: 30px 0;'>";
echo "<h2>✅ Setup Complete!</h2>";
echo "<p>Your Adrox E-Commerce website is ready to use.</p>";
echo "<p><strong>Access the website:</strong> <a href='/adrox/'>Click here</a></p>";
echo "<p><strong>Admin Panel:</strong> <a href='/adrox/admin/'>http://localhost/adrox/admin/</a></p>";
echo "<p><strong>Admin Credentials:</strong> username: admin | password: admin123</p>";

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>ADROX Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h2 {
            color: #E60012;
        }
        a {
            color: #E60012;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        p {
            line-height: 1.6;
        }
    </style>
</head>
<body>
</body>
</html>
