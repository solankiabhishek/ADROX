<?php
header('Content-Type: application/json');

require_once 'path/to/your/config_file.php';
// Initialize database if needed
require_once 'db_init.php';

// Connect to database
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'products':
        $category = isset($_GET['category']) ? $_GET['category'] : null;
        
        if ($category) {
            $products = get_products_by_category($conn, $category);
        } else {
            $products = get_all_products($conn);
        }
        
        echo json_encode(['success' => true, 'products' => $products]);
        break;

    case 'featured':
        $products = get_featured_products($conn);
        echo json_encode(['success' => true, 'products' => $products]);
        break;

    case 'categories':
        $categories = get_all_categories($conn);
        echo json_encode(['success' => true, 'categories' => $categories]);
        break;

    case 'product':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $product = get_product($conn, $id);
        
        if ($product) {
            echo json_encode(['success' => true, 'product' => $product]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
        break;

    case 'contact':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $name = $input['name'] ?? '';
            $email = $input['email'] ?? '';
            $message = $input['message'] ?? '';
            
            if (empty($name) || empty($email) || empty($message)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
                break;
            }
            
            if (save_contact_message($conn, $name, $email, $message)) {
                echo json_encode(['success' => true, 'message' => 'Message saved successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error saving message']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

$conn->close();
?>

