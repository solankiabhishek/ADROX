<?php

require_once 'config.php';

// Fetch all products
function get_all_products($conn) {
    $sql = "SELECT * FROM products ORDER BY is_featured DESC, created_at DESC";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $products = [];
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }
    return [];
}

// Fetch products by category
function get_products_by_category($conn, $category) {
    $category = $conn->real_escape_string($category);
    $sql = "SELECT * FROM products WHERE category = '$category' ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $products = [];
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }
    return [];
}

// Fetch featured products
function get_featured_products($conn) {
    $sql = "SELECT * FROM products WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 6";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $products = [];
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }
    return [];
}

// Get single product
function get_product($conn, $id) {
    $id = (int)$id;
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Get all categories
function get_all_categories($conn) {
    $sql = "SELECT * FROM categories";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $categories = [];
        while($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        return $categories;
    }
    return [];
}

// Add product
function add_product($conn, $name, $description, $price, $category, $image_url, $affiliate_link, $is_featured = 0) {
    $name = $conn->real_escape_string($name);
    $description = $conn->real_escape_string($description);
    $price = (float)$price;
    $category = $conn->real_escape_string($category);
    $image_url = $conn->real_escape_string($image_url);
    $affiliate_link = $conn->real_escape_string($affiliate_link);
    $is_featured = (int)$is_featured;
    
    $sql = "INSERT INTO products (name, description, price, category, image_url, affiliate_link, is_featured) 
            VALUES ('$name', '$description', $price, '$category', '$image_url', '$affiliate_link', $is_featured)";
    
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Update product
function update_product($conn, $id, $name, $description, $price, $category, $image_url, $affiliate_link, $is_featured = 0) {
    $id = (int)$id;
    $name = $conn->real_escape_string($name);
    $description = $conn->real_escape_string($description);
    $price = (float)$price;
    $category = $conn->real_escape_string($category);
    $image_url = $conn->real_escape_string($image_url);
    $affiliate_link = $conn->real_escape_string($affiliate_link);
    $is_featured = (int)$is_featured;
    
    $sql = "UPDATE products SET name='$name', description='$description', price=$price, 
            category='$category', image_url='$image_url', affiliate_link='$affiliate_link', 
            is_featured=$is_featured WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Delete product
function delete_product($conn, $id) {
    $id = (int)$id;
    $sql = "DELETE FROM products WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Save contact message
function save_contact_message($conn, $name, $email, $message) {
    $name = $conn->real_escape_string($name);
    $email = $conn->real_escape_string($email);
    $message = $conn->real_escape_string($message);
    
    $sql = "INSERT INTO contact_messages (name, email, message) VALUES ('$name', '$email', '$message')";
    
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Add category
function add_category($conn, $name, $description = '') {
    $name = $conn->real_escape_string($name);
    $description = $conn->real_escape_string($description);
    
    $sql = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";
    
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Update category
function update_category($conn, $id, $name, $description = '') {
    $id = (int)$id;
    $name = $conn->real_escape_string($name);
    $description = $conn->real_escape_string($description);
    
    $sql = "UPDATE categories SET name='$name', description='$description' WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Delete category
function delete_category($conn, $id) {
    $id = (int)$id;
    $sql = "DELETE FROM categories WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Get category stats
function get_category_stats($conn) {
    $sql = "SELECT c.id, c.name, COUNT(p.id) as product_count FROM categories c 
            LEFT JOIN products p ON c.name = p.category GROUP BY c.id, c.name";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $stats = [];
        while($row = $result->fetch_assoc()) {
            $stats[] = $row;
        }
        return $stats;
    }
    return [];
}

// Get dashboard stats
function get_dashboard_stats($conn) {
    $stats = [];
    
    // Total products
    $result = $conn->query("SELECT COUNT(*) as count FROM products");
    $row = $result->fetch_assoc();
    $stats['total_products'] = $row['count'];
    
    // Featured products
    $result = $conn->query("SELECT COUNT(*) as count FROM products WHERE is_featured = 1");
    $row = $result->fetch_assoc();
    $stats['featured_products'] = $row['count'];
    
    // Total categories
    $result = $conn->query("SELECT COUNT(*) as count FROM categories");
    $row = $result->fetch_assoc();
    $stats['total_categories'] = $row['count'];
    
    // Contact messages
    $result = $conn->query("SELECT COUNT(*) as count FROM contact_messages");
    $row = $result->fetch_assoc();
    $stats['contact_messages'] = $row['count'];
    
    // Average product price
    $result = $conn->query("SELECT AVG(price) as avg_price FROM products");
    $row = $result->fetch_assoc();
    $stats['avg_price'] = round($row['avg_price'] ?? 0, 2);
    
    return $stats;
}

?>
