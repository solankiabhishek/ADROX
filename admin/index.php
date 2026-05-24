<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

// Initialize database
require_once '../includes/db_init.php';

// Create connection
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();

// Check if user is logged in
$is_logged_in = isset($_SESSION['admin_logged_in']);
$error_message = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $sql = "SELECT id, password FROM admin_users WHERE username = '" . $conn->real_escape_string($username) . "'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // For demo purposes, password is stored as plain text. In production, use password_hash()
        if ($password === 'admin123' && $username === 'admin') {
            $_SESSION['admin_logged_in'] = true;
            $is_logged_in = true;
        } else {
            $login_error = "Invalid credentials";
        }
    } else {
        $login_error = "User not found";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Handle product operations
if ($is_logged_in) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
            
            // Product operations
            if ($action === 'add') {
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $price = $_POST['price'] ?? 0;
                $category = $_POST['category'] ?? '';
                $image_url = $_POST['image_url'] ?? '';
                $affiliate_link = $_POST['affiliate_link'] ?? '';
                $is_featured = isset($_POST['is_featured']) ? 1 : 0;
                
                // Validate required fields
                if (!empty($name) && !empty($description) && !empty($price) && !empty($category) && !empty($affiliate_link)) {
                    if (add_product($conn, $name, $description, $price, $category, $image_url, $affiliate_link, $is_featured)) {
                        header('Location: index.php?success=added');
                        exit;
                    } else {
                        $error_message = "Error adding product";
                    }
                } else {
                    $error_message = "Please fill in all required fields";
                }
            } elseif ($action === 'edit') {
                $id = $_POST['id'] ?? 0;
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $price = $_POST['price'] ?? 0;
                $category = $_POST['category'] ?? '';
                $image_url = $_POST['image_url'] ?? '';
                $affiliate_link = $_POST['affiliate_link'] ?? '';
                $is_featured = isset($_POST['is_featured']) ? 1 : 0;
                
                if (!empty($id) && !empty($name) && !empty($description) && !empty($price) && !empty($category) && !empty($affiliate_link)) {
                    if (update_product($conn, $id, $name, $description, $price, $category, $image_url, $affiliate_link, $is_featured)) {
                        header('Location: index.php?success=updated');
                        exit;
                    } else {
                        $error_message = "Error updating product";
                    }
                } else {
                    $error_message = "Please fill in all required fields";
                }
            } elseif ($action === 'delete') {
                $id = $_POST['id'] ?? 0;
                
                if (!empty($id) && delete_product($conn, $id)) {
                    header('Location: index.php?success=deleted');
                    exit;
                } else {
                    $error_message = "Error deleting product";
                }
            }
            // Category operations
            elseif ($action === 'add_category') {
                $cat_name = $_POST['cat_name'] ?? '';
                $cat_description = $_POST['cat_description'] ?? '';
                
                if (!empty($cat_name)) {
                    if (add_category($conn, $cat_name, $cat_description)) {
                        header('Location: index.php?tab=categories&success=cat_added');
                        exit;
                    } else {
                        $error_message = "Error adding category";
                    }
                } else {
                    $error_message = "Category name is required";
                }
            } elseif ($action === 'edit_category') {
                $cat_id = $_POST['cat_id'] ?? 0;
                $cat_name = $_POST['cat_name'] ?? '';
                $cat_description = $_POST['cat_description'] ?? '';
                
                if (!empty($cat_id) && !empty($cat_name)) {
                    if (update_category($conn, $cat_id, $cat_name, $cat_description)) {
                        header('Location: index.php?tab=categories&success=cat_updated');
                        exit;
                    } else {
                        $error_message = "Error updating category";
                    }
                } else {
                    $error_message = "Category name is required";
                }
            } elseif ($action === 'delete_category') {
                $cat_id = $_POST['cat_id'] ?? 0;
                
                if (!empty($cat_id) && delete_category($conn, $cat_id)) {
                    header('Location: index.php?tab=categories&success=cat_deleted');
                    exit;
                } else {
                    $error_message = "Error deleting category";
                }
            }
        }
    }
}

// Fetch products and categories
$products = $is_logged_in ? get_all_products($conn) : [];
$categories = get_all_categories($conn);
$category_stats = $is_logged_in ? get_category_stats($conn) : [];
$dashboard_stats = $is_logged_in ? get_dashboard_stats($conn) : [];

