<?php
session_start();
include('db.php');

// Get session ID and user ID
$sessionId = session_id();
include('auth_middleware.php');
$userId = getUserId();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    
    try {
        switch ($action) {
            case 'update_quantity':
                $itemIndex = (int)($_POST['item_index'] ?? -1);
                $quantity = max(1, (int)($_POST['quantity'] ?? 1));
                
                // Get the item ID from database based on index
                $getItemsQuery = "SELECT id FROM shopping_bag WHERE session_id = ? ORDER BY created_at ASC";
                $getItemsStmt = $conn->prepare($getItemsQuery);
                
                if (!$getItemsStmt) {
                    throw new Exception("Error preparing get items query: " . $conn->error);
                }
                
                $getItemsStmt->bind_param("s", $sessionId);
                $getItemsStmt->execute();
                $itemsResult = $getItemsStmt->get_result();
                
                $itemIds = [];
                while ($row = $itemsResult->fetch_assoc()) {
                    $itemIds[] = $row['id'];
                }
                $getItemsStmt->close();
                
                if ($itemIndex >= 0 && isset($itemIds[$itemIndex])) {
                    $itemId = $itemIds[$itemIndex];
                    
                    $updateQuery = "UPDATE shopping_bag SET quantity = ?, updated_at = NOW() WHERE id = ?";
                    $updateStmt = $conn->prepare($updateQuery);
                    
                    if (!$updateStmt) {
                        throw new Exception("Error preparing update query: " . $conn->error);
                    }
                    
                    $updateStmt->bind_param("ii", $quantity, $itemId);
                    
                    if (!$updateStmt->execute()) {
                        throw new Exception("Error updating quantity: " . $updateStmt->error);
                    }
                    
                    $updateStmt->close();
                    
                    // Calculate new totals
                    $totalsQuery = "SELECT SUM(price * quantity) as subtotal FROM shopping_bag WHERE session_id = ?";
                    $totalsStmt = $conn->prepare($totalsQuery);
                    
                    if ($totalsStmt) {
                        $totalsStmt->bind_param("s", $sessionId);
                        $totalsStmt->execute();
                        $totalsResult = $totalsStmt->get_result();
                        $subtotal = (float)($totalsResult->fetch_assoc()['subtotal'] ?? 0);
                        $totalsStmt->close();
                    } else {
                        $subtotal = 0;
                    }
                    
                    $tax = $subtotal * 0.15;
                    $total = $subtotal + $tax;
                    
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Quantity updated',
                        'summary' => [
                            'subtotal' => $subtotal,
                            'tax' => $tax,
                            'total' => $total
                        ]
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Item not found']);
                }
                break;
                
            case 'remove_item':
                $itemIndex = (int)($_POST['item_index'] ?? -1);
                
                // Get the item ID from database based on index
                $getItemsQuery = "SELECT id FROM shopping_bag WHERE session_id = ? ORDER BY created_at ASC";
                $getItemsStmt = $conn->prepare($getItemsQuery);
                
                if (!$getItemsStmt) {
                    throw new Exception("Error preparing get items query: " . $conn->error);
                }
                
                $getItemsStmt->bind_param("s", $sessionId);
                $getItemsStmt->execute();
                $itemsResult = $getItemsStmt->get_result();
                
                $itemIds = [];
                while ($row = $itemsResult->fetch_assoc()) {
                    $itemIds[] = $row['id'];
                }
                $getItemsStmt->close();
                
                if ($itemIndex >= 0 && isset($itemIds[$itemIndex])) {
                    $itemId = $itemIds[$itemIndex];
                    
                    $deleteQuery = "DELETE FROM shopping_bag WHERE id = ?";
                    $deleteStmt = $conn->prepare($deleteQuery);
                    
                    if (!$deleteStmt) {
                        throw new Exception("Error preparing delete query: " . $conn->error);
                    }
                    
                    $deleteStmt->bind_param("i", $itemId);
                    
                    if (!$deleteStmt->execute()) {
                        throw new Exception("Error deleting item: " . $deleteStmt->error);
                    }
                    
                    $deleteStmt->close();
                    
                    // Calculate new totals
                    $totalsQuery = "SELECT SUM(price * quantity) as subtotal FROM shopping_bag WHERE session_id = ?";
                    $totalsStmt = $conn->prepare($totalsQuery);
                    
                    if ($totalsStmt) {
                        $totalsStmt->bind_param("s", $sessionId);
                        $totalsStmt->execute();
                        $totalsResult = $totalsStmt->get_result();
                        $subtotal = (float)($totalsResult->fetch_assoc()['subtotal'] ?? 0);
                        $totalsStmt->close();
                    } else {
                        $subtotal = 0;
                    }
                    
                    $tax = $subtotal * 0.15;
                    $total = $subtotal + $tax;
                    
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Item removed',
                        'summary' => [
                            'subtotal' => $subtotal,
                            'tax' => $tax,
                            'total' => $total
                        ],
                        'reload_required' => true
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Item not found']);
                }
                break;
                
            case 'get_cart':
                $cartQuery = "SELECT * FROM shopping_bag WHERE session_id = ? ORDER BY created_at ASC";
                $cartStmt = $conn->prepare($cartQuery);
                
                if (!$cartStmt) {
                    throw new Exception("Error preparing cart query: " . $conn->error);
                }
                
                $cartStmt->bind_param("s", $sessionId);
                $cartStmt->execute();
                $cartResult = $cartStmt->get_result();
                
                $items = [];
                $subtotal = 0;
                
                while ($row = $cartResult->fetch_assoc()) {
                    $items[] = [
                        'name' => $row['product_name'],
                        'image' => $row['image'],
                        'price' => (float)$row['price'],
                        'color' => $row['color'],
                        'size' => $row['size'],
                        'quantity' => (int)$row['quantity']
                    ];
                    $subtotal += (float)$row['price'] * (int)$row['quantity'];
                }
                
                $cartStmt->close();
                
                $tax = $subtotal * 0.15;
                $total = $subtotal + $tax;
                
                echo json_encode([
                    'success' => true,
                    'items' => $items,
                    'summary' => [
                        'subtotal' => $subtotal,
                        'tax' => $tax,
                        'total' => $total
                    ]
                ]);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                break;
        }
    } catch (Exception $e) {
        error_log("Shopping cart error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    } finally {
        if (isset($conn)) {
            $conn->close();
        }
    }
    exit;
}

