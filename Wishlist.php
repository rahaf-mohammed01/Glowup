<?php
session_start();

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle AJAX requests for wishlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' || (isset($_GET['ajax']) && $_GET['ajax'] === '1')) {
    header('Content-Type: application/json');
    
    try {
        // Initialize wishlist session array if not exists
        if (!isset($_SESSION['wishlist'])) {
            $_SESSION['wishlist'] = [];
        }

        // Handle POST request to add/remove items
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Debug logging
            error_log("Wishlist POST request - Input: " . print_r($input, true));

            if (isset($input['action']) && $input['action'] === 'remove') {
                if (isset($input['productId'])) {
                    $productId = (string)$input['productId'];
                    $found = false;
                    
                    error_log("Attempting to remove product ID: " . $productId);
                    error_log("Current wishlist: " . print_r($_SESSION['wishlist'], true));
                    
                    // Find and remove the item
                    foreach ($_SESSION['wishlist'] as $key => $item) {
                        if ((string)$item['id'] === $productId) {
                            unset($_SESSION['wishlist'][$key]);
                            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
                            $found = true;
                            error_log("Item removed successfully");
                            break;
                        }
                    }
                    
                    if ($found) {
                        echo json_encode([
                            'status' => 'removed',
                            'message' => 'Item removed from wishlist',
                            'count' => count($_SESSION['wishlist'])
                        ]);
                    } else {
                        error_log("Item not found in wishlist");
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Item not found in wishlist'
                        ]);
                    }
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'No product ID provided'
                    ]);
                }
            } else if (isset($input['action']) && $input['action'] === 'toggle') {
                // Handle toggle action (add/remove)
                if (isset($input['product'])) {
                    $product = $input['product'];
                    
                    // Validate required fields
                    if (empty($product['id']) || empty($product['name'])) {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Missing required product information'
                        ]);
                        exit;
                    }
                    
                    $productId = (string)$product['id'];
                    $found = false;
                    
                    // Check if item exists and remove it
                    foreach ($_SESSION['wishlist'] as $key => $item) {
                        if ((string)$item['id'] === $productId) {
                            unset($_SESSION['wishlist'][$key]);
                            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
                            $found = true;
                            
                            echo json_encode([
                                'status' => 'removed',
                                'message' => 'Item removed from wishlist',
                                'count' => count($_SESSION['wishlist'])
                            ]);
                            break;
                        }
                    }

                    // If not found, add it
                    if (!$found) {
                        $newProduct = [
                            'id' => $productId,
                            'name' => $product['name'] ?? '',
                            'price' => $product['price'] ?? '0',
                            'image' => $product['image'] ?? '',
                            'description' => $product['description'] ?? '',
                            'color' => $product['color'] ?? 'beige',
                            'size' => $product['size'] ?? 'M'
                        ];
                        
                        $_SESSION['wishlist'][] = $newProduct;
                        
                        echo json_encode([
                            'status' => 'added',
                            'message' => 'Item added to wishlist',
                            'count' => count($_SESSION['wishlist'])
                        ]);
                    }
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'No product data provided'
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid action'
                ]);
            }
        } else {
            // GET request - just return current wishlist
            echo json_encode([
                'status' => 'success',
                'items' => $_SESSION['wishlist'] ?? [],
                'count' => count($_SESSION['wishlist'] ?? [])
            ]);
        }
    } catch (Exception $e) {
        error_log("Wishlist error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Server error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Get wishlist items for display
$wishlistItems = $_SESSION['wishlist'] ?? [];
?>

<!DOCTYPE html>
<html>

<head>
    <title>GlowUp-Clothes Wishlist</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href=" Wishlist.css">
</head>

<body>
 <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
            <div class="container">
                <a class="navbar-brand" href="GlowUP.php">
                    <h4><em>GlowUp</em></h4>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">




                        <!-- Left section: Home -->

                        <li class="nav-item">
                            <a class="nav-link" href="home.php" aria-label="Home">
                                <i class="fas fa-home"></i>
                                <span>Home</span>
                            </a>
                        </li>


                        <!-- Center section: Main navigation -->

                        <li class="nav-item">
                            <a class="nav-link" href="Women.php" aria-label="Women's Collection">
                                <i class="fas fa-female"></i>
                                <span>Women</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Men.php" aria-label="Men's Collection">
                                <i class="fas fa-male"></i>
                                <span>Men</span>
                            </a>
                        </li>
                        <li class="nav-item position-relative">
                            <a class="nav-link wishlist-link" href="Wishlist.php" aria-label="Wishlist">
                                <i class="bx bx-heart"></i>
                                <span>Wishlist</span>
                                <span class="wishlist-counter" id="wishlistCounter"> </span>
                            </a>
                        </li>
                        <li class="nav-item position-relative">
                            <a class="nav-link" href="ShoppingBag.php" aria-label="Shopping Bag">
                                <i class="fas fa-shopping-bag"></i>
                                <span>Shopping Bag</span>
                                <span class="shopping-bag-counter" id="bagCounter" style="display: none;"> </span>
                            </a>
                        </li>


                        <!-- Right section: Search and Account -->
                        <div class="navbar-nav navbar-nav-right">
                            <!-- Search bar -->
                            <div class="search-container">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="search-input" placeholder="Search products..." aria-label="Search">
                            </div>

                            <!-- Account -->

                            <li class="nav-item">
                                <a class="nav-link" href="Account.php" aria-label="My Account">
                                    <i class="fas fa-user"></i>
                                    <span>Account</span>
                                </a>
                            </li>
                        </ul>

                        </div>
                </div>
            </div>
            </div>
        </nav>
    </header>

    <div class="wishlist-header">
        <h1><i class='bx bx-heart'></i> My Wishlist</h1>
        <p>Your favorite items saved for later</p>
    </div>

    <!-- This container will be populated dynamically by JavaScript -->
    <div class="wishlist-container" style="display: none;">
        <!-- Products will be added here dynamically -->
    </div>

    <!-- Empty wishlist state -->
    <div class="empty-wishlist" id="emptyWishlist" style="display: block;">
        <i class='bx bx-heart-circle'></i>
        <h2>Your Wishlist is Empty</h2>
        <p>Save your favorite items to shop them later</p>
        <a href="Women.php" class="browse-products-btn">Browse Products</a>
    </div>

        <!-- Chat Button -->
<a href="chat.php" class="simple-chat-btn" target="_top" title="Chat with Fashion Assistant">
  <i class="fas fa-comments"></i>
  <span class="chat-badge">!</span>
</a>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h5>Company Info</h5>
                    <ul>
                        <li><a href="#">About GlowUp</a></li>
                        <li><a href="#">Fashion Blogger</a></li>
                        <li><a href="#">Features</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h5>Help</h5>
                    <ul>
                        <li><a href="#">Shipping Info</a></li>
                        <li><a href="#">Returns</a></li>
                        <li><a href="#">Refund</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h5>Customer Care</h5>
                    <ul>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Payment Method</a></li>
                        <li><a href="#">Bonus Points</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="social-icons">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-whatsapp"></i></a>
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
            </div>

            <div class="payment-methods">
                <img src="https://cdn.msaaq.com/assets/images/payments/applepay.svg" alt="Apple Pay">
                <img src="https://cdn.msaaq.com/assets/images/payments/visa.svg" alt="Visa">
                <img src="https://cdn.msaaq.com/assets/images/payments/master.svg" alt="Mastercard">
                <img src="https://cdn.msaaq.com/assets/images/payments/mada.svg" alt="Mada">
            </div>

            <div class="footer-bottom">
                <p>&copy; 2023 GlowUp. All rights reserved.</p>
            </div>
        </div>
    </footer>


    <div class="c-footer" title="back to top">
        <button type="button" id="backtotop" onclick="scrollToTop()">&#8593;</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src=" Wishlist.js"></script>
</body>

</html>