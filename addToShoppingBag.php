<?php
session_start();
include("db.php");

// Set JSON response header
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Get POST data
$name = $_POST['name'] ?? '';
$image = $_POST['image'] ?? '';
$price = $_POST['price'] ?? '';
$color = $_POST['color'] ?? '';
$size = $_POST['size'] ?? '';

// Validate required fields
if (empty($name) || empty($price) || empty($color) || empty($size)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required product information']);
    exit;
}

// Clean price (remove SAR and $ symbols, then convert to float)
$cleanPrice = (float)preg_replace('/[^\d.]/', '', $price);

// Validate that we got a valid price
if ($cleanPrice <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid price format']);
    exit;
}

// Get session ID and user ID
$sessionId = session_id();
include('auth_middleware.php');
$userId = getUserId();

try {
    // Check if item already exists in cart
    $checkQuery = "SELECT id, quantity FROM shopping_bag WHERE session_id = ? AND product_name = ? AND color = ? AND size = ?";
    $checkStmt = $conn->prepare($checkQuery);
    
    if (!$checkStmt) {
        throw new Exception("Error preparing check query: " . $conn->error);
    }
    
    $checkStmt->bind_param("ssss", $sessionId, $name, $color, $size);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        // Item exists, update quantity
        $row = $result->fetch_assoc();
        $newQuantity = $row['quantity'] + 1;
        
        $updateQuery = "UPDATE shopping_bag SET quantity = ?, updated_at = NOW() WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        
        if (!$updateStmt) {
            throw new Exception("Error preparing update query: " . $conn->error);
        }
        
        $updateStmt->bind_param("ii", $newQuantity, $row['id']);
        
        if (!$updateStmt->execute()) {
            throw new Exception("Error updating quantity: " . $updateStmt->error);
        }
        
        $message = "Item quantity updated in cart";
        $updateStmt->close();
    } else {
        // Item doesn't exist, insert new item
        $insertQuery = "INSERT INTO shopping_bag (session_id, user_id, product_name, image, price, color, size, quantity, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
        $insertStmt = $conn->prepare($insertQuery);
        
        if (!$insertStmt) {
            throw new Exception("Error preparing insert query: " . $conn->error);
        }
        
        // Fix the bind_param - use proper types
        $insertStmt->bind_param("sissdss", $sessionId, $userId, $name, $image, $cleanPrice, $color, $size);
        
        if (!$insertStmt->execute()) {
            throw new Exception("Error inserting item: " . $insertStmt->error);
        }
        
        $message = "Item added to cart successfully";
        $insertStmt->close();
    }
    
    $checkStmt->close();
    
    // Get cart count for response
    $countQuery = "SELECT SUM(quantity) as cart_count FROM shopping_bag WHERE session_id = ?";
    $countStmt = $conn->prepare($countQuery);
    
    if ($countStmt) {
        $countStmt->bind_param("s", $sessionId);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $cartCount = $countResult->fetch_assoc()['cart_count'] ?? 0;
        $countStmt->close();
    } else {
        $cartCount = 0;
    }
    
    // Return success response
    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'cart_count' => (int)$cartCount
    ]);
    
} catch (Exception $e) {
    // Log error for debugging
    error_log("Shopping cart error: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to add item to cart. Please try again.'
    ]);
} finally {
    // Close connection
    if (isset($conn)) {
        $conn->close();
    }
}
?>