// Load cart items from database for display
try {
    $cartQuery = "SELECT * FROM shopping_bag WHERE session_id = ? ORDER BY created_at ASC";
    $cartStmt = $conn->prepare($cartQuery);
    
    if (!$cartStmt) {
        throw new Exception("Error preparing cart query: " . $conn->error);
    }
    
    $cartStmt->bind_param("s", $sessionId);
    $cartStmt->execute();
    $cartResult = $cartStmt->get_result();
    
    $items = [];
    $subtotal = 0;
    
    while ($row = $cartResult->fetch_assoc()) {
        $items[] = [
            'name' => $row['product_name'],
            'image' => $row['image'],
            'price' => (float)$row['price'],
            'color' => $row['color'],
            'size' => $row['size'],
            'quantity' => (int)$row['quantity']
        ];
        $subtotal += (float)$row['price'] * (int)$row['quantity'];
    }
    
    $cartStmt->close();
    
} catch (Exception $e) {
    error_log("Error loading cart: " . $e->getMessage());
    $items = [];
    $subtotal = 0;
}

// Calculate totals for initial display
$tax = $subtotal * 0.15;
$total = $subtotal + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>GlowUP - Shopping Cart</title>
    <link rel="stylesheet" href="ShoppingBag.css">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
 <!--<header>
        <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
            <div class="container">
              
                <a class="navbar-brand" href="index.php" aria-label="GlowUp Home">
                    GlowUp
                </a>

               
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarContent" aria-controls="navbarContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

               
                <div class="collapse navbar-collapse" id="navbarContent">
                    
                    <ul class="navbar-nav navbar-nav-left">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php" aria-label="Home">
                                <i class="fas fa-home"></i>
                                <span>Home</span>
                            </a>
                        </li>
                    </ul>

                  
                    <ul class="navbar-nav navbar-nav-center">
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
                                <span class="wishlist-counter" id="wishlistCounter">0</span>
                            </a>
                        </li>
                        <li class="nav-item position-relative">
                            <a class="nav-link" href="ShoppingBag.php" aria-label="Shopping Bag">
                                <i class="fas fa-shopping-bag"></i>
                                <span>Shopping Bag</span>
                                <span class="shopping-bag-counter" id="bagCounter" style="display: none;">0</span>
                            </a>
                        </li>
                    </ul>

                  
                    <div class="navbar-nav navbar-nav-right">
                        
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" placeholder="Search products..." aria-label="Search">
                        </div>
                        
                      
                        <ul class="navbar-nav">
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
        </nav>
    </header> 
