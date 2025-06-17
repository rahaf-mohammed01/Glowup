<?php
// Add output buffering to prevent header issues
ob_start();

// Start session with enhanced security (but simplified)
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', 3600); // 1 hour
    ini_set('session.gc_maxlifetime', 3600);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1); // Enable for debugging - change to 0 in production

// Include the database connection file
include('db.php');

// Check if user is already logged in - MOVED TO TOP
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // Redirect based on role
    switch($_SESSION['role']) {
        case 'admin':
            header("Location: admin.php");
            break;
        case 'supplier':
            header("Location: supplier.php");
            break;
        case 'customer':
        default:
            header("Location: home.php");
            break;
    }
    exit();
}

// Initialize variables for username, email, and password - ALWAYS INITIALIZE
$username = $email = $password = '';
$signin_email = $signin_password = '';

// Initialize an array to store validation errors - ENSURE THIS IS ALWAYS SET
$errors = array(
    'username' => '', 
    'email' => '', 
    'password' => '', 
    'signin_email' => '', 
    'signin_password' => ''
);

// Success and error messages - ENSURE THESE ARE ALWAYS SET
$success_message = '';
$error_message = '';

// Sign up (customers only - admins and suppliers should be created by admin)
if (isset($_POST['signup'])) {
    // Reset errors for signup
    $errors['username'] = $errors['email'] = $errors['password'] = '';
    
    // Validation for username
    if (empty($_POST['username'])) {
        $errors['username'] = 'A username is required <br />';
    } else {
        $username = $_POST['username'];
        if (!preg_match('/^[a-zA-Z0-9\s]+$/', $username)) {
            $errors['username'] = 'Must be more than one letter/number';
        }
    }

    // Validation for email
    if (empty($_POST['email'])) {
        $errors['email'] = 'An email is required <br />';
    } else {
        $email = $_POST['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Must be a valid email';
        }
    }

    // Validation for password
    if (empty($_POST['password'])) {
        $errors['password'] = 'A password is required <br />';
    } else {
        $password = $_POST['password'];
        // Password conditions
        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors['password'] = 'Password must contain at least one uppercase letter';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $errors['password'] = 'Password must contain at least one lowercase letter';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors['password'] = 'Password must contain at least one digit';
        }
    }

    // Check if there are any signup errors
    if (!empty($errors['username']) || !empty($errors['email']) || !empty($errors['password'])) {
        $error_message = 'There is something wrong';
    } else {
        // Check if user already exists in the database
        $query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error_message = 'User already exists. Please use a different username or email.';
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user with hashed password (default role is customer)
                $query = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'customer')";
                $stmt->close();
                $stmt = $conn->prepare($query);
                
                if ($stmt) {
                    $stmt->bind_param("sss", $username, $email, $hashedPassword);
                    
                    if ($stmt->execute()) {
                        $user_id = $conn->insert_id;  // get the user id
                        // Sign up successful, redirect to home page
                        echo 'Sign up successful! Redirecting to home page...';
                        $_SESSION['username'] = $username;
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['email'] = $email;
                        $_SESSION['role'] = 'customer';
                        // Set the showWelcomeMessage session variable
                        $_SESSION['showWelcomeMessage'] = true;
                        $_SESSION['logged_in'] = true;
                        
                        // Regenerate session ID for security
                        session_regenerate_id(true);
                        
                        // Debug logging
                        error_log("Signup successful for user: " . $username);
                        
                        // Clear output buffer and redirect
                        ob_clean();
                        header('location: home.php'); // Customers go to home page
                        exit();
                    } else {
                        $error_message = 'Error creating account. Please try again.';
                        error_log("Database insert error: " . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    $error_message = 'Database error. Please try again.';
                    error_log("Prepare statement error: " . $conn->error);
                }
            }
        } else {
            $error_message = 'Database error. Please try again.';
            error_log("Prepare statement error: " . $conn->error);
        }
    }
}

// Sign in (for all user types)
if (isset($_POST['signin'])) {
    // Reset signin errors
    $errors['signin_email'] = $errors['signin_password'] = '';
    
    // Validation for signin email
    if (empty($_POST['signin_email'])) {
        $errors['signin_email'] = 'Email is required';
    } else {
        $signin_email = $_POST['signin_email'];
        if (!filter_var($signin_email, FILTER_VALIDATE_EMAIL)) {
            $errors['signin_email'] = 'Must be a valid email';
        }
    }

    // Validation for signin password
    if (empty($_POST['signin_password'])) {
        $errors['signin_password'] = 'Password is required';
    } else {
        $signin_password = $_POST['signin_password'];
    }

    // If no validation errors, proceed with authentication
    if (empty($errors['signin_email']) && empty($errors['signin_password'])) {
       $query = "SELECT user_id, username, email, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("s", $signin_email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($signin_password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];  
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    // Set the showWelcomeMessage 
                    $_SESSION['showWelcomeMessage'] = true;
                    $_SESSION['logged_in'] = true;
                    
                    // Regenerate session ID for security
                    session_regenerate_id(true);
                    
                    // Debug logging
                    error_log("Login successful for user: " . $user['username'] . " with role: " . $user['role']);
                    
                    // Clear output buffer and redirect based on role
                    ob_clean();
                    
                    // Role-based redirection
                    switch($user['role']) {
                        case 'admin':
                            header('location: admin.php');
                            break;
                        case 'supplier':
                            header('location: supplier.php');
                            break;
                        case 'customer':
                        default:
                            header('location: home.php');
                            break;
                    }
                    exit();
                } else {
                    $errors['signin_password'] = 'Incorrect password. Please try again.';
                }
            } else {
                $errors['signin_email'] = 'User not found. Please sign up.';
            }
            $stmt->close();
        } else {
            $error_message = 'Database error. Please try again.';
            error_log("Prepare statement error: " . $conn->error);
        }
    }
}

