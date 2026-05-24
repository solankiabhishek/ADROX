<?php
// Database Configuration
define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'adrox_ecommerce');

// Create connection
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    // echo "Database created successfully";
} else {
    // echo "Error creating database: " . $conn->error;
}

// Select database
$conn->select_db(DB_NAME);

// Set charset
$conn->set_charset("utf8mb4");

// Color Palette based on Adrox Brand Logo
define('PRIMARY_RED', '#E60012');
define('DARK_GRAY', '#2c2c2c');
define('LIGHT_GRAY', '#f5f5f5');
define('ACCENT_GOLD', '#d4a574');
define('WHITE', '#ffffff');

// Affiliate Link Base URL (Change as needed)
define('AFFILIATE_BASE', 'https://');

?>