-->

       <style>
        :root {
            --primary-color: #b07154;
            --secondary-color: #6c757d;
            --text-dark: #2c2c2c;
            --text-light: #6c757d;
            --bg-white: #ffffff;
            --border-light: #e9ecef;
            --shadow-light: 0 2px 20px rgba(0,0,0,0.08);
            --shadow-hover: 0 4px 25px rgba(0,0,0,0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

      /*  .navbar-custom {
            background: var(--bg-white);
            box-shadow: var(--shadow-light);
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-light);
            transition: all 0.3s ease;
        }*/

        /* Logo Section */
        .navbar-brand {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            text-decoration: none;
            font-style: italic;
            letter-spacing: -0.5px;
            transition: transform 0.2s ease;
        }

        .navbar-brand:hover {
            color: var(--primary-color);
            transform: scale(1.05);
        }

        /* Navigation Links */
        .navbar-nav {
            display: flex;
            align-items: center;
            list-style: none;
            gap: 2rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.7rem 1.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            white-space: nowrap;
        }

        .nav-link:hover {
            color: var(--primary-color);
            background: rgba(176, 113, 84, 0.08);
            transform: translateY(-1px);
        }

        .nav-link i {
            font-size: 1.1rem;
            opacity: 0.8;
        }

        .nav-link:hover i {
            opacity: 1;
        }

        /* Special styling for wishlist */
        .wishlist-link {
            color: var(--primary-color) !important;
            font-weight: 600;
        }

        .wishlist-link:hover {
            background: rgba(176, 113, 84, 0.12);
        }

        /* Counter badges */
        .counter-badge {
            position: absolute;
            top: 0.3rem;
            right: 0.5rem;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Right Section */
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        /* Search Container */
        .search-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-input {
            width: 250px;
            padding: 0.7rem 1rem 0.7rem 2.5rem;
            border: 2px solid var(--border-light);
            border-radius: 25px;
            font-size: 0.9rem;
            background: #f8f9fa;
            transition: all 0.3s ease;
            outline: none;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(176, 113, 84, 0.1);
            width: 280px;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            color: var(--text-light);
            font-size: 1rem;
        }

        /* Mobile Toggle */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-dark);
            cursor: pointer;
            padding: 0.5rem;
        }

        /* Mobile Menu */
        .mobile-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--bg-white);
            box-shadow: var(--shadow-hover);
            border-radius: 0 0 15px 15px;
            padding: 2rem;
            z-index: 1000;
        }

        .mobile-menu.active {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mobile-nav {
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .mobile-nav .nav-link {
            width: 100%;
            justify-content: flex-start;
            padding: 1rem;
            border-radius: 10px;
        }

        .mobile-search {
            width: 100%;
        }

        .mobile-search .search-input {
            width: 100%;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .navbar-nav,
            .navbar-right {
                display: none;
            }

            .mobile-toggle {
                display: block;
            }

            .navbar-container {
                padding: 0 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.7rem;
            }

            .navbar-container {
                padding: 0 1rem;
            }

            .counter-badge {
                position: static;
                margin-left: auto;
            }
        }

        @media (max-width: 480px) {
            .navbar-brand {
                font-size: 1.5rem;
            }
        }

        /* Active page indicator */
        .nav-link.active {
            background: rgba(176, 113, 84, 0.12);
            color: var(--primary-color);
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -0.3rem;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 2px;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Demo functions for counter functionality
        let wishlistCount = 0;
        let bagCount = 0;

        function updateWishlist() {
            wishlistCount++;
            const counter = document.getElementById('wishlistCounter');
            counter.textContent = wishlistCount;
            counter.style.display = wishlistCount > 0 ? 'flex' : 'none';
        }

        function updateBag() {
            bagCount++;
            const counter = document.getElementById('bagCounter');
            counter.textContent = bagCount;
            counter.style.display = 'flex'; // Always show when there are items
        }

        // Initialize counters on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Show wishlist counter if there are items
            const wishlistCounter = document.getElementById('wishlistCounter');
            if (wishlistCount > 0) {
                wishlistCounter.style.display = 'flex';
            }
            
            // Show bag counter if there are items
            const bagCounter = document.getElementById('bagCounter');
            if (bagCount > 0) {
                bagCounter.style.display = 'flex';
            }
            
            // Add search functionality
            const searchInput = document.querySelector('.search-input');
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const searchTerm = this.value.trim();
                    if (searchTerm) {
                        // Redirect to search results page
                        window.location.href = `search.php?q=${encodeURIComponent(searchTerm)}`;
                    }
                }
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add active state to current page
        const currentPage = window.location.pathname.split('/').pop();
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            }
        });
    </script>

    
