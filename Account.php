<?php
// Start session with better configuration
if (session_status() == PHP_SESSION_NONE) {
    // Configure session settings for better reliability
    ini_set('session.cookie_lifetime', 3600); // 1 hour
    ini_set('session.gc_maxlifetime', 3600);
    ini_set('session.cookie_httponly', 1); // Security improvement
    ini_set('session.use_strict_mode', 1); // Security improvement
    session_start();
}

include('auth_middleware.php');

// Require user role to access this page
requireCustomer();

// Debug information (remove in production)
error_log("Account.php - Session ID: " . session_id());
error_log("Account.php - Session username: " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'NOT SET'));
error_log("Account.php - Session logged_in: " . (isset($_SESSION['logged_in']) ? ($_SESSION['logged_in'] ? 'true' : 'false') : 'NOT SET'));

// Enhanced session check - check both username and logged_in status
if (
    !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['username']) || empty($_SESSION['username'])
) {

    // Clear any corrupted session data
    session_unset();
    session_destroy();

    // Start fresh session for the redirect
    session_start();
    $_SESSION['redirect_message'] = 'Please log in to access your account.';

    error_log("Account.php - Redirecting to login - Session invalid");
    header('Location: login.php');
    exit();
}

// Include database connection
include('db.php');

// Verify database connection
if (!$conn) {
    error_log("Database connection failed in Account.php");
    die("Database connection error");
}

// Get user information from database using user_id for better security
$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'];

// ALWAYS fetch fresh user data from database to ensure we have the latest information
if ($user_id) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
} else {
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
}

if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    die("Database error");
}

$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If user not found in database, redirect to login
if (!$user) {
    error_log("User not found in database: " . $username);
    session_unset();
    session_destroy();

    session_start();
    $_SESSION['error_message'] = 'User account not found. Please log in again.';

    header('Location: login.php');
    exit();
}

// Update session variables with fresh database data to ensure consistency
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['phone'] = $user['phone'];
$_SESSION['date_of_birth'] = $user['date_of_birth'];
$_SESSION['gender'] = $user['gender'];
$_SESSION['street_address'] = $user['street_address'];
$_SESSION['city'] = $user['city'];
$_SESSION['postal_code'] = $user['postal_code'];
$_SESSION['country'] = $user['country'];

// Get user's recent orders
$recent_orders = [];
if ($user_id) {
    $order_query = "SELECT 
                        o.id,
                        o.order_id,
                        o.total_amount,
                        o.status,
                        o.order_date,
                        o.tracking_number,
                        COUNT(oi.id) as item_count
                    FROM orders o
                    LEFT JOIN order_items oi ON o.id = oi.order_id
                    WHERE o.user_id = ?
                    GROUP BY o.id
                    ORDER BY o.order_date DESC
                    LIMIT 5";

    $order_stmt = $conn->prepare($order_query);
    if ($order_stmt) {
        $order_stmt->bind_param("i", $user_id);
        $order_stmt->execute();
        $order_result = $order_stmt->get_result();
        while ($order = $order_result->fetch_assoc()) {
            $recent_orders[] = $order;
        }
        $order_stmt->close();
    }
}

// Helper function to format order status
function formatOrderStatus($status)
{
    $status_classes = [
        'Pending' => 'status-pending',
        'Processing' => 'status-processing',
        'Shipped' => 'status-shipped',
        'Delivered' => 'status-delivered',
        'Cancelled' => 'status-cancelled',
        'Refunded' => 'status-refunded'
    ];

    $class = $status_classes[$status] ?? 'status-pending';
    return '<span class="order-status ' . $class . '">' . ucfirst($status) . '</span>';
}

// Helper function to format currency
function formatCurrency($amount)
{
    return '$' . number_format($amount, 2);
}

// Set avatar source - check for profile picture
$avatarSrc = 'https://via.placeholder.com/120x120/C5AB96/ffffff?text=' . strtoupper(substr($user['username'], 0, 2));
if (!empty($user['profile_picture']) && file_exists($user['profile_picture'])) {
    $avatarSrc = $user['profile_picture'];
}

// Check for success/error messages from EditProfile.php
$success_message = '';
$error_message = '';
if (isset($_SESSION['profile_success'])) {
    $success_message = $_SESSION['profile_success'];
    unset($_SESSION['profile_success']);
}
if (isset($_SESSION['profile_error'])) {
    $error_message = $_SESSION['profile_error'];
    unset($_SESSION['profile_error']);
}