// Handle success messages from redirect
$success_message = '';
if (isset($_GET['success'])) {
    $success_type = $_GET['success'];
    if ($success_type === 'added') {
        $success_message = "✅ Product added successfully!";
    } elseif ($success_type === 'updated') {
        $success_message = "✅ Product updated successfully!";
    } elseif ($success_type === 'deleted') {
        $success_message = "✅ Product deleted successfully!";
    } elseif ($success_type === 'cat_added') {
        $success_message = "✅ Category added successfully!";
    } elseif ($success_type === 'cat_updated') {
        $success_message = "✅ Category updated successfully!";
    } elseif ($success_type === 'cat_deleted') {
        $success_message = "✅ Category deleted successfully!";
    }
}

// Get current tab
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADROX Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            color: #2c2c2c;
            display: flex;
            height: 100vh;
        }

        /* ============================================
           SIDEBAR NAVIGATION
           ============================================ */

        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1a1a1a 0%, #2c2c2c 100%);
            color: white;
            padding: 30px 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
        }

        .sidebar-logo {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #E60012;
        }

        .sidebar-logo h2 {
            color: #E60012;
            font-size: 1.8em;
            margin-bottom: 5px;
        }

        .sidebar-logo p {
            color: #d4a574;
            font-size: 0.85em;
            font-weight: 300;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 10px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: #ccc;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover {
            background: rgba(230, 0, 18, 0.1);
            color: #E60012;
            transform: translateX(5px);
        }

        .sidebar-menu a.active {
            background: #E60012;
            color: white;
        }

        .sidebar-user {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            padding-top: 20px;
            border-top: 1px solid #444;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #E60012;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        .user-details small {
            display: block;
            color: #999;
            font-size: 0.8em;
        }

        .logout-btn {
            width: 100%;
            padding: 10px;
            background: #E60012;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
            font-size: 0.9em;
        }

        .logout-btn:hover {
            background: #cc000f;
        }

        /* ============================================
           MAIN CONTENT
           ============================================ */

        .main-content {
            margin-left: 260px;
            flex: 1;
            overflow-y: auto;
            padding: 30px;
        }

        .admin-container {
            max-width: 1200px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.5em;
            color: #E60012;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ============================================
           LOGIN FORM
           ============================================ */

        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: linear-gradient(135deg, #1a1a1a 0%, #2c2c2c 100%);
        }

        .login-form {
            max-width: 400px;
            width: 90%;
            background: white;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(230, 0, 18, 0.15);
            border-top: 4px solid #E60012;
        }

        .login-form h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #E60012;
            font-size: 2em;
        }

        .login-form .subtitle {
            text-align: center;
            color: #d4a574;
            margin-bottom: 30px;
            font-size: 0.9em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c2c2c;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-family: inherit;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #E60012;
            box-shadow: 0 0 0 3px rgba(230, 0, 18, 0.1);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            accent-color: #E60012;
        }

        .btn {
            padding: 12px 25px;
            background: #E60012;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 1em;
        }

        .btn:hover {
            background: #cc000f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 0, 18, 0.3);
        }

        .btn-secondary {
            background: #666;
        }

        .btn-secondary:hover {
            background: #555;
        }

        .btn-danger {
            background: #f44336;
        }

        .btn-danger:hover {
            background: #da190b;
        }

        .message {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            overflow: hidden;
        }

        .products-table thead {
            background: linear-gradient(90deg, #E60012 0%, #cc000f 100%);
            color: white;
        }

        .products-table th,
        .products-table td {
            padding: 15px;
            text-align: left;
        }

        .products-table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9em;
        }

        .products-table tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.3s;
        }

        .products-table tbody tr:hover {
            background: #f9f9f9;
        }

        .products-table tbody tr:nth-child(even) {
            background: #f5f5f5;
        }

        .featured-badge {
            display: inline-block;
            background: #E60012;
            color: white;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 0.8em;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-buttons button {
            padding: 8px 15px;
            font-size: 0.9em;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border-top: 4px solid #E60012;
        }

        .modal-header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            color: #E60012;
            font-size: 1.8em;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 2em;
            cursor: pointer;
            color: #999;
            transition: color 0.3s;
        }

        .close-btn:hover {
            color: #E60012;
        }

        .add-product-btn {
            margin-bottom: 30px;
            display: inline-block;
        }

        .add-product-btn::before {
            content: '+ ';
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: all 0.3s;
            border-left: 4px solid #E60012;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(230, 0, 18, 0.15);
        }

        .stat-card h3 {
            color: #2c2c2c;
            font-size: 0.95em;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .stat-number {
            font-size: 2.5em;
            color: #E60012;
            font-weight: bold;
            margin: 0;
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 20px;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .sidebar-user {
                position: static;
                margin-top: 20px;
                padding-top: 20px;
            }

            .products-table {
                font-size: 0.9em;
            }

            .products-table th,
            .products-table td {
                padding: 10px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .header {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <?php if (!$is_logged_in): ?>
        <!-- LOGIN PAGE -->
        <div class="login-container">
            <div class="login-form">
                <h2>ADROX</h2>
                <p class="subtitle">Admin Dashboard</p>
                
                <?php if (isset($login_error)): ?>
                    <div class="message error"><?php echo $login_error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="username">👤 Username</label>
                        <input type="text" id="username" name="username" placeholder="admin" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">🔐 Password</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>
                    
                    <input type="hidden" name="action" value="login">
                    <button type="submit" class="btn" style="width: 100%; margin-bottom: 15px;">Login</button>
                </form>
                
                <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; text-align: center; font-size: 0.85em; color: #666;">
                    <strong>Demo Credentials:</strong><br>
                    Username: <code>admin</code><br>
                    Password: <code>admin123</code>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- SIDEBAR NAVIGATION -->
        <div class="sidebar">
            <div class="sidebar-logo">
                <h2>ADROX</h2>
                <p>Luxury Meets Tech</p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="index.php?tab=dashboard" class="menu-link <?php echo ($current_tab === 'dashboard') ? 'active' : ''; ?>">📊 Dashboard</a></li>
                <li><a href="index.php?tab=products" class="menu-link <?php echo ($current_tab === 'products') ? 'active' : ''; ?>">📦 Products</a></li>
                <li><a href="index.php?tab=categories" class="menu-link <?php echo ($current_tab === 'categories') ? 'active' : ''; ?>">🏷️ Categories</a></li>
                <li><a href="index.php?tab=analytics" class="menu-link <?php echo ($current_tab === 'analytics') ? 'active' : ''; ?>">📈 Analytics</a></li>
                <li><a href="index.php?tab=settings" class="menu-link <?php echo ($current_tab === 'settings') ? 'active' : ''; ?>">⚙️ Settings</a></li>
            </ul>

            <div class="sidebar-user">
                <div class="user-info">
                    <div class="user-avatar">A</div>
                    <div>
                        <strong>Admin</strong>
                        <small>Administrator</small>
                    </div>
                </div>
                <a href="?logout=true" class="logout-btn">🚪 Logout</a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="admin-container">
                <div class="header">
                    <h1><?php 
                        switch($current_tab) {
                            case 'dashboard': echo '📊 Dashboard'; break;
                            case 'products': echo '📦 Products'; break;
                            case 'categories': echo '🏷️ Categories'; break;
                            case 'analytics': echo '📈 Analytics'; break;
                            case 'settings': echo '⚙️ Settings'; break;
                            default: echo '📊 Dashboard';
                        }
                    ?></h1>
                </div>

                <?php if (!empty($success_message)): ?>
                    <div class="message success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                <?php if (!empty($error_message)): ?>
                    <div class="message error">❌ <?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- DASHBOARD TAB -->
                <?php if ($current_tab === 'dashboard'): ?>
                    <div class="dashboard-stats">
                        <div class="stat-card">
                            <h3>📦 Total Products</h3>
                            <p class="stat-number"><?php echo $dashboard_stats['total_products']; ?></p>
                        </div>
                        <div class="stat-card">
                            <h3>⭐ Featured Products</h3>
                            <p class="stat-number"><?php echo $dashboard_stats['featured_products']; ?></p>
                        </div>
                        <div class="stat-card">
                            <h3>🏷️ Total Categories</h3>
                            <p class="stat-number"><?php echo $dashboard_stats['total_categories']; ?></p>
                        </div>
                        <div class="stat-card">
                            <h3>💬 Messages</h3>
                            <p class="stat-number"><?php echo $dashboard_stats['contact_messages']; ?></p>
                        </div>
                        <div class="stat-card">
                            <h3>💰 Avg Price</h3>
                            <p class="stat-number">₹<?php echo number_format($dashboard_stats['avg_price'], 2); ?></p>
                        </div>
                    </div>

                    <div style="margin-top: 40px;">
                        <h2 style="margin-bottom: 20px; color: #2c2c2c;">Recent Products</h2>
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 40px;">
                                            <strong>No products yet</strong>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach (array_slice($products, 0, 5) as $product): ?>
                                        <tr>
                                            <td><strong>#<?php echo $product['id']; ?></strong></td>
                                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                                            <td><strong>₹<?php echo number_format($product['price'], 2); ?></strong></td>
                                            <td>
                                                <?php if ($product['is_featured']): ?>
                                                    <span class="featured-badge">⭐ Featured</span>
                                                <?php else: ?>
                                                    <small style="color: #999;">Regular</small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- PRODUCTS TAB -->
                <?php if ($current_tab === 'products'): ?>
                    <button class="btn add-product-btn" onclick="openAddModal()">Add New Product</button>

                    <div id="products">
                        <h2 style="margin-bottom: 20px; color: #2c2c2c;">📦 All Products (<?php echo count($products); ?>)</h2>
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 40px;">
                                            <strong>No products found</strong><br>
                                            <small style="color: #999;">Click "Add New Product" to get started</small>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><strong>#<?php echo $product['id']; ?></strong></td>
                                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                                            <td><strong>₹<?php echo number_format($product['price'], 2); ?></strong></td>
                                            <td>
                                                <?php if ($product['is_featured']): ?>
                                                    <span class="featured-badge">⭐ Featured</span>
                                                <?php else: ?>
                                                    <small style="color: #999;">Regular</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn btn-secondary" type="button" data-product='<?php echo json_encode($product); ?>' onclick="openEditModalWithData(this)">✏️ Edit</button>
                                                    <form style="display: inline;" method="POST">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">🗑️ Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- CATEGORIES TAB -->
                <?php if ($current_tab === 'categories'): ?>
                    <button class="btn add-product-btn" onclick="openCategoryModal()">Add New Category</button>

                    <div id="categories">
                        <h2 style="margin-bottom: 20px; color: #2c2c2c;">🏷️ All Categories (<?php echo count($categories); ?>)</h2>
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category Name</th>
                                    <th>Products</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 40px;">
                                            <strong>No categories found</strong>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <tr>
                                            <td><strong>#<?php echo $cat['id']; ?></strong></td>
                                            <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                            <td>
                                                <strong><?php 
                                                    $count = 0;
                                                    foreach ($category_stats as $stat) {
                                                        if ($stat['name'] === $cat['name']) {
                                                            $count = $stat['product_count'];
                                                            break;
                                                        }
                                                    }
                                                    echo $count;
                                                ?></strong>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn btn-secondary" type="button" data-category='<?php echo json_encode($cat); ?>' onclick="openEditCategoryModal(this)">✏️ Edit</button>
                                                    <form style="display: inline;" method="POST">
                                                        <input type="hidden" name="action" value="delete_category">
                                                        <input type="hidden" name="cat_id" value="<?php echo $cat['id']; ?>">
                                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">🗑️ Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- ANALYTICS TAB -->
                <?php if ($current_tab === 'analytics'): ?>
                    <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
                        <h2 style="margin-bottom: 20px; color: #2c2c2c;">📊 Analytics Overview</h2>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
                            <div class="stat-card">
                                <h3>📦 Total Products</h3>
                                <p class="stat-number"><?php echo $dashboard_stats['total_products']; ?></p>
                            </div>
                            <div class="stat-card">
                                <h3>⭐ Featured Products</h3>
                                <p class="stat-number"><?php echo $dashboard_stats['featured_products']; ?></p>
                            </div>
                            <div class="stat-card">
                                <h3>🏷️ Total Categories</h3>
                                <p class="stat-number"><?php echo $dashboard_stats['total_categories']; ?></p>
                            </div>
                        </div>

                        <h3 style="margin-top: 30px; margin-bottom: 15px;">Products by Category</h3>
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Product Count</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($category_stats)): ?>
                                    <tr>
                                        <td colspan="3" style="text-align: center; padding: 30px;">No data available</td>
                                    </tr>
                                <?php else: ?>
                                    <?php 
                                        $total = array_sum(array_column($category_stats, 'product_count'));
                                        foreach ($category_stats as $stat): 
                                            $percentage = $total > 0 ? round(($stat['product_count'] / $total) * 100, 1) : 0;
                                    ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($stat['name']); ?></strong></td>
                                            <td><?php echo $stat['product_count']; ?></td>
                                            <td>
                                                <div style="background: #e0e0e0; border-radius: 5px; height: 20px; overflow: hidden;">
                                                    <div style="background: #E60012; height: 100%; width: <?php echo $percentage; ?>%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.8em;">
                                                        <?php echo $percentage; ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <h3 style="margin-top: 30px; margin-bottom: 15px;">Price Range Statistics</h3>
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Average Price</strong></td>
                                    <td>₹<?php echo number_format($dashboard_stats['avg_price'], 2); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Total Products</strong></td>
                                    <td><?php echo $dashboard_stats['total_products']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Featured Percentage</strong></td>
                                    <td><?php echo $dashboard_stats['total_products'] > 0 ? round(($dashboard_stats['featured_products'] / $dashboard_stats['total_products']) * 100, 1) : 0; ?>%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- SETTINGS TAB -->
                <?php if ($current_tab === 'settings'): ?>
                    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); max-width: 600px;">
                        <h2 style="margin-bottom: 20px; color: #2c2c2c;">⚙️ Admin Settings</h2>
                        
                        <div class="form-group">
                            <h3 style="color: #E60012; margin-top: 20px; margin-bottom: 10px;">Database Information</h3>
                            <p><strong>Database Name:</strong> <?php echo DB_NAME; ?></p>
                            <p><strong>Database Host:</strong> <?php echo DB_SERVER; ?></p>
                            <p><strong>Total Products:</strong> <?php echo $dashboard_stats['total_products']; ?></p>
                            <p><strong>Total Categories:</strong> <?php echo $dashboard_stats['total_categories']; ?></p>
                        </div>

                        <div class="form-group">
                            <h3 style="color: #E60012; margin-top: 20px; margin-bottom: 10px;">System Information</h3>
                            <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                            <p><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
                            <p><strong>Current Date & Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                        </div>

                        <div class="form-group">
                            <h3 style="color: #E60012; margin-top: 20px; margin-bottom: 10px;">Admin Account</h3>
                            <p><strong>Username:</strong> admin</p>
                            <p><strong>Status:</strong> <span style="color: green; font-weight: bold;">✓ Active</span></p>
                            <button class="btn btn-secondary" style="margin-top: 10px;" onclick="alert('Password change feature coming soon!')">🔐 Change Password</button>
                        </div>

                        <div class="form-group">
                            <h3 style="color: #E60012; margin-top: 20px; margin-bottom: 10px;">Export Data</h3>
                            <button class="btn btn-secondary" onclick="alert('Export feature coming soon!')">📥 Export Products</button>
                            <button class="btn btn-secondary" onclick="alert('Export feature coming soon!')">📥 Export Categories</button>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    <?php endif; ?>

    <!-- ADD/EDIT PRODUCT MODAL -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">✏️ Add New Product</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            
            <form method="POST">
                <input type="hidden" id="editId" name="id">
                <input type="hidden" name="action" id="formAction" value="add">

                <div class="form-group">
                    <label for="productName">📝 Product Name</label>
                    <input type="text" id="productName" name="name" placeholder="E.g., Premium Mobile Cover" required>
                </div>

                <div class="form-group">
                    <label for="productDescription">📄 Description</label>
                    <textarea id="productDescription" name="description" rows="4" placeholder="Detailed product description..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="productPrice">💰 Price (₹)</label>
                    <input type="number" id="productPrice" name="price" step="0.01" placeholder="299.00" required>
                </div>

                <div class="form-group">
                    <label for="productCategory">🏷️ Category</label>
                    <select id="productCategory" name="category" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['name']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="productImage">🖼️ Image URL</label>
                    <input type="url" id="productImage" name="image_url" placeholder="https://example.com/image.jpg">
                </div>

                <div class="form-group">
                    <label for="productLink">🔗 Affiliate Link</label>
                    <input type="url" id="productLink" name="affiliate_link" required placeholder="https://amazon.in/...">
                    <small style="color: #999;">This is where users will be redirected when they click "Buy"</small>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="productFeatured" name="is_featured">
                        <label for="productFeatured" style="margin: 0;">⭐ Mark as Featured (shows on homepage)</label>
                    </div>
                </div>

                <button type="submit" class="btn" style="width: 100%; margin-top: 20px;">💾 Save Product</button>
            </form>
        </div>
    </div>

    <!-- ADD/EDIT CATEGORY MODAL -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="categoryModalTitle">✏️ Add New Category</h2>
                <button class="close-btn" onclick="closeCategoryModal()">&times;</button>
            </div>
            
            <form method="POST" id="categoryForm">
                <input type="hidden" id="categoryId" name="cat_id">
                <input type="hidden" id="categoryFormAction" name="action" value="add_category">

                <div class="form-group">
                    <label for="categoryName">🏷️ Category Name</label>
                    <input type="text" id="categoryName" name="cat_name" placeholder="E.g., Mobile Covers" required>
                </div>

                <div class="form-group">
                    <label for="categoryDescription">📝 Description</label>
                    <textarea id="categoryDescription" name="cat_description" rows="3" placeholder="Category description..."></textarea>
                </div>

                <button type="submit" class="btn" style="width: 100%; margin-top: 20px;">💾 Save Category</button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('editId').value = '';
            document.getElementById('formAction').value = 'add';
            document.getElementById('modalTitle').textContent = '✏️ Add New Product';
            document.getElementById('productName').value = '';
            document.getElementById('productDescription').value = '';
            document.getElementById('productPrice').value = '';
            document.getElementById('productCategory').value = '';
            document.getElementById('productImage').value = '';
            document.getElementById('productLink').value = '';
            document.getElementById('productFeatured').checked = false;
            document.getElementById('productModal').classList.add('active');
        }

        function openEditModal(product) {
            document.getElementById('editId').value = product.id;
            document.getElementById('formAction').value = 'edit';
            document.getElementById('modalTitle').textContent = '✏️ Edit Product';
            document.getElementById('productName').value = product.name;
            document.getElementById('productDescription').value = product.description;
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productCategory').value = product.category;
            document.getElementById('productImage').value = product.image_url;
            document.getElementById('productLink').value = product.affiliate_link;
            document.getElementById('productFeatured').checked = product.is_featured == 1;
            document.getElementById('productModal').classList.add('active');
        }

        function openEditModalWithData(button) {
            const product = JSON.parse(button.dataset.product);
            openEditModal(product);
        }

        function closeModal() {
            document.getElementById('productModal').classList.remove('active');
        }

        window.onclick = function(event) {
            const modal = document.getElementById('productModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Category Modal Functions
        function openCategoryModal() {
            document.getElementById('categoryId').value = '';
            document.getElementById('categoryForm').action = '?tab=categories';
            document.getElementById('categoryForm').style.display = 'block';
            document.getElementById('categoryModalTitle').textContent = '✏️ Add New Category';
            document.getElementById('categoryName').value = '';
            document.getElementById('categoryDescription').value = '';
            document.getElementById('categoryFormAction').value = 'add_category';
            document.getElementById('categoryModal').classList.add('active');
        }

        function openEditCategoryModal(button) {
            const category = JSON.parse(button.dataset.category);
            document.getElementById('categoryId').value = category.id;
            document.getElementById('categoryName').value = category.name;
            document.getElementById('categoryDescription').value = category.description || '';
            document.getElementById('categoryFormAction').value = 'edit_category';
            document.getElementById('categoryModalTitle').textContent = '✏️ Edit Category';
            document.getElementById('categoryModal').classList.add('active');
        }

        function closeCategoryModal() {
            document.getElementById('categoryModal').classList.remove('active');
        }

        window.onclick = function(event) {
            const modal = document.getElementById('categoryModal');
            if (event.target == modal) {
                closeCategoryModal();
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
