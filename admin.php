<?php
session_start();

include('auth_middleware.php');

// Require admin role to access this page
requireAdmin();

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
            // Add this at the top of your edit_product case for debugging
            error_log("Edit product data: " . print_r($_POST, true));
        case 'edit_product':
            $product_id = intval($_POST['product_id']); // Ensure integer
            $product_name = trim($_POST['product_name']);
            $product_description = trim($_POST['product_description']);
            $price = floatval($_POST['price']);
            $stock = intval($_POST['stock']);
            $category = trim($_POST['category']);

            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
                // Handle file upload
                $stmt = $conn->prepare("UPDATE products SET product_name=?, name=?, product_description=?, price=?, stock=?, category=?, image=? WHERE id=?");
                $stmt->bind_param("sssdissi", $product_name, $product_name, $product_description, $price, $stock, $category, $image, $product_id);
            } else {
                // No file upload
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

        case 'add_user':
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = $_POST['role'];

            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $password, $role);

            if ($stmt->execute()) {
                $_SESSION['message'] = "User added successfully!";
                $_SESSION['messageType'] = "success";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
                $_SESSION['messageType'] = "error";
            }
            $stmt->close();

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
            break;

        case 'update_user_status':
            $user_id = $_POST['user_id'];
            $status = $_POST['status'];

            $stmt = $conn->prepare("UPDATE users SET status=? WHERE user_id=?");
            $stmt->bind_param("si", $status, $user_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "User status updated successfully!";
                $_SESSION['messageType'] = "success";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
                $_SESSION['messageType'] = "error";
            }
            $stmt->close();

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
            break;

        case 'delete_user':
            $user_id = $_POST['user_id'];
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
            $stmt->bind_param("i", $user_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "User deleted successfully!";
                $_SESSION['messageType'] = "success";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
                $_SESSION['messageType'] = "error";
            }
            $stmt->close();

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
            break;

        case 'update_order_status':
            $order_id = $_POST['order_id'];
            $status = $_POST['order_status'];

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

        case 'process_refund':
            $order_id = $_POST['order_id'];

            // Update order status to refunded
            $stmt = $conn->prepare("UPDATE orders SET status='Refunded', refund_date=NOW() WHERE order_id=?");
            $stmt->bind_param("i", $order_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Refund processed successfully!";
                $_SESSION['messageType'] = "success";
            } else {
                $_SESSION['message'] = "Error processing refund: " . $stmt->error;
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

// Fetch data for display
$products_result = null;
$users_result = null;
$orders_result = null;
$low_stock_result = null;

if ($conn) {
    $products_result = $conn->query("SELECT * FROM products ORDER BY id DESC");
    $users_result = $conn->query("SELECT *, DATE_FORMAT(created_at, '%Y-%m-%d') as join_date FROM users WHERE role != 'admin' ORDER BY user_id DESC");
    $orders_result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
    $low_stock_result = $conn->query("SELECT * FROM products WHERE stock <= 10 ORDER BY stock ASC");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="admin.css">
    <script src="admin.js"></script>
    <title>Admin Dashboard</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <p>Complete Store Management System</p>
        </div>

        <!-- Alert Container -->
        <div id="alertContainer" class="alert-container"></div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $products_result ? $products_result->num_rows : '0'; ?></div>
                <div class="stat-label">Total Products</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $users_result ? $users_result->num_rows : '0'; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $orders_result ? $orders_result->num_rows : '0'; ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $low_stock_result ? $low_stock_result->num_rows : '0'; ?></div>
                <div class="stat-label">Low Stock Items</div>
            </div>
        </div>


        <div class="tabs">
            <button class="tab active" onclick="showTab('products')">Products</button>
            <button class="tab" onclick="showTab('inventory')">Inventory</button>
            <button class="tab" onclick="showTab('users')">Customers</button>
            <button class="tab" onclick="showTab('orders')">Orders</button>
        </div>

        <!-- Product Management Tab -->
        <div id="products" class="tab-content active">
            <div class="cards-grid">
                <!-- Add Product Card -->
                <div class="card">
                    <div class="card-header">
                        <h3>Add New Product</h3>
                        <p>Add products to your catalog</p>
                    </div>
                    <div class="card-content">
                        <form method="POST" enctype="multipart/form-data" onsubmit="return showConfirmation(event, 'add-product', 'Add Product', 'Are you sure you want to add this new product?')">
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
                                <label for="stock">Initial Stock:</label>
                                <input type="number" name="stock" id="stock" required>
                            </div>

                            <div class="form-group">
                                <label for="category">Category:</label>
                                <select name="category" id="category" required>
                                    <option value="Women">Women</option>
                                    <option value="Men">Men</option>
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

                <!-- Manage Products Card -->
                <div class="card">
                    <div class="card-header">
                        <h3>Product Catalog</h3>
                        <p>Manage existing products</p>
                    </div>
                    <div class="card-content">
                        <?php if ($products_result && $products_result->num_rows > 0): ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Category</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $products_result->data_seek(0); // Reset result pointer
                                    while ($product = $products_result->fetch_assoc()):
                                        $rowClass = '';
                                        if ($product['stock'] == 0) {
                                            $rowClass = 'out-of-stock';
                                        } elseif ($product['stock'] <= 10) {
                                            $rowClass = 'low-stock';
                                        }
                                        $productName = $product['name'] ?? $product['product_name'] ?? 'Unnamed Product';
                                    ?>
                                        <tr class="<?php echo $rowClass; ?>">
                                            <td>
                                                <?php if (!empty($product['image'])): ?>
                                                    <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Product" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                                <?php else: ?>
                                                    <div style="width: 50px; height: 50px; background: #eee; border-radius: 5px; display: flex; align-items: center; justify-content: center; font-size: 10px;">No Image</div>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($productName); ?></strong></td>
                                            <td><strong>SAR<?php echo number_format($product['price'], 2); ?></strong></td>
                                            <td>
                                                <?php if ($product['stock'] == 0): ?>
                                                    <span class="status-badge status-cancelled">Out of Stock</span>
                                                <?php elseif ($product['stock'] <= 10): ?>
                                                    <span class="status-badge status-pending"><?php echo $product['stock']; ?> (Low)</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-active"><?php echo $product['stock']; ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                                            <td>
                                                <button class="btn-warning" onclick="editProduct(
    <?php echo $product['id']; ?>,
    <?php echo htmlspecialchars(json_encode($productName)); ?>,
    <?php echo htmlspecialchars(json_encode($product['product_description'])); ?>,
    <?php echo $product['price']; ?>,
    <?php echo $product['stock']; ?>,
    <?php echo htmlspecialchars(json_encode($product['category'])); ?>
)">Edit</button>
                                                <form method="POST" style="display: inline;" onsubmit="return showConfirmationForm(event, 'delete-product', 'Delete Product', 'Are you sure you want to delete this product? This action cannot be undone.', 'btn-danger')">
                                                    <input type="hidden" name="action" value="delete_product">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <button type="submit" class="btn-danger">Delete</button>
                                                </form>
                    </div>
                    </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
                </table>
            <?php else: ?>
                <p>No products found.</p>
            <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Inventory Management Tab -->
    <div id="inventory" class="tab-content">
        <div class="card-content">
            <!-- Low Stock Alert Section -->
            <?php if ($low_stock_result && $low_stock_result->num_rows > 0): ?>
                <div class="card" style="margin-top: 20px; border-left: 5px solid #ff6b6b;">
                    <div class="card-header">
                        <h3 style="color: #ff6b6b;">⚠️ Low Stock Alert</h3>
                        <p>Products running low on inventory</p>
                    </div>
                    <div class="card-content">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Current Stock</th>
                                    <th>Category</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($low_product = $low_stock_result->fetch_assoc()): ?>
                                    <tr class="<?php echo $low_product['stock'] == 0 ? 'out-of-stock' : 'low-stock'; ?>">
                                        <td><?php echo htmlspecialchars($low_product['name'] ?? $low_product['product_name']); ?></td>
                                        <td>
                                            <span class="stock-level" style="color: <?php echo $low_product['stock'] == 0 ? '#dc3545' : '#ffc107'; ?>; font-weight: bold;">
                                                <?php echo $low_product['stock']; ?>
                                                <?php echo $low_product['stock'] == 0 ? ' (OUT OF STOCK)' : ' (LOW)'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($low_product['category']); ?></td>
                                        <td>
                                        
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Users/Customers Tab -->
    <div id="users" class="tab-content">
        <div class="cards-grid">
            <!-- Add User Card -->
            <div class="card">
                <div class="card-header">
                    <h3>Add New User</h3>
                    <p>Create customer accounts</p>
                </div>
                <div class="card-content">
                    <form method="POST" onsubmit="return showConfirmationForm(event, 'add-user', 'Add User', 'Are you sure you want to add this new user?', 'btn')">
                        <input type="hidden" name="action" value="add_user">

                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" name="username" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" name="password" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Role:</label>
                            <select name="role" required>
                                <option value="customer">Customer</option>
                                <option value="admin">Admin</option>
                                <option value="admin">Supplier</option>
                            </select>
                        </div>

                        <button type="submit" class="btn">Add User</button>
                    </form>
                </div>
            </div>

            <!-- Manage Users Card -->
            <div class="card">
                <div class="card-header">
                    <h3>Customer Management</h3>
                    <p>Manage user accounts</p>
                </div>
                <div class="card-content">
                    <?php if ($users_result && $users_result->num_rows > 0): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Join Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $users_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo $user['join_date']; ?></td>
                                        <td><?php echo htmlspecialchars($user['status'] ?? 'active'); ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;" onsubmit="return showConfirmationForm(event, 'delete-user', 'Delete User', 'Are you sure you want to delete this user? This action cannot be undone.', 'btn-danger')">
                                                <input type="hidden" name="action" value="delete_user">
                                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                <button type="submit" class="btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No users found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Tab -->
    <div id="orders" class="tab-content">
        <div class="card">
            <div class="card-header">
                <h3>Order Management</h3>
                <p>Track and manage customer orders</p>
            </div>
            <div class="card-content">
                <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $orders_result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $order['order_id'] ?? $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name'] ?? 'N/A'); ?></td>
                                    <td>SAR <?php echo number_format($order['total'] ?? 0, 2); ?></td>
                                    <td>
                                        <span class="status-badge <?php
                                                                    $status = $order['status'] ?? 'pending';
                                                                    echo strtolower($status) === 'completed' ? 'status-active' : (strtolower($status) === 'cancelled' ? 'status-cancelled' : 'status-pending');
                                                                    ?>">
                                            <?php echo htmlspecialchars($order['status'] ?? 'pending'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d', strtotime($order['order_date'] ?? $order['created_at'] ?? 'now')); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline-block; margin-right: 5px;" onsubmit="return showConfirmationForm(event, 'update-order', 'Update Order Status', 'Are you sure you want to update this order status?', 'btn-warning')">
                                            <input type="hidden" name="action" value="update_order_status">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?? $order['id']; ?>">
                                            <select name="order_status" onchange="this.form.submit()" style="padding: 5px;">
                                                <option value="pending" <?php echo ($order['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?php echo ($order['status'] ?? '') === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="shipped" <?php echo ($order['status'] ?? '') === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?php echo ($order['status'] ?? '') === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="completed" <?php echo ($order['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="cancelled" <?php echo ($order['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </form>

                                        <?php if (($order['status'] ?? '') !== 'refunded'): ?>
                                            <form method="POST" style="display: inline-block;" onsubmit="return showConfirmationForm(event, 'process-refund', 'Process Refund', 'Are you sure you want to process a refund for this order?', 'btn-danger')">
                                                <input type="hidden" name="action" value="process_refund">
                                                <input type="hidden" name="order_id" value="<?php echo $order['order_id'] ?? $order['id']; ?>">
                                                <button type="submit" class="btn-danger" style="padding: 5px 10px; font-size: 12px;">Refund</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No orders found.</p>
                <?php endif; ?>
            </div>
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


    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-modal-content">
            <span class="confirmation-close" onclick="closeConfirmationModal()">&times;</span>
            <h2 id="confirmationTitle">Confirm Action</h2>
            <p id="confirmationMessage">Are you sure you want to proceed?</p>
            <div class="confirmation-modal-buttons">
                <button type="button" class="btn" onclick="closeConfirmationModal()" style="background: #6c757d;">Cancel</button>
                <button type="button" class="btn" id="confirmationButton" onclick="proceedWithAction()">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Logout Section -->
    <div style="text-align: center; margin-top: 30px;">
        <a href="logout.php" class="btn-glowup btn-danger">Logout</a>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 admin Dashboard. All rights reserved.</p>
    </div>
    </div>

    <script>
        // Global variables for confirmation
        let pendingForm = null;
        let pendingEvent = null;

        // Show message function
        function showMessage(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;
            alertDiv.style.display = 'block';

            alertContainer.appendChild(alertDiv);

            // Auto remove after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Tab functionality
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));

            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked tab
            event.target.classList.add('active');
        }

        // Edit Product Modal Functions
        function editProduct(id, name, description, price, stock, category) {
            document.getElementById('edit_product_id').value = id;
            document.getElementById('edit_product_name').value = name;
            document.getElementById('edit_product_description').value = description;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_stock').value = stock;
            document.getElementById('edit_category').value = category;

            document.getElementById('editProductModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editProductModal').style.display = 'none';
        }

        // Confirmation Modal Functions
        function showConfirmation(event, actionId, title, message) {
            event.preventDefault();
            pendingForm = event.target;
            pendingEvent = event;

            document.getElementById('confirmationTitle').textContent = title;
            document.getElementById('confirmationMessage').textContent = message;
            document.getElementById('confirmationModal').style.display = 'block';

            return false;
        }

        function showConfirmationForm(event, actionId, title, message, buttonClass) {
            event.preventDefault();
            pendingForm = event.target;
            pendingEvent = event;

            document.getElementById('confirmationTitle').textContent = title;
            document.getElementById('confirmationMessage').textContent = message;

            const confirmButton = document.getElementById('confirmationButton');
            confirmButton.className = buttonClass || 'btn';

            document.getElementById('confirmationModal').style.display = 'block';

            return false;
        }

        function closeConfirmationModal() {
            document.getElementById('confirmationModal').style.display = 'none';
            pendingForm = null;
            pendingEvent = null;
        }

        function proceedWithAction() {
            if (pendingForm) {
                pendingForm.submit();
            }
            closeConfirmationModal();
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const confirmModal = document.getElementById('confirmationModal');
            const editModal = document.getElementById('editProductModal');

            if (event.target === confirmModal) {
                closeConfirmationModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
        }

        // Show PHP messages as alerts
        <?php if ($message): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showMessage('<?php echo addslashes($message); ?>', '<?php echo $messageType; ?>');
            });
        <?php endif; ?>

        // Auto-refresh page every 5 minutes to update stock levels
        setTimeout(function() {
            location.reload();
        }, 300000); // 5 minutes
    </script>

</body>

</html>