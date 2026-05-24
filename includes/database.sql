-- Create Tables for Adrox E-Commerce

-- Products Table
CREATE TABLE IF NOT EXISTS products (
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
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL UNIQUE,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(100) NOT NULL,
  message LONGTEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Sample Categories (using INSERT IGNORE to avoid duplicates)
INSERT IGNORE INTO categories (name, description) VALUES
('Mobile Covers', 'Premium mobile phone covers and cases'),
('Car Key Covers', 'Stylish car key protectors and covers'),
('Accessories', 'Premium lifestyle accessories'),
('Tech Gadgets', 'Latest technology gadgets');

-- Insert Sample Products (using INSERT IGNORE to avoid duplicates)
INSERT IGNORE INTO products (name, description, price, category, affiliate_link, is_featured) VALUES
('Premium Mobile Cover - Red', 'Luxury red mobile cover with advanced protection', 299.00, 'Mobile Covers', 'https://affiliate.link/mobile-cover-red', 1),
('Leather Car Key Cover', 'Premium leather car key cover with elegant design', 499.00, 'Car Key Covers', 'https://affiliate.link/car-key-cover', 1),
('Wireless Charger', 'Fast wireless charging pad with LED indicator', 1999.00, 'Tech Gadgets', 'https://affiliate.link/wireless-charger', 1),
('Designer Car Key Chain', 'Luxury stainless steel car key holder', 599.00, 'Car Key Covers', 'https://affiliate.link/car-key-chain', 0),
('Glass Mobile Screen Protector', 'Tempered glass screen protector with anti-glare', 199.00, 'Mobile Covers', 'https://affiliate.link/screen-protector', 0);

-- Insert Admin User (Username: admin, Password: admin123) (using INSERT IGNORE to avoid duplicates)
INSERT IGNORE INTO admin_users (username, password, email) VALUES
('admin', 'admin123', 'admin@adrox.com');
