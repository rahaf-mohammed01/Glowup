<?php
session_start();

// Include database connection
include 'db.php';

// Handle AJAX requests for wishlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' || (isset($_GET['ajax']) && $_GET['ajax'] === '1')) {
    header('Content-Type: application/json');

    // Initialize wishlist session array if not exists
    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }

    // Handle POST request to add/remove items
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        if (isset($input['action'])) {
            switch ($input['action']) {
                case 'toggle':
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
                                    'items' => $_SESSION['wishlist'],
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
                                'items' => $_SESSION['wishlist'],
                                'count' => count($_SESSION['wishlist'])
                            ]);
                        }
                    }
                    break;
            }
        }
    } else {
        // GET request - just return current wishlist
        echo json_encode([
            'status' => 'success',
            'items' => $_SESSION['wishlist'] ?? [],
            'count' => count($_SESSION['wishlist'] ?? [])
        ]);
    }
    exit;
}

// Fetch products from database
$products = [];
if ($conn) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = 'Women' AND stock > 0 ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}

// Helper function to check if product is in wishlist
function isInWishlist($productId) {
    if (!isset($_SESSION['wishlist'])) {
        return false;
    }
    foreach ($_SESSION['wishlist'] as $item) {
        if ((string)$item['id'] === (string)$productId) {
            return true;
        }
    }
    return false;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>GlowUp-Clothes Women</title>
    <link rel="stylesheet" type="text/css" href="Product.css">
    <script src="Product.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
 <!-- Header Section -->
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
                                <span class="wishlist-counter" id="wishlistCounter"><?php echo count($_SESSION['wishlist'] ?? []); ?></span>
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
        </nav>
    </header>

    <style>
        :root {
            --primary-color: #b07154;
            --secondary-color: #6c757d;
            --text-dark: #2c2c2c;
            --text-light: #6c757d;
            --bg-white: #ffffff;
            --border-light: #e9ecef;
            --shadow-light: 0 2px 20px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 4px 25px rgba(0, 0, 0, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-custom {
            background: var(--bg-white);
            box-shadow: var(--shadow-light);
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-light);
            transition: all 0.3s ease;
        }

        .navbar-custom.scrolled {
            padding: 0.7rem 0;
            box-shadow: var(--shadow-hover);
        }

        .navbar-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
        }

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
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
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

    <!-- Results Counter -->
    <div id="resultsCounter" class="results-counter"></div>

    <div class="product-cards-container">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="shoe-details">
                        <?php if (!empty($product['image'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x400?text=No+Image"
                                alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php endif; ?>

                        <span class="shoe-name"><?php echo htmlspecialchars($product['name']); ?></span>
                        <p><?php echo htmlspecialchars($product['product_description']); ?></p>
                    </div>

                    <div class="color-size-price">
                        <div class="color-option">
                            <span class="color">Color:</span>
                            <div class="circles">
                                <span class="circle beige active" id="beige"></span>
                                <span class="circle black" id="black"></span>
                                <span class="circle brown" id="brown"></span>
                            </div>
                        </div>

                        <div class="size-option">
                            <span class="size">Size:</span>
                            <div class="sizes">
                                <span class="size-option">XS</span>
                                <span class="size-option">S</span>
                                <span class="size-option">M</span>
                                <span class="size-option">L</span>
                                <span class="size-option">XL</span>
                                <span class="size-option">XXL</span>
                                <span class="size-option">XXXL</span>
                            </div>
                        </div>

                        <div class="price">
                            <span class="price_num">SAR <?php echo number_format($product['price'], 2); ?></span>
                        </div>
                    </div>

                    <div class="button">
                        <button class="add-to-bag"
                            data-product-id="<?php echo $product['id']; ?>"
                            data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                            data-product-price="<?php echo $product['price']; ?>">
                            Add to Bag
                        </button>
                    </div>

                    <button class="wishlist-heart <?php echo isInWishlist($product['id']) ? 'in-wishlist' : ''; ?>"
                        data-product-id="<?php echo $product['id']; ?>"
                        onclick="toggleWishlist(this)"
                        data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                        data-product-price="<?php echo $product['price']; ?>"
                        data-product-image="<?php echo htmlspecialchars($product['image']); ?>"
                        data-product-description="<?php echo htmlspecialchars($product['product_description']); ?>"
                        style="<?php echo isInWishlist($product['id']) ? 'color: #e74c3c;' : ''; ?>">
                        <i class="bx <?php echo isInWishlist($product['id']) ? 'bxs-heart' : 'bx-heart'; ?>"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 50px;">
                <h3>No products available</h3>
            </div>
        <?php endif; ?>
    </div>

    <!-- Chat Button -->
    <a href="chat.php" class="simple-chat-btn" target="_top" title="Chat with Fashion Assistant">
      <i class="fas fa-comments"></i>
      <span class="chat-badge">!</span>
    </a>

    <div class="c-footer" title="back to top">
        <button type="button" id="backtotop" onclick="scrollToTop()">&#8593;</button>
    </div>

    <!-- Pass wishlist data to JavaScript -->
    <script>
        // Initialize wishlist state from PHP
        window.wishlistItems = <?php echo json_encode($_SESSION['wishlist'] ?? []); ?>;
        window.wishlistCount = <?php echo count($_SESSION['wishlist'] ?? []); ?>;
        
        // Update wishlist counter on page load
        document.addEventListener('DOMContentLoaded', function() {
            const wishlistCounter = document.getElementById('wishlistCounter');
            if (wishlistCounter) {
                wishlistCounter.textContent = window.wishlistCount;
                wishlistCounter.style.display = window.wishlistCount > 0 ? 'inline' : 'none';
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>