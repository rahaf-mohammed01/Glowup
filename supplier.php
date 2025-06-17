<?php
ob_start();
session_start();

include('auth_middleware.php');
// Require supplier role to access this page
requireSupplier();

// Include database connection (with error handling)
$conn = null;
if (file_exists('db.php')) {
    include 'db.php';
} else {
    // Create a dummy connection for testing
    echo "<!-- Warning: db.php not found -->";
}

$message = '';
$messageType = '';

// Handle form submissions with POST-REDIRECT-GET pattern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn) {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    switch ($action) {
        case 'add_product':
            $product_name = $_POST['product_name'];
            $product_description = $_POST['product_description'];
            $price = $_POST['price'];
            $stock = $_POST['stock'];
            $category = $_POST['category'];

            // Handle file upload
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
                $image = $_FILES['product_image']['name'];
                $target_dir = "uploads/";

                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $target_file = $target_dir . basename($image);

                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                    // Insert into both product_name AND name columns for consistency
                    $stmt = $conn->prepare("INSERT INTO products (product_name, name, product_description, price, stock, category, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssdsss", $product_name, $product_name, $product_description, $price, $stock, $category, $image);

                    if ($stmt->execute()) {
                        $_SESSION['message'] = "Product added successfully!";
                        $_SESSION['messageType'] = "success";
                    } else {
                        $_SESSION['message'] = "Error: " . $stmt->error;
                        $_SESSION['messageType'] = "error";
                    }
                    $stmt->close();
                } else {
                    $_SESSION['message'] = "Error uploading image.";
                    $_SESSION['messageType'] = "error";
                }
            } else {
                $_SESSION['message'] = "Please select an image.";
                $_SESSION['messageType'] = "error";
            }

            // Redirect to prevent resubmission
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
            break;

        case 'edit_product':
            $product_id = intval($_POST['product_id']);
            $product_name = trim($_POST['product_name']);
            $product_description = trim($_POST['product_description']);
            $price = floatval($_POST['price']);
            $stock = intval($_POST['stock']);
            $category = trim($_POST['category']);

            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
                $image = $_FILES['product_image']['name'];
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($image);
                
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                    $stmt = $conn->prepare("UPDATE products SET product_name=?, name=?, product_description=?, price=?, stock=?, category=?, image=? WHERE id=?");
                    $stmt->bind_param("sssdissi", $product_name, $product_name, $product_description, $price, $stock, $category, $image, $product_id);
                } else {
                    $_SESSION['message'] = "Error uploading image.";
                    $_SESSION['messageType'] = "error";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }
            } else {
                $stmt = $conn->prepare("UPDATE products SET product_name=?, name=?, product_description=?, price=?, stock=?, category=? WHERE id=?");
                $stmt->bind_param("sssdisi", $product_name, $product_name, $product_description, $price, $stock, $category, $product_id);
            }

            if ($stmt->execute()) {
                $_SESSION['message'] = "Product updated successfully!";
                $_SESSION['messageType'] = "success";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
                $_SESSION['messageType'] = "error";
            }
            $stmt->close();

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
            break;

        case 'delete_product':
            $product_id = $_POST['product_id'];
            $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
            $stmt->bind_param("i", $product_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Product deleted successfully!";
                $_SESSION['messageType'] = "success";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
                $_SESSION['messageType'] = "error";
            }
            $stmt->close();

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
            break;

        case 'update_inventory':
            $product_id = $_POST['product_id'];
            $new_stock = $_POST['new_stock'];
            $action_type = $_POST['inventory_action'];

            if ($action_type === 'set') {
                $stmt = $conn->prepare("UPDATE products SET stock=? WHERE id=?");
                $stmt->bind_param("ii", $new_stock, $product_id);
            } elseif ($action_type === 'add') {
                $stmt = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id=?");
                $stmt->bind_param("ii", $new_stock, $product_id);
            } elseif ($action_type === 'subtract') {
                $stmt = $conn->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id=?");
                $stmt->bind_param("ii", $new_stock, $product_id);
            }

            if ($stmt->execute()) {
                $_SESSION['message'] = "Inventory updated successfully!";
                $_SESSION['messageType'] = "success";
            } else {
                $_SESSION['message'] = "Error updating inventory: " . $stmt->error;
                $_SESSION['messageType'] = "error";
            }
            $stmt->close();

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
            break;

        case 'update_order_status':
            $order_id = $_POST['order_id'];
            $status = $_POST['status'];

            $stmt = $conn->prepare("UPDATE orders SET status=? WHERE order_id=?");
            $stmt->bind_param("si", $status, $order_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Order status updated successfully!";
                $_SESSION['messageType'] = "success";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
                $_SESSION['messageType'] = "error";
            }
            $stmt->close();

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
            break;

        case 'assign_tracking':
            $order_id = $_POST['order_id'];
            $tracking_number = $_POST['tracking_number'];
            $carrier = $_POST['carrier'];
            $estimated_delivery = $_POST['estimated_delivery'];

            $stmt = $conn->prepare("UPDATE orders SET tracking_number=?, carrier=?, estimated_delivery=?, status='Shipped' WHERE order_id=?");
            $stmt->bind_param("sssi", $tracking_number, $carrier, $estimated_delivery, $order_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Tracking assigned successfully!";
                $_SESSION['messageType'] = "success";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
                $_SESSION['messageType'] = "error";
            }
            $stmt->close();

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
            break;

        case 'process_return':
            $return_id = $_POST['return_id'];
            $action_taken = $_POST['action_taken'];
            $return_status = $_POST['return_status'];
            $refund_amount = $_POST['refund_amount'] ?? 0;

            $stmt = $conn->prepare("UPDATE returns SET action_taken=?, status=?, refund_amount=? WHERE return_id=?");
            $stmt->bind_param("ssdi", $action_taken, $return_status, $refund_amount, $return_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Return processed successfully!";
                $_SESSION['messageType'] = "success";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
                $_SESSION['messageType'] = "error";
            }
            $stmt->close();

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
            break;

        case 'resolve_issue':
            $issue_id = $_POST['issue_id'];
            $resolution = $_POST['resolution'];
            $resolution_notes = $_POST['resolution_notes'];

            $stmt = $conn->prepare("UPDATE delivery_issues SET resolution=?, resolution_notes=?, status='Resolved' WHERE issue_id=?");
            $stmt->bind_param("ssi", $resolution, $resolution_notes, $issue_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Issue resolved successfully!";
                $_SESSION['messageType'] = "success";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
                $_SESSION['messageType'] = "error";
            }
            $stmt->close();

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
            break;
    }
}

