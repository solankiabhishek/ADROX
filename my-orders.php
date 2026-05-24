<?php
// Order tracking & history page
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - ADROX</title>
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
        }

        header {
            background: white;
            padding: 20px 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8em;
            font-weight: 700;
            color: #E60012;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-title {
            font-size: 2em;
            margin-bottom: 30px;
            color: #2c2c2c;
        }

        .order-filters {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 20px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }

        .filter-btn.active {
            background: #E60012;
            color: white;
            border-color: #E60012;
        }

        .filter-btn:hover {
            border-color: #E60012;
        }

        /* ORDER CARDS */
        .order-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: 0.3s;
        }

        .order-card:hover {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .order-header {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .order-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .order-info label {
            font-size: 0.85em;
            color: #999;
            text-transform: uppercase;
            font-weight: 600;
        }

        .order-info span {
            font-size: 1.1em;
            font-weight: 600;
            color: #2c2c2c;
        }

        .order-status {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
        }

        .status-delivered {
            background: #d4edda;
            color: #155724;
        }

        .status-processing {
            background: #fff3cd;
            color: #856404;
        }

        .status-shipped {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .order-progress {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        .progress-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 10px;
            color: #999;
        }

        .progress-circle.active {
            background: #E60012;
            color: white;
        }

        .progress-circle.completed {
            background: #4CAF50;
            color: white;
        }

        .progress-line {
            height: 2px;
            flex: 1;
            background: #e0e0e0;
            margin-bottom: 30px;
        }

        .progress-line.active {
            background: #E60012;
        }

        .progress-label {
            font-size: 0.85em;
            text-align: center;
            color: #666;
        }

        .order-items {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-details {
            display: flex;
            gap: 15px;
            flex: 1;
        }

        .item-image {
            width: 60px;
            height: 60px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
        }

        .item-info h4 {
            margin-bottom: 5px;
        }

        .item-info small {
            color: #999;
        }

        .item-price {
            font-weight: 600;
            min-width: 100px;
            text-align: right;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .order-total {
            font-size: 1.2em;
            font-weight: 700;
            color: #E60012;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
            font-size: 0.9em;
        }

        .btn-primary {
            background: #E60012;
            color: white;
        }

        .btn-primary:hover {
            background: #cc000f;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #2c2c2c;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
        }

        .empty-state-icon {
            font-size: 4em;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #999;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .order-header {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .order-progress {
                flex-direction: column;
            }

            .progress-line {
                width: 2px;
                height: 30px;
                margin: 0;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">🔴 ADROX</div>
        <div>
            <button onclick="history.back()" style="padding: 10px 20px; border: none; background: #E60012; color: white; border-radius: 5px; cursor: pointer; font-weight: 600;">← Back to Shop</button>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">📦 My Orders</h1>

        <!-- FILTERS -->
        <div class="order-filters">
            <button class="filter-btn active">All Orders</button>
            <button class="filter-btn">Processing</button>
            <button class="filter-btn">Shipped</button>
            <button class="filter-btn">Delivered</button>
            <button class="filter-btn">Cancelled</button>
        </div>

        <!-- SAMPLE ORDERS -->
        
        <!-- ORDER 1 - Delivered -->
        <div class="order-card">
            <div class="order-header">
                <div class="order-info">
                    <label>Order ID</label>
                    <span>#10234</span>
                </div>
                <div class="order-info">
                    <label>Order Date</label>
                    <span>May 8, 2026</span>
                </div>
                <div class="order-info">
                    <label>Delivery Date</label>
                    <span>May 10, 2026</span>
                </div>
                <div class="order-status">
                    <span class="status-badge status-delivered">✓ Delivered</span>
                </div>
            </div>

            <div class="order-progress">
                <div class="progress-step">
                    <div class="progress-circle completed">✓</div>
                    <div class="progress-label">Order Placed</div>
                </div>
                <div class="progress-line active"></div>
                <div class="progress-step">
                    <div class="progress-circle completed">✓</div>
                    <div class="progress-label">Processing</div>
                </div>
                <div class="progress-line active"></div>
                <div class="progress-step">
                    <div class="progress-circle completed">✓</div>
                    <div class="progress-label">Shipped</div>
                </div>
                <div class="progress-line active"></div>
                <div class="progress-step">
                    <div class="progress-circle completed">✓</div>
                    <div class="progress-label">Delivered</div>
                </div>
            </div>

            <div class="order-items">
                <div class="order-item">
                    <div class="item-details">
                        <div class="item-image">📱</div>
                        <div class="item-info">
                            <h4>Premium Mobile Cover - Red</h4>
                            <small>Qty: 1 | Color: Red</small>
                        </div>
                    </div>
                    <div class="item-price">₹299</div>
                </div>
            </div>

            <div class="order-footer">
                <div>
                    <div style="font-size: 0.85em; color: #999;">Total Amount</div>
                    <div class="order-total">₹299</div>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary">👁️ View Details</button>
                    <button class="btn btn-secondary">⭐ Write Review</button>
                </div>
            </div>
        </div>

        <!-- ORDER 2 - Shipped -->
        <div class="order-card">
            <div class="order-header">
                <div class="order-info">
                    <label>Order ID</label>
                    <span>#10233</span>
                </div>
                <div class="order-info">
                    <label>Order Date</label>
                    <span>May 6, 2026</span>
                </div>
                <div class="order-info">
                    <label>Est. Delivery</label>
                    <span>May 11, 2026</span>
                </div>
                <div class="order-status">
                    <span class="status-badge status-shipped">📦 Shipped</span>
                </div>
            </div>

            <div class="order-progress">
                <div class="progress-step">
                    <div class="progress-circle completed">✓</div>
                    <div class="progress-label">Order Placed</div>
                </div>
                <div class="progress-line active"></div>
                <div class="progress-step">
                    <div class="progress-circle completed">✓</div>
                    <div class="progress-label">Processing</div>
                </div>
                <div class="progress-line active"></div>
                <div class="progress-step">
                    <div class="progress-circle active">📍</div>
                    <div class="progress-label">Shipped</div>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step">
                    <div class="progress-circle"></div>
                    <div class="progress-label">Delivered</div>
                </div>
            </div>

            <div class="order-items">
                <div class="order-item">
                    <div class="item-details">
                        <div class="item-image">🔑</div>
                        <div class="item-info">
                            <h4>Leather Car Key Cover</h4>
                            <small>Qty: 1 | Color: Black</small>
                        </div>
                    </div>
                    <div class="item-price">₹499</div>
                </div>
            </div>

            <div class="order-footer">
                <div>
                    <div style="font-size: 0.85em; color: #999;">Total Amount</div>
                    <div class="order-total">₹499</div>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary">📍 Track Package</button>
                    <button class="btn btn-secondary">❌ Cancel Order</button>
                </div>
            </div>
        </div>

        <!-- ORDER 3 - Processing -->
        <div class="order-card">
            <div class="order-header">
                <div class="order-info">
                    <label>Order ID</label>
                    <span>#10232</span>
                </div>
                <div class="order-info">
                    <label>Order Date</label>
                    <span>May 9, 2026</span>
                </div>
                <div class="order-info">
                    <label>Est. Delivery</label>
                    <span>May 13, 2026</span>
                </div>
                <div class="order-status">
                    <span class="status-badge status-processing">⏳ Processing</span>
                </div>
            </div>

            <div class="order-progress">
                <div class="progress-step">
                    <div class="progress-circle completed">✓</div>
                    <div class="progress-label">Order Placed</div>
                </div>
                <div class="progress-line active"></div>
                <div class="progress-step">
                    <div class="progress-circle active">⚙️</div>
                    <div class="progress-label">Processing</div>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step">
                    <div class="progress-circle"></div>
                    <div class="progress-label">Shipped</div>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step">
                    <div class="progress-circle"></div>
                    <div class="progress-label">Delivered</div>
                </div>
            </div>

            <div class="order-items">
                <div class="order-item">
                    <div class="item-details">
                        <div class="item-image">⚡</div>
                        <div class="item-info">
                            <h4>Wireless Charger</h4>
                            <small>Qty: 1 | Color: Black</small>
                        </div>
                    </div>
                    <div class="item-price">₹1,999</div>
                </div>
            </div>

            <div class="order-footer">
                <div>
                    <div style="font-size: 0.85em; color: #999;">Total Amount</div>
                    <div class="order-total">₹1,999</div>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary">👁️ View Details</button>
                    <button class="btn btn-secondary">❌ Cancel Order</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Filter buttons functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // Filter logic can be added here
            });
        });
    </script>
</body>
</html>