<div class="container">
  <!--  <div class="cart-header">
        <h1><i class="fas fa-shopping-bag"></i> Shopping Cart</h1>
        <p>Review your items and proceed to checkout</p>
    </div>-->

    <div class="cart-content">
        <div class="cart-items" id="cartItems">
            <?php if (empty($items)): ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your cart is empty</h3>
                    <p>Looks like you haven't added any items to your cart yet.</p>
                    <a href="Women.php" class="continue-shopping">Continue Shopping</a>
                </div>
            <?php else: ?>
                <?php foreach ($items as $index => $item): ?>
                    <div class="cart-item" data-id="<?= $index ?>">
                        <div class="item-image">
                            <?php if (isset($item['image']) && !empty($item['image'])): ?>
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name'] ?? 'Product') ?>">
                            <?php else: ?>
                                <i class="fas fa-tshirt"></i>
                            <?php endif; ?>
                        </div>
                        <div class="item-details">
                            <div class="item-name"><?= htmlspecialchars($item['name'] ?? 'Unknown Product') ?></div>
                            <div class="item-variants">
                                <?php if (isset($item['color']) && !empty($item['color'])): ?>Color: <?= htmlspecialchars($item['color']) ?><?php endif; ?>
                                <?php if (isset($item['size']) && !empty($item['size'])): ?><?= isset($item['color']) && !empty($item['color']) ? ' • ' : '' ?>Size: <?= htmlspecialchars($item['size']) ?><?php endif; ?>
                            </div>
                            <div class="item-price">SAR <?= number_format((float)($item['price'] ?? 0), 2) ?></div>
                        </div>
                        <div class="quantity-controls">
                            <button class="quantity-btn" onclick="updateQuantity(<?= $index ?>, <?= ((int)($item['quantity'] ?? 1)) - 1 ?>)" <?= ((int)($item['quantity'] ?? 1)) <= 1 ? 'disabled' : '' ?>>
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="quantity-display" id="quantity-<?= $index ?>"><?= (int)($item['quantity'] ?? 1) ?></span>
                            <button class="quantity-btn" onclick="updateQuantity(<?= $index ?>, <?= ((int)($item['quantity'] ?? 1)) + 1 ?>)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <button class="remove-btn" onclick="removeItem(<?= $index ?>)" title="Remove item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="cart-summary">
            <h3 class="summary-title">Order Summary</h3>
            <div class="summary-row">
                <span>Subtotal:</span>
                <span id="subtotal">SAR <?= number_format($subtotal, 2) ?></span>
            </div>
            <div class="summary-row">
                <span>Tax (15%):</span>
                <span id="tax">SAR <?= number_format($tax, 2) ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping:</span>
                <span id="shipping">Free</span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span id="total">SAR <?= number_format($total, 2) ?></span>
            </div>
            <button class="checkout-btn" id="checkoutBtn" onclick="proceedToCheckout()" <?= empty($items) ? 'disabled' : '' ?>>
                <i class="fas fa-lock"></i> Proceed to Checkout
            </button>
            <p style="text-align: center; margin-top: 15px; font-size: 0.9rem; color: var(--text-secondary);">
                <i class="fas fa-shield-alt"></i> Secure checkout guaranteed
            </p>
        </div>
    </div>
</div>

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


<script src="ShoppingBag.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>