// Ensure variables are always set before HTML output
$username = $username ?? '';
$email = $email ?? '';
$password = $password ?? '';
$signin_email = $signin_email ?? '';
$signin_password = $signin_password ?? '';
$success_message = $success_message ?? '';
$error_message = $error_message ?? '';

// Ensure errors array is always properly initialized
if (!isset($errors) || !is_array($errors)) {
    $errors = array(
        'username' => '', 
        'email' => '', 
        'password' => '', 
        'signin_email' => '', 
        'signin_password' => ''
    );
}
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    echo "<div style='background: yellow; padding: 10px; margin: 10px;'>";
    echo "<strong>DEBUG INFORMATION:</strong><br>";
    echo "Session Status: " . session_status() . "<br>";
    echo "Session ID: " . session_id() . "<br>";
    echo "Logged In: " . (isset($_SESSION['logged_in']) ? ($_SESSION['logged_in'] ? 'YES' : 'NO') : 'NOT SET') . "<br>";
    echo "Username: " . ($_SESSION['username'] ?? 'NOT SET') . "<br>";
    echo "Email: " . ($_SESSION['email'] ?? 'NOT SET') . "<br>";
    echo "Role: " . ($_SESSION['role'] ?? 'NOT SET') . "<br>";
    echo "All Session Data: ";
    print_r($_SESSION);
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="path/to/bootstrap/css/bootstrap.min.css">
    <script src="path/to/bootstrap/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="auth-enhancements.css">
    <script src="login.js" type="text/javascript"></script>
    <title>GlowUp - Your Fashion Destination</title>
</head>

<body>
    <!-- Display success or error messages -->
    <?php if (!empty($success_message)): ?>
        <div class="success-message" style="background-color: #d4edda; color: #155724; padding: 10px; margin: 10px; border-radius: 5px;">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="error-message" style="background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border-radius: 5px;">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <!-- Container for the sign-up and sign-in forms -->
    <div class="container" id="container">
        <!-- Sign-up form (Only for customers) -->
        <div class="form-container sign-up-container">
            <form action="" method="POST" id="signupForm" name="signupForm">
                <h1>Create Account</h1>
                <!-- Social icons for alternative sign-up -->
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fab fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" class="icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="icon"><i class="fab fa-twitter"></i></a>
                </div>
                <!-- Form inputs for username, email, password -->
                <span>or use your email for registration</span>
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username) ?>">
                <div class="red-text"><?php echo isset($errors['username']) ? $errors['username'] : ''; ?></div>
                
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email) ?>">
                <div class="red-text"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></div>

                <!-- Sign-up form password section with toggle -->
                <div class="input-group password-container">
                    <input type="password" name="password" id="signup_password" placeholder="Password"
                        autocomplete="new-password" required
                        aria-describedby="password-error password-strength" value="<?php echo htmlspecialchars($password) ?>">
                    <span class="toggle-password" onclick="togglePassword('signup_password', this)" title="Show/Hide Password">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="red-text" id="password-error"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?></div>
                <div class="password-strength" id="password-strength"></div>

                <small style="color: #666; font-size: 8px; margin-top: 10px; display: block;">
                    Customer registration only. Admin/Supplier accounts are created by administrators.
                </small>

                <!-- Submit button for sign-up -->
                <button type="submit" name="signup" id="signup-btn">Sign Up</button>
            </form>
        </div>

        <!-- Sign-in form (For all user types) -->
        <div class="form-container sign-in-container">
            <form action="" method="POST" id="signinForm" name="signinForm">
                <h1>Sign In</h1>
                <!-- Social icons for alternative sign-in -->
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fab fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" class="icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="icon"><i class="fab fa-twitter"></i></a>
                </div>
                <!-- Form inputs for email, password -->
                <span>Customer, Admin, or Supplier Login</span>
                <input type="email" name="signin_email" placeholder="Email" value="<?php echo htmlspecialchars($signin_email) ?>">
                <div class="red-text"><?php echo isset($errors['signin_email']) ? $errors['signin_email'] : ''; ?></div>

                <!-- Sign-in form password section with toggle -->
                <div class="input-group password-container">
                    <input type="password" name="signin_password" id="signin_password" placeholder="Password"
                        autocomplete="current-password" required
                        aria-describedby="signin-password-error">
                    <span class="toggle-password" onclick="togglePassword('signin_password', this)" title="Show/Hide Password">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="red-text" id="signin-password-error"><?php echo isset($errors['signin_password']) ? $errors['signin_password'] : ''; ?></div>

                <!-- Forgot password link -->
                <a href="reset password.html" class="mainbnr_wrap">
                    <span class="button-border">Forgot Your Password?</span>
                </a>
                <!-- Submit button for sign-in -->
                <button type="submit" name="signin" id="signin-btn">Sign In</button>
            </form>
        </div>

        <!-- Overlay container for switching between sign-up and sign-in -->
        <div class="overlay-container">
            <div class="overlay">
                <!-- Left panel for sign-in -->
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back to GlowUp!</h1>
                    <p>Enter with your email and password to sign in</p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <!-- Right panel for sign-up -->
                <div class="overlay-panel overlay-right">
                    <h1>Hello in GlowUp</h1>
                    <p>Enter your email and password to register as customer</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script src="auth-enhancements.js"></script>
</body>

</html>
<?php
// End output buffering and send output
ob_end_flush();
?>