// Close database connection
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowUp - User Profile</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="Account.css">
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



    <!-- Main Profile Container -->
    <div class="profile-container">
        <!-- Show success/error messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Profile Header -->
        <div class="profile-header">
            <img src="<?php echo $avatarSrc; ?>"
                alt="Profile Picture" class="profile-avatar">
            <div class="profile-name"><?php echo htmlspecialchars($user['username']); ?></div>
            <div class="profile-email"><?php echo htmlspecialchars($user['email']); ?></div>
            <div style="margin-top: 20px;">
                <a href="EditProfile.php" class="btn-glowup">Edit Profile</a>
                <a href="order.php" class="btn-glowup">Order History</a>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Personal Information -->
            <div class="profile-section">
                <h3 class="section-title">
                    <span class="section-icon">👤</span>
                    Personal Information
                </h3>
                <div class="profile-field">
                    <div class="field-label">Username</div>
                    <div class="field-value"><?php echo htmlspecialchars($user['username']); ?></div>
                </div>
                <div class="profile-field">
                    <div class="field-label">Full Name</div>
                    <div class="field-value"><?php echo !empty($user['full_name']) ? htmlspecialchars($user['full_name']) : 'Not provided'; ?></div>
                </div>
                <div class="profile-field">
                    <div class="field-label">Email</div>
                    <div class="field-value"><?php echo htmlspecialchars($user['email']); ?></div>
                </div>
                <div class="profile-field">
                    <div class="field-label">Phone</div>
                    <div class="field-value"><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Not provided'; ?></div>
                </div>
                <div class="profile-field">
                    <div class="field-label">Date of Birth</div>
                    <div class="field-value"><?php echo !empty($user['date_of_birth']) ? htmlspecialchars($user['date_of_birth']) : 'Not provided'; ?></div>
                </div>
                <div class="profile-field">
                    <div class="field-label">Gender</div>
                    <div class="field-value"><?php echo !empty($user['gender']) ? htmlspecialchars($user['gender']) : 'Not provided'; ?></div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="profile-section">
                <h3 class="section-title">
                    <span class="section-icon">📍</span>
                    Shipping Address
                </h3>
                <div class="profile-field">
                    <div class="field-label">Street Address</div>
                    <div class="field-value"><?php echo !empty($user['street_address']) ? htmlspecialchars($user['street_address']) : 'Not provided'; ?></div>
                </div>
                <div class="profile-field">
                    <div class="field-label">City</div>
                    <div class="field-value"><?php echo !empty($user['city']) ? htmlspecialchars($user['city']) : 'Not provided'; ?></div>
                </div>
                <div class="profile-field">
                    <div class="field-label">Postal Code</div>
                    <div class="field-value"><?php echo !empty($user['postal_code']) ? htmlspecialchars($user['postal_code']) : 'Not provided'; ?></div>
                </div>
                <div class="profile-field">
                    <div class="field-label">Country</div>
                    <div class="field-value"><?php echo !empty($user['country']) ? htmlspecialchars($user['country']) : 'Not provided'; ?></div>
                </div>
                <button class="btn-glowup btn-outline" onclick="window.location.href='EditProfile.php'">Update Address</button>
            </div>


            <div class="profile-section">
                <h3 class="section-title">
                    <span class="section-icon">📦</span>
                    Recent Orders
                </h3>

                <?php if (empty($recent_orders)): ?>
                    <div class="no-data">
                        <p>No orders found.</p>
                        <p>Start shopping to see your order history here!</p>
                    </div>
                    <a href="home.php" class="btn-glowup btn-outline">Start Shopping</a>
                <?php else: ?>
                    <?php foreach ($recent_orders as $order): ?>
                        <div class="order-item">
                            <div class="order-info">
                                <div class="order-number">Order #<?php echo htmlspecialchars($order['order_id']); ?></div>
                                <div class="order-date"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></div>
                                <div class="order-details">
                                    <?php echo $order['item_count']; ?> item(s) • <?php echo formatCurrency($order['total_amount']); ?>
                                </div>
                            </div>
                            <div class="order-actions">
                                <?php echo formatOrderStatus($order['status']); ?>
                                <?php if (!empty($order['tracking_number'])): ?>
                                    <div class="tracking-info">
                                        <small>Tracking: <?php echo htmlspecialchars($order['tracking_number']); ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div style="margin-top: 15px; text-align: center;">
                        <a href="order.php" class="btn-glowup btn-outline">View All Orders</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>




        <!-- Logout Section -->
        <div style="text-align: center; margin-top: 30px;">
            <a href="logout.php" class="btn-glowup btn-danger">Logout</a>
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
        </div>

      <div class="c-footer" title="back to top">
        <button type="button" id="backtotop" onclick="scrollToTop()">&#8593;</button>
    </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Your existing JavaScript remains the same
            window.addEventListener('scroll', function() {
                if (window.scrollY > 100) {
                    document.body.classList.add('scrolled');
                } else {
                    document.body.classList.remove('scrolled');
                }
            });

            function scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            document.querySelectorAll('.btn-glowup').forEach(button => {
                button.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'translateY(-2px)';
                    }, 100);
                });
            });

            // Auto-hide alerts after 5 seconds
            document.addEventListener('DOMContentLoaded', function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    setTimeout(() => {
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.remove();
                        }, 300);
                    }, 5000);
                });
            });
    
// Scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
        </script>
</body>

</html>