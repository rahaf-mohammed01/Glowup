<?php
include('db.php');
session_start();

// Check if user is logged in (optional, you can also show orders for guests)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Handle success message from checkout
$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == '1' && isset($_GET['order_id'])) {
    $success_message = "Order " . htmlspecialchars($_GET['order_id']) . " has been placed successfully!";
}

// Pagination settings
$orders_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $orders_per_page;

// Filter settings
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date_range']) ? $_GET['date_range'] : '';
$search_filter = isset($_GET['search']) ? $_GET['search'] : '';

// Build the WHERE clause
$where_conditions = [];
$params = [];
$types = '';

// If user is logged in, show only their orders, otherwise show recent orders (demo)
if ($user_id) {
    $where_conditions[] = "o.user_id = ?";
    $params[] = $user_id;
    $types .= 'i';
} else {
    // For guests, show orders from session or recent orders with same email
    if (isset($_SESSION['guest_email']) && !empty($_SESSION['guest_email'])) {
        $where_conditions[] = "o.email = ?";
        $params[] = $_SESSION['guest_email'];
        $types .= 's';
    } else {
        // Show only recent orders (last 24 hours) for demo purposes
        $where_conditions[] = "o.order_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    }
}

// Status filter
if (!empty($status_filter)) {
    $where_conditions[] = "o.status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

// Date filter
if (!empty($date_filter)) {
    switch ($date_filter) {
        case 'today':
            $where_conditions[] = "DATE(o.order_date) = CURDATE()";
            break;
        case 'week':
            $where_conditions[] = "o.order_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $where_conditions[] = "o.order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
        case '3months':
            $where_conditions[] = "o.order_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
            break;
        case 'year':
            $where_conditions[] = "o.order_date >= DATE_SUB(NOW(), INTERVAL 365 DAY)";
            break;
    }
}

// Search filter (search by order ID, name, or email)
if (!empty($search_filter)) {
    $where_conditions[] = "(o.order_id LIKE ? OR o.name LIKE ? OR o.email LIKE ?)";
    $search_param = '%' . $search_filter . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

// Build the complete query
$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM orders o $where_clause";
if (!empty($params)) {
    $count_stmt = $conn->prepare($count_query);
    if (!empty($types)) {
        $count_stmt->bind_param($types, ...$params);
    }
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_orders = $count_result->fetch_assoc()['total'];
    $count_stmt->close();
} else {
    $count_result = $conn->query($count_query);
    $total_orders = $count_result->fetch_assoc()['total'];
}

$total_pages = ceil($total_orders / $orders_per_page);

// Get orders with pagination
$query = "SELECT o.*, 
          COUNT(oi.id) as item_count,
          GROUP_CONCAT(CONCAT(oi.quantity, 'x ', COALESCE(p.name, 'Product')) SEPARATOR ', ') as items_summary
          FROM orders o 
          LEFT JOIN order_items oi ON o.id = oi.order_id 
          LEFT JOIN products p ON oi.product_id = p.id
          $where_clause 
          GROUP BY o.id 
          ORDER BY o.order_date DESC 
          LIMIT ? OFFSET ?";

// Add pagination parameters
$params[] = $orders_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($query);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$orders = $stmt->get_result();

// Function to get status badge class
function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'pending': return 'status-processing';
        case 'processing': return 'status-processing';
        case 'confirmed': return 'status-processing';
        case 'shipped': return 'status-shipped';
        case 'delivered': return 'status-delivered';
        case 'cancelled': return 'status-cancelled';
        case 'refunded': return 'status-cancelled';
        default: return 'status-processing';
    }
}

// Function to get order details
function getOrderItems($conn, $order_id) {
    $query = "SELECT oi.*, p.name as product_name, p.image_url 
              FROM order_items oi 
              LEFT JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowUp - Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
     <link rel="stylesheet" type="text/css" href="order.css">
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

    <!-- Main Orders Container -->
    <div class="orders-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">Order History</div>
            <div class="page-subtitle">Track all your orders</div>
        </div>

        <!-- Success Message -->
        <?php if (!empty($success_message)): ?>
        <div class="success-alert" id="successAlert">
            <i class="fas fa-check-circle"></i>
            <?php echo $success_message; ?>
        </div>
        <?php endif; ?>

        <!-- Filters Section -->
        <div class="filters-section">
            <form method="GET" action="order.php">
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">Status</label>
                        <select class="form-select" name="status" onchange="this.form.submit()">
                            <option value="">All Orders</option>
                            <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Processing" <?php echo $status_filter == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="Confirmed" <?php echo $status_filter == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="Shipped" <?php echo $status_filter == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="Delivered" <?php echo $status_filter == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="Cancelled" <?php echo $status_filter == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            <option value="Refunded" <?php echo $status_filter == 'Refunded' ? 'selected' : ''; ?>>Refunded</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Date Range</label>
                        <select class="form-select" name="date_range" onchange="this.form.submit()">
                            <option value="">All Time</option>
                            <option value="today" <?php echo $date_filter == 'today' ? 'selected' : ''; ?>>Today</option>
                            <option value="week" <?php echo $date_filter == 'week' ? 'selected' : ''; ?>>Last Week</option>
                            <option value="month" <?php echo $date_filter == 'month' ? 'selected' : ''; ?>>Last Month</option>
                            <option value="3months" <?php echo $date_filter == '3months' ? 'selected' : ''; ?>>Last 3 Months</option>
                            <option value="year" <?php echo $date_filter == 'year' ? 'selected' : ''; ?>>Last Year</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Search</label>
                        <input type="text" class="form-control" name="search" 
                               placeholder="Order ID, Name, or Email" 
                               value="<?php echo htmlspecialchars($search_filter); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <button type="submit" class="btn btn-glowup">
                            <i class="fas fa-search"></i> Filter
                        </button>
            
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Orders List -->
        <div class="orders-list">
            <?php if ($orders->num_rows > 0): ?>
                <?php while ($order = $orders->fetch_assoc()): ?>
                <div class="order-card">
                    <!-- Order Header -->
                    <div class="order-header">
                        <div class="order-info">
                            <div class="order-number">Order #<?php echo htmlspecialchars($order['order_id']); ?></div>
                            <div class="order-date">
                                <i class="fas fa-calendar-alt"></i>
                                <?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?>
                            </div>
                        </div>
                        <div class="order-total">$<?php echo number_format($order['total_amount'], 2); ?></div>
                        <div class="order-status <?php echo getStatusBadgeClass($order['status']); ?>">
                            <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="customer-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Customer:</strong> <?php echo htmlspecialchars($order['name']); ?><br>
                                <strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?><br>
                                <strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Shipping Address:</strong><br>
                                <?php echo nl2br(htmlspecialchars($order['address'])); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tracking Info (if available) -->
                    <?php if (!empty($order['tracking_number'])): ?>
                    <div class="tracking-info" style="background-color: #e3f2fd; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Tracking Number:</strong> <?php echo htmlspecialchars($order['tracking_number']); ?><br>
                                <strong>Carrier:</strong> <?php echo htmlspecialchars($order['carrier'] ?? 'Standard Shipping'); ?>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($order['estimated_delivery'])): ?>
                                <strong>Estimated Delivery:</strong> <?php echo date('F j, Y', strtotime($order['estimated_delivery'])); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Order Items -->
                    <div class="order-items">
                        <h6 style="margin-bottom: 15px; color: #333; font-weight: 600;">
                            <i class="fas fa-box"></i> Items (<?php echo $order['item_count']; ?>)
                        </h6>
                        
                        <?php 
                        $order_items = getOrderItems($conn, $order['id']); 
                        if ($order_items->num_rows > 0):
                        ?>
                            <?php while ($item = $order_items->fetch_assoc()): ?>
                            <div class="item-row">
                                <img src="<?php echo !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : 'https://via.placeholder.com/60x60?text=No+Image'; ?>" 
                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                     class="item-image">
                                <div class="item-details">
                                    <div class="item-name"><?php echo htmlspecialchars($item['product_name'] ?: 'Product'); ?></div>
                                    <div class="item-specs">
                                        Quantity: <?php echo $item['quantity']; ?> × $<?php echo number_format($item['price'], 2); ?>
                                    </div>
                                </div>
                                <div class="item-price">
                                    $<?php echo number_format($item['quantity'] * $item['price'], 2); ?>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="item-row">
                                <div class="item-details" style="text-align: center; width: 100%; color: #666;">
                                    <i class="fas fa-info-circle"></i> Item details not available
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Order Summary -->
                    <div class="order-summary">
                        <div class="row">
                            <div class="col-md-8">
                                <strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method'] ?? 'Not specified'); ?>
                                <?php if (!empty($order['notes'])): ?>
                                <br><strong>Notes:</strong> <?php echo htmlspecialchars($order['notes']); ?>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 text-end">
                                <div style="font-size: 18px; font-weight: 600; color: #C5AB96;">
                                    Total: $<?php echo number_format($order['total_amount'], 2); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Actions -->
                    <div class="order-actions">
                        <?php if (in_array(strtolower($order['status']), ['pending', 'confirmed'])): ?>
                        <button class="btn btn-outline" onclick="cancelOrder(<?php echo $order['id']; ?>)">
                            <i class="fas fa-times"></i> Cancel Order
                        </button>
                        <?php endif; ?>
                        
                        <?php if (in_array(strtolower($order['status']), ['delivered', 'shipped'])): ?>
                        <a href="#" class="btn btn-outline" onclick="alert('Return/Exchange feature coming soon!')">
                            <i class="fas fa-undo"></i> Return/Exchange
                        </a>
                        <?php endif; ?>
                        
                        <button class="btn btn-glowup" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        
                        <button class="btn btn-glowup" onclick="downloadInvoice('<?php echo $order['order_id']; ?>')">
                            <i class="fas fa-file-pdf"></i> Download Invoice
                        </button>
                        
                        <?php if (strtolower($order['status']) == 'delivered'): ?>
                        <button class="btn btn-glowup" onclick="writeReview(<?php echo $order['id']; ?>)">
                            <i class="fas fa-star"></i> Write Review
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination-container">
                    <nav aria-label="Orders pagination">
                        <ul class="pagination">
                <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            for ($i = $start_page; $i <= $end_page; $i++): 
                            ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- No Orders Found -->
                <div class="no-orders">
                    <i class="fas fa-shopping-bag"></i>
                    <h4>No Orders Found</h4>
                    <p>You haven't placed any orders yet, or no orders match your current filters.</p>
                    <a href="home.php" class="btn btn-glowup">
                        Start Shopping
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-hide success message after 5 seconds
        setTimeout(function() {
            const successAlert = document.getElementById('successAlert');
            if (successAlert) {
                successAlert.style.transition = 'opacity 0.5s ease';
                successAlert.style.opacity = '0';
                setTimeout(() => {
                    successAlert.remove();
                }, 500);
            }
        }, 5000);

        // Order management functions
        function viewOrderDetails(orderId) {
            // In a real application, this would open a detailed view modal or redirect to a details page
            alert('Order details view functionality would open here for Order ID: ' + orderId);
            // Example: window.location.href = 'order-details.php?id=' + orderId;
        }

        function cancelOrder(orderId) {
            if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
                // AJAX call to cancel order
                fetch('cancel_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: orderId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order cancelled successfully!');
                        location.reload();
                    } else {
                        alert('Failed to cancel order: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error cancelling order. Please try again.');
                });
            }
        }

        function downloadInvoice(orderNumber) {
            // In a real application, this would generate and download a PDF invoice
            alert('Invoice download functionality would trigger here for Order: ' + orderNumber);
            // Example: window.open('generate_invoice.php?order=' + orderNumber, '_blank');
        }

        function writeReview(orderId) {
            // In a real application, this would open a review form modal or redirect to review page
            alert('Review writing functionality would open here for Order ID: ' + orderId);
            // Example: window.location.href = 'write-review.php?order_id=' + orderId;
        }

        // Add smooth scrolling for better UX
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Add loading state for filter form
        document.querySelector('.filters-section form').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Filtering...';
            submitBtn.disabled = true;
            
            // Re-enable after 3 seconds in case of issues
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });

        // Enhance mobile experience
        if (window.innerWidth < 768) {
            // Adjust filters for mobile
            const filterRow = document.querySelector('.filter-row');
            if (filterRow) {
                filterRow.style.flexDirection = 'column';
                filterRow.style.gap = '15px';
            }
        }

        // Add print functionality
        function printOrder(orderId) {
            const printWindow = window.open('print_order.php?id=' + orderId, '_blank');
            printWindow.onload = function() {
                printWindow.print();
            };
        }

        // Track order function
        function trackOrder(trackingNumber) {
            if (trackingNumber) {
                // This would integrate with shipping provider's tracking API
                alert('Tracking functionality would redirect to carrier tracking page for: ' + trackingNumber);
                // Example: window.open('https://www.fedex.com/apps/fedextrack/?tracknumbers=' + trackingNumber, '_blank');
            }
        }

        // Initialize tooltips if Bootstrap is loaded
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    </script>
</body>
</html>

<?php
// Close database connection
$stmt->close();
$conn->close();
?>