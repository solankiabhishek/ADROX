<?php
/**
 * ADROX E-Commerce Platform
 * Configuration Summary & Status
 * 
 * This file provides a visual configuration status
 * Access at: http://localhost/adrox/status.php
 */

require_once 'includes/config.php';

$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
$db_status = $conn->connect_error ? 'Failed' : 'Connected';
$db_error = $conn->connect_error;

// Get product count
if (!$conn->connect_error) {
    $product_result = $conn->query("SELECT COUNT(*) as count FROM products");
    $product_count = $product_result->fetch_assoc()['count'];
    
    $category_result = $conn->query("SELECT COUNT(*) as count FROM categories");
    $category_count = $category_result->fetch_assoc()['count'];
    
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADROX - Configuration Status</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            color: #fff;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .header h1 {
            color: #E60012;
            font-size: 3em;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #d4a574;
            font-size: 1.2em;
        }
        
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .status-card {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid #E60012;
            border-radius: 10px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }
        
        .status-card h3 {
            color: #d4a574;
            margin-bottom: 15px;
            font-size: 1.1em;
        }
        
        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .status-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .status-label {
            color: #ccc;
        }
        
        .status-value {
            color: #E60012;
            font-weight: bold;
        }
        
        .status-value.success {
            color: #4CAF50;
        }
        
        .status-value.error {
            color: #f44336;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
        }
        
        .badge.success {
            background: #4CAF50;
            color: white;
        }
        
        .badge.error {
            background: #f44336;
            color: white;
        }
        
        .links-section {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid #d4a574;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .links-section h2 {
            color: #d4a574;
            margin-bottom: 20px;
        }
        
        .link-button {
            display: inline-block;
            padding: 12px 25px;
            background: #E60012;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 15px;
            margin-bottom: 15px;
            transition: 0.3s;
        }
        
        .link-button:hover {
            background: #cc000f;
            transform: translateY(-2px);
        }
        
        .features-section {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid #E60012;
            border-radius: 10px;
            padding: 30px;
        }
        
        .features-section h2 {
            color: #d4a574;
            margin-bottom: 20px;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .feature-item::before {
            content: '✓';
            color: #4CAF50;
            font-weight: bold;
            font-size: 1.5em;
        }
        
        .error-message {
            background: #f44336;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        footer {
            text-align: center;
            margin-top: 50px;
            color: #999;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <h1>ADROX</h1>
            <p>Luxury Meets Technology</p>
        </div>
        
        <!-- ERROR MESSAGES -->
        <?php if ($db_error): ?>
            <div class="error-message">
                ⚠️ Database Connection Error: <?php echo htmlspecialchars($db_error); ?>
            </div>
        <?php endif; ?>
        
        <!-- STATUS CARDS -->
        <div class="status-grid">
            <div class="status-card">
                <h3>System Status</h3>
                <div class="status-item">
                    <span class="status-label">PHP Version</span>
                    <span class="status-value"><?php echo phpversion(); ?></span>
                </div>
                <div class="status-item">
                    <span class="status-label">Server</span>
                    <span class="status-value">Apache</span>
                </div>
                <div class="status-item">
                    <span class="status-label">Operating System</span>
                    <span class="status-value"><?php echo php_uname('s'); ?></span>
                </div>
            </div>
            
            <div class="status-card">
                <h3>Database</h3>
                <div class="status-item">
                    <span class="status-label">Connection</span>
                    <span class="status-value <?php echo $db_status === 'Connected' ? 'success' : 'error'; ?>">
                        <span class="badge <?php echo $db_status === 'Connected' ? 'success' : 'error'; ?>">
                            <?php echo $db_status; ?>
                        </span>
                    </span>
                </div>
                <div class="status-item">
                    <span class="status-label">Database</span>
                    <span class="status-value"><?php echo DB_NAME; ?></span>
                </div>
                <div class="status-item">
                    <span class="status-label">Host</span>
                    <span class="status-value"><?php echo DB_SERVER; ?></span>
                </div>
            </div>
            
            <div class="status-card">
                <h3>Content</h3>
                <div class="status-item">
                    <span class="status-label">Products</span>
                    <span class="status-value"><?php echo isset($product_count) ? $product_count : 'N/A'; ?></span>
                </div>
                <div class="status-item">
                    <span class="status-label">Categories</span>
                    <span class="status-value"><?php echo isset($category_count) ? $category_count : 'N/A'; ?></span>
                </div>
                <div class="status-item">
                    <span class="status-label">Admin Access</span>
                    <span class="status-value success">✓ Active</span>
                </div>
            </div>
        </div>
        
        <!-- QUICK LINKS -->
        <div class="links-section">
            <h2>🔗 Quick Access Links</h2>
            <a href="/adrox/" class="link-button">🏠 Website</a>
            <a href="/adrox/admin/" class="link-button">🔐 Admin Panel</a>
            <a href="setup.php" class="link-button">⚙️ Setup</a>
            <a href="README.md" class="link-button">📖 Documentation</a>
        </div>
        
        <!-- FEATURES -->
        <div class="features-section">
            <h2>✨ Features Included</h2>
            <div class="features-grid">
                <div class="feature-item">3D Animations</div>
                <div class="feature-item">Full Database</div>
                <div class="feature-item">Admin Panel</div>
                <div class="feature-item">Affiliate Links</div>
                <div class="feature-item">Product Categories</div>
                <div class="feature-item">Contact Form</div>
                <div class="feature-item">Responsive Design</div>
                <div class="feature-item">Adrox Branding</div>
            </div>
        </div>
        
        <!-- CREDENTIALS -->
        <div class="links-section" style="margin-top: 30px;">
            <h2>🔐 Admin Credentials</h2>
            <p><strong>Username:</strong> admin</p>
            <p><strong>Password:</strong> admin123</p>
            <p style="margin-top: 15px; color: #aaa; font-size: 0.9em;">
                ⚠️ Change password immediately in production environment
            </p>
        </div>
        
        <!-- FOOTER -->
        <footer>
            <p>ADROX E-Commerce Platform | Built with ❤️ for Luxury Meets Technology</p>
            <p style="margin-top: 10px; font-size: 0.9em;">© 2026 ADROX. All Rights Reserved.</p>
        </footer>
    </div>
</body>
</html>
