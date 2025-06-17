<?php
include('db.php');
session_start();

$errors = array();
$success_message = '';

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $method = $_POST['method'];
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    
    // Credit card fields (if applicable)
    $cardNumber = isset($_POST['cardNumber']) ? trim($_POST['cardNumber']) : '';
    $cardName = isset($_POST['cardName']) ? trim($_POST['cardName']) : '';
    $cvv = isset($_POST['CVV']) ? trim($_POST['CVV']) : '';

    // Validation
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    } elseif (strlen($name) < 2) {
        $errors['name'] = "Name must be at least 2 characters long.";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($method)) {
        $errors['method'] = "Payment method is required.";
    } elseif ($method == 'credit_card') {
        // Validate credit card fields
        if (empty($cardNumber)) {
            $errors['cardNumber'] = "Credit card number is required.";
        } elseif (!preg_match('/^\d{16}$/', str_replace(' ', '', $cardNumber))) {
            $errors['cardNumber'] = "Please enter a valid 16-digit card number.";
        }
        
        if (empty($cardName)) {
            $errors['cardName'] = "Cardholder name is required.";
        }
        
        if (empty($cvv)) {
            $errors['CVV'] = "CVV is required.";
        } elseif (!preg_match('/^\d{3,4}$/', $cvv)) {
            $errors['CVV'] = "CVV must be 3 or 4 digits.";
        }
    }

    if (empty($address)) {
        $errors['address'] = "Address is required.";
    } elseif (strlen($address) < 10) {
        $errors['address'] = "Please provide a complete address.";
    }

    if (empty($phone)) {
        $errors['phone'] = "Phone number is required.";
    } elseif (!preg_match('/^[\d\s\+\-\(\)]{10,15}$/', $phone)) {
        $errors['phone'] = "Please enter a valid phone number.";
    }

    if (empty($errors)) {
        // Get user_id from session, default to NULL if not logged in
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
        
        // Generate unique order ID
        $order_id = 'GL-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Calculate total amount from cart/session
        $total_amount = isset($_SESSION['cart_total']) ? $_SESSION['cart_total'] : 99.99; // Default for demo
        
        // Set initial status based on payment method
        $status = ($method == 'cash') ? 'confirmed' : 'processing';
        
        // Generate tracking number for shipped orders
        $tracking_number = 'TRK' . strtoupper(substr(md5(uniqid()), 0, 8));
        
        // Set estimated delivery (7 days from now)
        $estimated_delivery = date('Y-m-d', strtotime('+7 days'));
        
        // Store guest email in session for order tracking
        if (!$user_id) {
            $_SESSION['guest_email'] = $email;
            $_SESSION['guest_order_tracking'] = true;
        }
        
        // Use prepared statements for better security
        $stmt = mysqli_prepare($conn, "INSERT INTO orders (order_id, user_id, name, email, phone, address, total_amount, payment_method, status, order_date, tracking_number, carrier, estimated_delivery) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 'DHL Express', ?)");
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sissssdssss", $order_id, $user_id, $name, $email, $phone, $address, $total_amount, $method, $status, $tracking_number, $estimated_delivery);
            
            if (mysqli_stmt_execute($stmt)) {
                $db_order_id = mysqli_insert_id($conn);
                
                // Insert order items from cart/session
                $cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
                
                // If no cart items, use sample items for demo
                if (empty($cart_items)) {
                    $cart_items = [
                        ['product_id' => 1, 'quantity' => 1, 'price' => 59.99, 'name' => 'Sample Product 1'],
                        ['product_id' => 2, 'quantity' => 1, 'price' => 39.99, 'name' => 'Sample Product 2']
                    ];
                }
                
                $item_stmt = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                
                if ($item_stmt) {
                    foreach ($cart_items as $item) {
                        $product_id = isset($item['product_id']) ? $item['product_id'] : 1;
                        $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
                        $price = isset($item['price']) ? $item['price'] : 0.00;
                        
                        mysqli_stmt_bind_param($item_stmt, "iiid", $db_order_id, $product_id, $quantity, $price);
                        mysqli_stmt_execute($item_stmt);
                    }
                    mysqli_stmt_close($item_stmt);
                }
                
                $username = $_SESSION['username'] ?? 'Guest';
                $success_message = "Order $order_id placed successfully! You will receive a confirmation email shortly.";
                
                // Store order information in session for immediate access
                $_SESSION['last_order_id'] = $order_id;
                $_SESSION['last_order_details'] = [
                    'order_id' => $order_id,
                    'name' => $name,
                    'email' => $email,
                    'total_amount' => $total_amount,
                    'status' => $status,
                    'order_date' => date('Y-m-d H:i:s'),
                    'user_id' => $user_id
                ];
                
                // Clear cart after successful order
                unset($_SESSION['cart']);
                unset($_SESSION['cart_total']);
                
                // Clear form data after successful submission
                $name = $email = $method = $address = $phone = '';
                $cardNumber = $cardName = $cvv = '';
                
                // Redirect to order page with success message
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'order.php?success=1&order_id=" . urlencode($order_id) . "';
                    }, 3000);
                </script>";
                
            } else {
                $errors['general'] = "Failed to place order. Please try again. Error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $errors['general'] = "Database error. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - GlowUp</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css" href="Checkout.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <h4><em>GlowUp</em></h4>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="Women.php">Women</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Men.php">Men</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Wishlist.php" style="color: #b07154; font-weight: bold;">
                                <i class='bx bx-heart'></i> Wishlist
                            </a>
                        </li>
                           <li class="nav-item">
                            <a class="nav-link" href="ShoppingBag.php">
                            <i class="fa-solid fa-bag-shopping style="color: #969696;"></i>
                               Shopping Bag
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Account.php">
                            <i class="fa-solid fa-user" style="color: #969696;"></i>
                                Account
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="from-container">
        <section class="checkout">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <h2><i class="fas fa-shopping-cart me-2"></i>Secure Checkout</h2>
                  
                    
                    <!-- Success Message -->
                    <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="animation: bounceIn 0.6s ease;">
                        <i class="fas fa-check-circle me-2"></i>
                        <span><?php echo htmlspecialchars($success_message); ?></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <div class="mt-2">
                            <small class="text-success">
                                <i class="fas fa-info-circle me-1"></i>
                                Redirecting to order history in 3 seconds...
                            </small>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- General Error Message -->
                    <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span><?php echo htmlspecialchars($errors['general']); ?></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" onsubmit="return validateForm()" novalidate id="checkoutForm">
                        <div class="field-group">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                   id="name" name="name" 
                                   value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
                                   placeholder="Enter your full name" required autocomplete="name" onkeyup="updateProgress()">
                            <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['name']); ?></div>
                            <?php else: ?>
                            <div class="invalid-feedback" id="nameError"></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="field-group">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                   id="email" name="email" 
                                   value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                                   placeholder="Enter your email address" required autocomplete="email" onkeyup="updateProgress()">
                            <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                            <?php else: ?>
                            <div class="invalid-feedback" id="emailError"></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="field-group">
                            <label for="method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            
                            <!-- Custom Payment Options -->
                            <div class="payment-options mb-3">
                                <div class="payment-option ripple <?php echo (isset($method) && $method == 'credit_card') ? 'selected' : ''; ?>" onclick="selectPayment('credit_card')" id="payment-credit_card">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-credit-card fa-2x me-3" style="color: #b07154;"></i>
                                        <div>
                                            <strong>Credit Card</strong>
                                            <div class="text-muted small">Visa, Mastercard, American Express</div>
                                        </div>
                                        <i class="fas fa-check-circle ms-auto <?php echo (isset($method) && $method == 'credit_card') ? '' : 'd-none'; ?>" id="check-credit_card"></i>
                                    </div>
                                </div>
                                
                                <div class="payment-option ripple <?php echo (isset($method) && $method == 'Apple pay') ? 'selected' : ''; ?>" onclick="selectPayment('Apple pay')" id="payment-Apple pay">
                                    <div class="d-flex align-items-center">
                                        <i class="fab fa-apple-pay fa-2x me-3" style="color: #b07154;"></i>
                                        <div>
                                            <strong>Apple Pay</strong>
                                            <div class="text-muted small">Quick and secure payment</div>
                                        </div>
                                        <i class="fas fa-check-circle ms-auto <?php echo (isset($method) && $method == 'Apple pay') ? '' : 'd-none'; ?>" id="check-Apple pay"></i>
                                    </div>
                                </div>
                                
                                <div class="payment-option ripple <?php echo (isset($method) && $method == 'cash') ? 'selected' : ''; ?>" onclick="selectPayment('cash')" id="payment-cash">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-money-bill fa-2x me-3" style="color: #b07154;"></i>
                                        <div>
                                            <strong>Cash on Delivery</strong>
                                            <div class="text-muted small">Pay when you receive your order</div>
                                        </div>
                                        <i class="fas fa-check-circle ms-auto <?php echo (isset($method) && $method == 'cash') ? '' : 'd-none'; ?>" id="check-cash"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <select class="form-select d-none <?php echo isset($errors['method']) ? 'is-invalid' : ''; ?>" 
                                    id="method" name="method" required>
                                <option value="">Select a payment method</option>
                                <option value="credit_card" <?php echo (isset($method) && $method == 'credit_card') ? 'selected' : ''; ?>>Credit Card</option>
                                <option value="Apple pay" <?php echo (isset($method) && $method == 'Apple pay') ? 'selected' : ''; ?>>Apple Pay</option>
                                <option value="cash" <?php echo (isset($method) && $method == 'cash') ? 'selected' : ''; ?>>Cash on Delivery</option>
                            </select>
                            <?php if (isset($errors['method'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['method']); ?></div>
                            <?php else: ?>
                            <div class="invalid-feedback" id="methodError"></div>
                            <?php endif; ?>
                        </div>
                        
                        <div id="creditCardFields" style="display: <?php echo (isset($method) && $method == 'credit_card') ? 'block' : 'none'; ?>;">
                            <div class="card mb-3" style="background-color: #f8f9fa;">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fas fa-credit-card me-2"></i>Credit Card Information</h6>
                                    
                                    <div class="mb-3">
                                        <label for="cardNumber" class="form-label">Card Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?php echo isset($errors['cardNumber']) ? 'is-invalid' : ''; ?>" 
                                               id="cardNumber" name="cardNumber" 
                                               value="<?php echo isset($cardNumber) ? htmlspecialchars($cardNumber) : ''; ?>"
                                               placeholder="1234 5678 9012 3456" 
                                               maxlength="19" oninput="formatCardNumber(this)">
                                        <?php if (isset($errors['cardNumber'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['cardNumber']); ?></div>
                                        <?php else: ?>
                                        <div class="invalid-feedback" id="cardNumberError"></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="cardName" class="form-label">Cardholder Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?php echo isset($errors['cardName']) ? 'is-invalid' : ''; ?>" 
                                               id="cardName" name="cardName" 
                                               value="<?php echo isset($cardName) ? htmlspecialchars($cardName) : ''; ?>"
                                               placeholder="Name as it appears on card" 
                                               style="text-transform: uppercase;">
                                        <?php if (isset($errors['cardName'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['cardName']); ?></div>
                                        <?php else: ?>
                                        <div class="invalid-feedback" id="cardNameError"></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="expiryDate" class="form-label">Expiry Date</label>
                                            <input type="text" class="form-control" id="expiryDate" name="expiryDate" 
                                                   placeholder="MM/YY" maxlength="5" oninput="formatExpiryDate(this)">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="CVV" class="form-label">CVV <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?php echo isset($errors['CVV']) ? 'is-invalid' : ''; ?>" 
                                                   id="CVV" name="CVV" 
                                                   value="<?php echo isset($cvv) ? htmlspecialchars($cvv) : ''; ?>"
                                                   placeholder="123" maxlength="4" 
                                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            <?php if (isset($errors['CVV'])): ?>
                                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['CVV']); ?></div>
                                            <?php else: ?>
                                            <div class="invalid-feedback" id="CVVError"></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="applePayFields" style="display: <?php echo (isset($method) && $method == 'Apple pay') ? 'block' : 'none'; ?>;">
                            <div class="alert alert-info">
                                <i class="fab fa-apple-pay me-2"></i>
                                You will be redirected to Apple Pay to complete your payment securely.
                            </div>
                        </div>
                        
                        <div id="cashFields" style="display: <?php echo (isset($method) && $method == 'cash') ? 'block' : 'none'; ?>;">
                            <div class="alert alert-warning">
                                <i class="fas fa-money-bill me-2"></i>
                                <strong>Cash on Delivery:</strong> You will pay when your order is delivered to your address.
                                Please have the exact amount ready.
                            </div>
                        </div>
                        
                        <div class="field-group">
                            <label for="address" class="form-label">Delivery Address <span class="text-danger">*</span></label>
                            <textarea class="form-control <?php echo isset($errors['address']) ? 'is-invalid' : ''; ?>" 
                                      id="address" name="address" rows="3" 
                                      placeholder="Enter your complete delivery address including street, city, and postal code" 
                                      required autocomplete="street-address" onkeyup="updateProgress()"><?php echo isset($address) ? htmlspecialchars($address) : ''; ?></textarea>
                            <?php if (isset($errors['address'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['address']); ?></div>
                            <?php else: ?>
                            <div class="invalid-feedback" id="addressError"></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="field-group">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                                   id="phone" name="phone" 
                                   value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>"
                                   placeholder="Enter your phone number" required autocomplete="tel" onkeyup="updateProgress()">
                            <div class="form-text">📱 We'll use this number to contact you about your delivery.</div>
                            <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['phone']); ?></div>
                            <?php else: ?>
                            <div class="invalid-feedback" id="phoneError"></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-grid gap-2 field-group">
                            <button type="submit" name="submit" class="button ripple" id="submitBtn" style="font-size: 1.1rem; padding: 15px;">
                                <i class="fas fa-lock me-2"></i>
                                <span id="btnText">Confirm Order</span>
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Your information is encrypted and secure
                                </small>
                            </div>
                        </div>
                        
                        <div class="alert alert-danger mt-3 d-none" role="alert" id="generalError">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span id="generalErrorMessage"></span>
                        </div>
                    </form>
                </div>
            </div>
        </section>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="Checkout.js"></script>
</body>
</html>