// Get messages from session and clear them
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['messageType'];
    unset($_SESSION['message'], $_SESSION['messageType']);
}

// Initialize data arrays
$products_result = null;
$orders_result = null;
$low_stock_result = null;
$pending_orders = [];
$active_shipments = [];
$pending_returns = [];
$delivery_issues = [];

// Fetch data for display
if ($conn) {
    $products_result = $conn->query("SELECT * FROM products ORDER BY id DESC");
    $orders_result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
    $low_stock_result = $conn->query("SELECT * FROM products WHERE stock <= 10 ORDER BY stock ASC");
    
    // Fetch pending orders
  $pending_orders_query = $conn->query("SELECT order_id, name, total_amount, status FROM orders WHERE status = 'Pending' LIMIT 10");
if ($pending_orders_query) {
    while ($row = $pending_orders_query->fetch_assoc()) {
        $row['items'] = '2 items'; //  calculate this from order_items table
        $pending_orders[] = $row;
    }
}
    
    // Fetch active shipments
   $shipments_query = $conn->query("SELECT order_id, name, status FROM orders WHERE status IN ('Shipped', 'In Transit') LIMIT 10");
    if ($shipments_query) {
        $active_shipments = $shipments_query->fetch_all(MYSQLI_ASSOC);
    }
    
    // Fetch pending returns
    $returns_query = $conn->query("SELECT return_id, order_id, name, reason, status FROM returns WHERE status = 'Pending' LIMIT 10");
    if ($returns_query) {
        $pending_returns = $returns_query->fetch_all(MYSQLI_ASSOC);
    }
    
    // Fetch delivery issues
    $issues_query = $conn->query("SELECT issue_id, order_id, name, issue_type, status FROM delivery_issues WHERE status = 'Open' LIMIT 10");
    if ($issues_query) {
        $delivery_issues = $issues_query->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="supplier.css">
    <script src="supplier.js"></script>

    <title>Supplier Dashboard</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Supplier Dashboard</h1>
            <p>Manage orders, shipments, and customer service efficiently</p>
        </div>

        <!-- Debug Information -->
        <?php if (!$conn): ?>
            <div class="debug-info">
                <strong>Debug Info:</strong> Database connection not available. Showing mock data for demonstration.
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($pending_orders); ?></div>
                <div class="stat-label">Pending Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">18</div>
                <div class="stat-label">Ready to Ship</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($active_shipments); ?></div>
                <div class="stat-label">In Transit</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($delivery_issues); ?></div>
                <div class="stat-label">Issues</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($pending_returns); ?></div>
                <div class="stat-label">Returns</div>
            </div>
        </div>

        <div class="tabs">
            <button class="tab active" onclick="showTab('orders')">Product&Order Management</button>
            <button class="tab" onclick="showTab('shipping')">Shipping & Tracking</button>
            <button class="tab" onclick="showTab('returns')">Returns & Exchanges</button>
            <button class="tab" onclick="showTab('issues')">Delivery Issues</button>
        </div>


        <!-- Order Management Tab -->
        <div id="orders" class="tab-content active">
            <div class="cards-grid">
                    <div class="card">
                    <div class="card-header products">
                        <h3>Add New Product</h3>
                        <p>Add products to your inventory</p>
                    </div>
                    <div class="card-content">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="add_product">

                            <div class="form-group">
                                <label for="product_name">Product Name:</label>
                                <input type="text" name="product_name" id="product_name" required>
                            </div>

                            <div class="form-group">
                                <label for="product_description">Description:</label>
                                <textarea name="product_description" id="product_description" rows="3" required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="price">Price:</label>
                                <input type="number" name="price" id="price" step="0.01" required>
                            </div>

                            <div class="form-group">
                                <label for="stock">Stock:</label>
                                <input type="number" name="stock" id="stock" required>
                            </div>

                            <div class="form-group">
                                <label for="category">Category:</label>
                                <select name="category" id="category" required>
                                    <option value="Women">Women</option>
                                    <option value="Men">Men</option>
                                    <option value="Girls">Girls</option>
                                    <option value="Boys">Boys</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="product_image">Product Image:</label>
                                <input type="file" name="product_image" id="product_image" accept="image/*" required>
                            </div>

                            <button type="submit" class="btn">Add Product</button>
                        </form>
                    </div>
                </div>
                <!-- Pending Orders -->
                <div class="card">
                    <div class="card-header orders">
                        <h3>Pending Orders</h3>
                        <p>Orders waiting to be processed</p>
                    </div>
                    <div class="card-content">
                        <?php if (!empty($pending_orders)): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Items</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['name']); ?></td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td><?php echo $order['items']; ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-success" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'Processing')">Process</button>
                                                <button class="btn-warning" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)">Details</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <p>No pending orders at this time.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Order Status Update -->
                <div class="card">
                    <div class="card-header orders">
                        <h3>Update Order Status</h3>
                        <p>Change order status and add notes</p>
                    </div>
                    <div class="card-content">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_order_status">

                            <div class="form-group">
                                <label for="order_id">Order ID:</label>
                                <input type="number" name="order_id" id="order_id" required>
                            </div>

                            <div class="form-group">
                                <label for="status">Status:</label>
                                <select name="status" id="status" required>
                                    <option value="Processing">Processing</option>
                                    <option value="Ready to Ship">Ready to Ship</option>
                                    <option value="Shipped">Shipped</option>
                                    <option value="Delivered">Delivered</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes (Optional):</label>
                                <textarea name="notes" id="notes" rows="3" placeholder="Add any relevant notes..."></textarea>
                            </div>

                            <button type="submit" class="btn">Update Status</button>
                        </form>
                    </div>
                </div>
                </div>
                
            </div>
        </div>
        <!-- Shipping & Tracking Tab -->
        <div id="shipping" class="tab-content">
            <div class="cards-grid">
                <!-- Assign Tracking -->
                <div class="card">
                    <div class="card-header shipping">
                        <h3>Assign Tracking Number</h3>
                        <p>Add tracking information for shipments</p>
                    </div>
                    <div class="card-content">
                        <form method="POST">
                            <input type="hidden" name="action" value="assign_tracking">

                            <div class="form-group">
                                <label for="ship_order_id">Order ID:</label>
                                <input type="number" name="order_id" id="ship_order_id" required>
                            </div>

                            <div class="form-group">
                                <label for="tracking_number">Tracking Number:</label>
                                <input type="text" name="tracking_number" id="tracking_number" required>
                            </div>

                            <div class="form-group">
                                <label for="carrier">Carrier:</label>
                                <select name="carrier" id="carrier" required>
                                    <option value="FedEx">FedEx</option>
                                    <option value="UPS">UPS</option>
                                    <option value="DHL">DHL</option>
                                    <option value="USPS">USPS</option>
                                    <option value="Local Courier">Local Courier</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="estimated_delivery">Estimated Delivery Date:</label>
                                <input type="date" name="estimated_delivery" id="estimated_delivery" required>
                            </div>

                            <button type="submit" class="btn">Assign Tracking</button>
                        </form>
                    </div>
                </div>

                <!-- Active Shipments -->
                <div class="card">
                    <div class="card-header shipping">
                        <h3>Active Shipments</h3>
                        <p>Track current deliveries</p>
                    </div>
                    <div class="card-content">
                        <?php if (!empty($active_shipments)): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tracking#</th>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Carrier</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($active_shipments as $shipment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($shipment['tracking_number']); ?></td>
                                        <td>#<?php echo $shipment['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($shipment['name']); ?></td>
                                        <td><?php echo htmlspecialchars($shipment['carrier']); ?></td>
                                        <td><span class="status-badge status-shipped"><?php echo $shipment['status']; ?></span></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-warning" onclick="updateDeliveryStatus('<?php echo $shipment['tracking_number']; ?>')">Update</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <p>No active shipments at this time.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Update Delivery Status -->
                <div class="card">
                    <div class="card-header shipping">
                        <h3>Update Delivery Status</h3>
                        <p>Update tracking and delivery information</p>
                    </div>
                    <div class="card-content">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_delivery_status">

                            <div class="form-group">
                                <label for="tracking_id">Tracking Number:</label>
                                <input type="text" name="tracking_id" id="tracking_id" required>
                            </div>

                            <div class="form-group">
                                <label for="delivery_status">Delivery Status:</label>
                                <select name="delivery_status" id="delivery_status" required>
                                    <option value="In Transit">In Transit</option>
                                    <option value="Out for Delivery">Out for Delivery</option>
                                    <option value="Delivered">Delivered</option>
                                    <option value="Delivery Attempted">Delivery Attempted</option>
                                    <option value="Exception">Exception</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="delivery_notes">Delivery Notes:</label>
                                <textarea name="delivery_notes" id="delivery_notes" rows="3" placeholder="Add delivery notes..."></textarea>
                            </div>

                            <button type="submit" class="btn">Update Delivery</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Returns & Exchanges Tab -->
        <div id="returns" class="tab-content">
            <div class="cards-grid">
                <!-- Pending Returns -->
                <div class="card">
                    <div class="card-header returns">
                        <h3>Pending Returns</h3>
                        <p>Returns awaiting processing</p>
                    </div>
                    <div class="card-content">
                        <?php if (!empty($pending_returns)): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Return ID</th>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_returns as $return): ?>
                                    <tr>
                                        <td>#<?php echo $return['return_id']; ?></td>
                                        <td>#<?php echo $return['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($return['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($return['reason']); ?></td>
                                        <td><span class="status-badge status-pending"><?php echo $return['status']; ?></span></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-success" onclick="processReturn(<?php echo $return['return_id']; ?>)">Process</button>
                                                <button class="btn-warning" onclick="viewReturnDetails(<?php echo $return['return_id']; ?>)">Details</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <p>No pending returns at this time.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Process Return -->
                <div class="card">
                    <div class="card-header returns">
                        <h3>Process Return</h3>
                        <p>Handle return requests and refunds</p>
                    </div>
                    <div class="card-content">
                        <form method="POST">
                            <input type="hidden" name="action" value="process_return">

                            <div class="form-group">
                                <label for="return_id">Return ID:</label>
                                <input type="number" name="return_id" id="return_id" required>
                            </div>

                            <div class="form-group">
                                <label for="action_taken">Action Taken:</label>
                                <select name="action_taken" id="action_taken" required>
                                    <option value="Full Refund">Full Refund</option>
                                    <option value="Partial Refund">Partial Refund</option>
                                    <option value="Exchange">Exchange</option>
                                    <option value="Store Credit">Store Credit</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="return_status">Return Status:</label>
                                <select name="return_status" id="return_status" required>
                                    <option value="Approved">Approved</option>
                                    <option value="Processed">Processed</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="refund_amount">Refund Amount (if applicable):</label>
                                <input type="number" name="refund_amount" id="refund_amount" step="0.01" min="0">
                            </div>

                            <button type="submit" class="btn">Process Return</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Issues Tab -->
        <div id="issues" class="tab-content">
            <div class="cards-grid">
                <!-- Open Issues -->
                <div class="card">
                    <div class="card-header issues">
                        <h3>Delivery Issues</h3>
                        <p>Customer reported delivery problems</p>
                    </div>
                    <div class="card-content">
                        <?php if (!empty($delivery_issues)): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Issue ID</th>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Issue Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($delivery_issues as $issue): ?>
                                    <tr>
                                        <td>#<?php echo $issue['issue_id']; ?></td>
                                        <td>#<?php echo $issue['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($issue['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($issue['issue_type']); ?></td>
                                        <td><span class="status-badge status-open"><?php echo $issue['status']; ?></span></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-success" onclick="resolveIssue(<?php echo $issue['issue_id']; ?>)">Resolve</button>
                                                <button class="btn-warning" onclick="viewIssueDetails(<?php echo $issue['issue_id']; ?>)">Details</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <p>No delivery issues at this time.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Resolve Issue -->
                <div class="card">
                    <div class="card-header issues">
                        <h3>Resolve Delivery Issue</h3>
                        <p>Address and resolve customer delivery concerns</p>
                    </div>
                    <div class="card-content">
                        <form method="POST">
                            <input type="hidden" name="action" value="resolve_issue">

                            <div class="form-group">
                                <label for="issue_id">Issue ID:</label>
                                <input type="number" name="issue_id" id="issue_id" required>
                            </div>

                            <div class="form-group">
                                <label for="resolution">Resolution:</label>
                                <select name="resolution" id="resolution" required>
                                    <option value="Reshipment">Reshipment</option>
                                    <option value="Refund">Refund</option>
                                    <option value="Replacement">Replacement</option>
                                    <option value="Investigation">Investigation</option>
                                    <option value="Customer Error">Customer Error</option>
                                    <option value="Carrier Issue">Carrier Issue</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="resolution_notes">Resolution Notes:</label>
                                <textarea name="resolution_notes" id="resolution_notes" rows="4" placeholder="Describe the resolution and any actions taken..." required></textarea>
                            </div>

                            <button type="submit" class="btn">Resolve Issue</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Management Section -->
        <div class="section">
            <h2>Inventory Management</h2>
            
            <!-- Low Stock Alerts -->
            <div class="card">
                <div class="card-header inventory">
                    <h3>Low Stock Alerts</h3>
                    <p>Products with low inventory levels</p>
                </div>
                <div class="card-content">
                    <?php if ($low_stock_result && $low_stock_result->num_rows > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Current Stock</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $low_stock_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                    <td><span class="stock-warning"><?php echo $row['stock']; ?></span></td>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-warning" onclick="updateInventory(<?php echo $row['id']; ?>)">Restock</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <p>All products have adequate stock levels.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Update Inventory -->
            <div class="card">
                <div class="card-header inventory">
                    <h3>Update Inventory</h3>
                    <p>Manage product stock levels</p>
                </div>
                <div class="card-content">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_inventory">

                        <div class="form-group">
                            <label for="inventory_product_id">Product ID:</label>
                            <input type="number" name="product_id" id="inventory_product_id" required>
                        </div>

                        <div class="form-group">
                            <label for="inventory_action">Action:</label>
                            <select name="inventory_action" id="inventory_action" required>
                                <option value="set">Set Stock Level</option>
                                <option value="add">Add to Stock</option>
                                <option value="subtract">Subtract from Stock</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="new_stock">Quantity:</label>
                            <input type="number" name="new_stock" id="new_stock" min="0" required>
                        </div>

                        <button type="submit" class="btn">Update Inventory</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Management Section -->
        <div class="section">
            <h2>Products Management</h2>
            
            <div class="card">
                <div class="card-header products">
                    <h3>All Products</h3>
                    <p>Manage your product catalog</p>
                </div>
                <div class="card-content">
                    <?php if ($products_result && $products_result->num_rows > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $products_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td>
                                        <?php if (!empty($row['image'])): ?>
                                            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" class="product-thumb">
                                        <?php else: ?>
                                            <span class="no-image">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($row['product_description'], 0, 50)) . '...'; ?></td>
                                    <td>SAR<?php echo number_format($row['price'], 2); ?></td>
                                    <td><?php echo $row['stock']; ?></td>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-warning" onclick="editProduct(<?php echo $row['id']; ?>)">Edit</button>
                                            <button class="btn-danger" onclick="deleteProduct(<?php echo $row['id']; ?>)">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <p>No products found. Add your first product above!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

       <!-- Edit Product Modal - tag -->
<div id="editProductModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Product</h3>
            <span class="close" onclick="closeEditModal()">&times;</span>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit_product">
            <input type="hidden" name="product_id" id="edit_product_id">

            <div class="form-group">
                <label for="edit_product_name">Product Name:</label>
                <input type="text" name="product_name" id="edit_product_name" required maxlength="255">
            </div>

            <div class="form-group">
                <label for="edit_product_description">Description:</label>
                <textarea name="product_description" id="edit_product_description" rows="3" required maxlength="1000"></textarea>
            </div>

            <div class="form-group">
                <label for="edit_price">Price (SAR):</label>
                <input type="number" name="price" id="edit_price" step="0.01" min="0" required>
            </div>

            <div class="form-group">
                <label for="edit_stock">Stock Quantity:</label>
                <input type="number" name="stock" id="edit_stock" min="0" required>
            </div>

            <div class="form-group">
                <label for="edit_category">Category:</label>
                <select name="category" id="edit_category" required>
                    <option value="Women">Women</option>
                    <option value="Men">Men</option>
                </select>
            </div>

            <div class="form-group">
                <label for="edit_product_image">Update Image (optional):</label>
                <input type="file" name="product_image" id="edit_product_image" accept="image/*">
                <div class="file-info">Leave empty to keep current image. Max size: 5MB</div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn">Update Product</button>
            </div>
        </form>
    </div>
</div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Confirm Delete</h3>
                    <span class="close" onclick="closeDeleteModal()">&times;</span>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                    <form method="POST" id="deleteProductForm">
                        <input type="hidden" name="action" value="delete_product">
                        <input type="hidden" name="product_id" id="delete_product_id">
                        
                        <div class="modal-actions">
                            <button type="submit" class="btn-danger">Delete Product</button>
                            <button type="button" class="btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        
    <!-- Logout Section -->
    <div style="text-align: center; margin-top: 30px;">
        <a href="logout.php" class="btn-glowup btn-danger">Logout</a>
    </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2024 Supplier Dashboard. All rights reserved.</p>
        </div>
    </div>


<?php
// Clean up
if ($conn) {
    $conn->close();
}
ob_end_flush();
?>