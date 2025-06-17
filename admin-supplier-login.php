<?php
// Add output buffering to prevent header issues
ob_start();

// Start session with enhanced security
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
// Fix the include path - it should be auth_middleware.php not athu_middleware.php
include('auth_middleware.php');

// Check if user is already logged in
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

// Initialize variables
$admin_username = '';
$admin_password = '';
$supplier_username = '';
$supplier_password = '';

// Initialize error messages
$admin_error = '';
$supplier_error = '';
$success_message = '';

// Admin Login Handler
if (isset($_POST['admin_login'])) {
    $admin_username = trim($_POST['admin_username'] ?? '');
    $admin_password = $_POST['admin_password'] ?? '';
    
    if (empty($admin_username)) {
        $admin_error = 'Username is required';
    } elseif (empty($admin_password)) {
        $admin_error = 'Password is required';
    } else {
        // Query database for admin user
        $query = "SELECT user_id, username, email, password, role FROM users WHERE username = ? AND role = 'admin'";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("s", $admin_username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($admin_password, $user['password'])) {
                    // Set session variables - FIXED: use user_id instead of id
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['logged_in'] = true;
                    $_SESSION['showWelcomeMessage'] = true;
                    
                    // Regenerate session ID for security
                    session_regenerate_id(true);
                    
                    // Debug logging
                    error_log("Admin login successful for user: " . $user['username']);
                    
                    // Clear output buffer and redirect
                    ob_clean();
                    header('Location: admin.php');
                    exit();
                } else {
                    $admin_error = 'Invalid username or password';
                }
            } else {
                $admin_error = 'Invalid admin credentials';
            }
            $stmt->close();
        } else {
            $admin_error = 'Database error. Please try again.';
            error_log("Prepare statement error: " . $conn->error);
        }
    }
}

// Supplier Login Handler
if (isset($_POST['supplier_login'])) {
    $supplier_username = trim($_POST['supplier_username'] ?? '');
    $supplier_password = $_POST['supplier_password'] ?? '';
    
    if (empty($supplier_username)) {
        $supplier_error = 'Username is required';
    } elseif (empty($supplier_password)) {
        $supplier_error = 'Password is required';
    } else {
        // Query database for supplier user
        $query = "SELECT user_id, username, email, password, role FROM users WHERE username = ? AND role = 'supplier'";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("s", $supplier_username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($supplier_password, $user['password'])) {
                    // Set session variables - FIXED: use user_id instead of id
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['logged_in'] = true;
                    $_SESSION['showWelcomeMessage'] = true;
                    
                    // Regenerate session ID for security
                    session_regenerate_id(true);
                    
                    // Debug logging
                    error_log("Supplier login successful for user: " . $user['username']);
                    
                    // Clear output buffer and redirect
                    ob_clean();
                    header('Location: supplier.php');
                    exit();
                } else {
                    $supplier_error = 'Invalid username or password';
                }
            } else {
                $supplier_error = 'Invalid supplier credentials';
            }
            $stmt->close();
        } else {
            $supplier_error = 'Database error. Please try again.';
            error_log("Prepare statement error: " . $conn->error);
        }
    }
}

// Debug information (remove in production)
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    echo "<div style='background: yellow; padding: 10px; margin: 10px;'>";
    echo "<strong>DEBUG INFORMATION:</strong><br>";
    echo "Session Status: " . session_status() . "<br>";
    echo "Session ID: " . session_id() . "<br>";
    echo "Logged In: " . (isset($_SESSION['logged_in']) ? ($_SESSION['logged_in'] ? 'YES' : 'NO') : 'NOT SET') . "<br>";
    echo "Username: " . ($_SESSION['username'] ?? 'NOT SET') . "<br>";
    echo "Email: " . ($_SESSION['email'] ?? 'NOT SET') . "<br>";
    echo "Role: " . ($_SESSION['role'] ?? 'NOT SET') . "<br>";
    echo "User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "<br>";
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
    <title>GlowUp - Admin & Supplier Access</title>
    <style>
        /* Import Montserrat font from Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');

        /* CSS Variables for consistency */
        :root {
            --bg-primary: #f8f9fa;
            --bg-secondary: #e9ecef;
            --text-primary: #333;
            --text-secondary: #666;
            --primary-color: #C5AB96;
            --border-color: #dee2e6;
            --admin-color: #b4977f6e;
            --supplier-color: #875c4584;
            --error-color: #d32f2f;
            --success-color: #2e7d32;
        }

        /* Reset default styles and set font-family to Montserrat */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        /* Styling for the body */
        body {
            background-color: #b4977f60;
            background: linear-gradient(to right, #b4977f6e, #0000002e);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-height: 100vh;
            padding: 20px;
        }

        /* Header with logo and title */
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(45deg, var(--admin-color), var(--supplier-color));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header p {
            font-size: 1.1rem;
            color: var(--text-secondary);
            font-weight: 400;
        }

        /* Customer login link */
        .customer-link {
            text-align: center;
            margin-bottom: 20px;
        }

        .customer-link a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 1rem;
            padding: 10px 20px;
            border: 2px solid var(--border-color);
            border-radius: 25px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .customer-link a:hover {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background-color: rgba(197, 171, 150, 0.1);
        }

        /* Main container */
        .container {
            background-color: #fff;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
            position: relative;
            overflow: hidden;
            width: 768px;
            max-width: 100%;
            min-height: 500px;
        }

        /* Role selection tabs */
        .role-tabs {
            display: flex;
            background: var(--bg-primary);
            border-radius: 30px 30px 0 0;
        }

        .role-tab {
            flex: 1;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            border-radius: 30px 30px 0 0;
            position: relative;
        }

        .role-tab.admin {
            color: var(--admin-color);
            border-bottom: 3px solid transparent;
        }

        .role-tab.supplier {
            color: var(--supplier-color);
            border-bottom: 3px solid transparent;
        }

        .role-tab.active.admin {
            background: white;
            border-bottom: 3px solid var(--admin-color);
            color: var(--admin-color);
        }

        .role-tab.active.supplier {
            background: white;
            border-bottom: 3px solid var(--supplier-color);
            color: var(--supplier-color);
        }

        .role-tab i {
            margin-right: 8px;
            font-size: 1.2rem;
        }

        /* Form container */
        .form-container {
            padding: 40px;
            transition: all 0.3s ease;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .form-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 10px;
            text-align: center;
        }

        .form-title.admin {
            color: var(--admin-color);
        }

        .form-title.supplier {
            color: var(--supplier-color);
        }

        .form-subtitle {
            font-size: 1rem;
            color: var(--text-secondary);
            margin-bottom: 30px;
            text-align: center;
        }

        /* Input styling */
        .input-group {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }

        .input-group input {
            width: 100%;
            padding: 15px 20px;
            padding-left: 50px;
            font-size: 1rem;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            outline: none;
            transition: all 0.3s ease;
            background-color: #fafafa;
        }

        .input-group input:focus {
            border-color: var(--primary-color);
            background-color: white;
            box-shadow: 0 0 0 3px rgba(180, 151, 112, 0.1);
        }

        .input-group.admin input:focus {
            border-color: var(--admin-color);
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
        }

        .input-group.supplier input:focus {
            border-color: var(--supplier-color);
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.1rem;
            color: var(--text-secondary);
        }

        .input-group.admin .input-icon {
            color: var(--admin-color);
        }

        .input-group.supplier .input-icon {
            color: var(--supplier-color);
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--text-primary);
        }

        /* Login button */
        .login-btn {
            width: 100%;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            color: white;
        }

        .login-btn.admin {
            background: linear-gradient(135deg, var(--admin-color), rgb(0, 0, 0));
        }

        .login-btn.supplier {
            background: linear-gradient(135deg, var(--supplier-color), rgb(0, 0, 0));
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgb(0, 0, 0);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        /* Error message */
        .error-message {
            background-color: #ffe6e6;
            color: var(--error-color);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9rem;
            border: 1px solid #ffcdd2;
            width: 100%;
        }

        /* Success message */
        .success-message {
            background-color: #e8f5e8;
            color: var(--success-color);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9rem;
            border: 1px solid #c8e6c9;
            width: 100%;
        }

        /* Forgot password link */
        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--primary-color);
        }

        /* Loading state */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .loading .login-btn {
            position: relative;
        }

        .loading .login-btn::after {
            content: '';
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 2px solid white;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: translateY(-50%) rotate(0deg);
            }

            100% {
                transform: translateY(-50%) rotate(360deg);
            }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                width: 100%;
                margin: 0 10px;
            }

            .form-container {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .role-tab {
                padding: 15px 10px;
                font-size: 1rem;
            }
        }

        /* Hidden form state */
        .form-hidden {
            display: none;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>GlowUp</h1>
        <p>Admin & Supplier Access Portal</p>
    </div>

    <!-- Link to customer login -->
    <div class="customer-link">
        <a href="login.php">
            <i class="fas fa-user"></i> Customer Login
        </a>
    </div>

    <div class="container">
        <!-- Role Selection Tabs -->
        <div class="role-tabs">
            <div class="role-tab admin <?php echo (!isset($_POST['supplier_login']) || isset($_POST['admin_login'])) ? 'active' : ''; ?>" onclick="switchRole('admin')">
                <i class="fas fa-user-shield"></i>
                Admin Login
            </div>
            <div class="role-tab supplier <?php echo isset($_POST['supplier_login']) ? 'active' : ''; ?>" onclick="switchRole('supplier')">
                <i class="fas fa-truck"></i>
                Supplier Login
            </div>
        </div>

        <!-- Form Container -->
        <div class="form-container">
            <!-- Success Message -->
            <?php if (!empty($success_message)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <!-- Admin Login Form -->
            <form class="login-form <?php echo isset($_POST['supplier_login']) && !isset($_POST['admin_login']) ? 'form-hidden' : ''; ?>" id="admin-form" method="POST" action="">
                <h2 class="form-title admin">Admin Access</h2>
                <p class="form-subtitle">Welcome back, Administrator</p>

                <?php if (!empty($admin_error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($admin_error); ?></div>
                <?php endif; ?>

                <div class="input-group admin">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="admin_username" placeholder="Admin Username" 
                           value="<?php echo htmlspecialchars($admin_username); ?>" required>
                </div>

                <div class="input-group admin">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="admin_password" id="admin-password" 
                           placeholder="Admin Password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('admin-password', this)"></i>
                </div>

                <button type="submit" name="admin_login" class="login-btn admin">
                    <i class="fas fa-sign-in-alt"></i> Login as Admin
                </button>

                <div class="forgot-password">
                    <a href="#" onclick="alert('Please contact system administrator for password reset.')">
                        Forgot your password?
                    </a>
                </div>
            </form>

            <!-- Supplier Login Form -->
            <form class="login-form <?php echo (!isset($_POST['supplier_login']) && !isset($_POST['admin_login'])) || isset($_POST['admin_login']) ? 'form-hidden' : ''; ?>" id="supplier-form" method="POST" action="">
                <h2 class="form-title supplier">Supplier Access</h2>
                <p class="form-subtitle">Welcome back, Supplier Partner</p>

                <?php if (!empty($supplier_error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($supplier_error); ?></div>
                <?php endif; ?>

                <div class="input-group supplier">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="supplier_username" placeholder="Supplier Username" 
                           value="<?php echo htmlspecialchars($supplier_username); ?>" required>
                </div>

                <div class="input-group supplier">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="supplier_password" id="supplier-password" 
                           placeholder="Supplier Password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('supplier-password', this)"></i>
                </div>

                <button type="submit" name="supplier_login" class="login-btn supplier">
                    <i class="fas fa-truck"></i> Login as Supplier
                </button>

                <div class="forgot-password">
                    <a href="#" onclick="alert('Please contact admin for password reset assistance.')">
                        Forgot your password?
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Function to switch between admin and supplier login forms
        function switchRole(role) {
            const adminTab = document.querySelector('.role-tab.admin');
            const supplierTab = document.querySelector('.role-tab.supplier');
            const adminForm = document.getElementById('admin-form');
            const supplierForm = document.getElementById('supplier-form');

            // Remove active class from all tabs
            adminTab.classList.remove('active');
            supplierTab.classList.remove('active');

            // Hide all forms
            adminForm.classList.add('form-hidden');
            supplierForm.classList.add('form-hidden');

            // Show selected form and activate tab
            if (role === 'admin') {
                adminTab.classList.add('active');
                adminForm.classList.remove('form-hidden');
            } else if (role === 'supplier') {
                supplierTab.classList.add('active');
                supplierForm.classList.remove('form-hidden');
            }

            // Clear any error messages when switching
            const errorMessages = document.querySelectorAll('.error-message');
            errorMessages.forEach(message => {
                message.style.display = 'none';
            });
        }

        // Function to toggle password visibility
        function togglePassword(inputId, toggleIcon) {
            const passwordInput = document.getElementById(inputId);
            const isPassword = passwordInput.type === 'password';
            
            passwordInput.type = isPassword ? 'text' : 'password';
            toggleIcon.classList.toggle('fa-eye');
            toggleIcon.classList.toggle('fa-eye-slash');
        }

        // Add loading state to forms on submit
        document.querySelectorAll('.login-form').forEach(form => {
            form.addEventListener('submit', function() {
                const button = this.querySelector('.login-btn');
                const container = this.closest('.form-container');
                
                // Add loading state
                container.classList.add('loading');
                button.disabled = true;
                
                // Remove loading state after 10 seconds (fallback)
                setTimeout(() => {
                    container.classList.remove('loading');
                    button.disabled = false;
                }, 10000);
            });
        });

        // Enhanced form validation
        function validateForm(formType) {
            let isValid = true;
            const form = document.getElementById(formType + '-form');
            const inputs = form.querySelectorAll('input[required]');
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.style.borderColor = '#d32f2f';
                    isValid = false;
                } else {
                    input.style.borderColor = '';
                }
            });
            
            return isValid;
        }

        // Add input event listeners for real-time validation
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                if (this.hasAttribute('required') && this.value.trim()) {
                    this.style.borderColor = '';
                }
            });
            
            input.addEventListener('focus', function() {
                this.style.borderColor = '';
            });
        });

        // Auto-hide messages after 5 seconds
        setTimeout(() => {
            const messages = document.querySelectorAll('.error-message, .success-message');
            messages.forEach(message => {
                message.style.transition = 'opacity 0.5s ease';
                message.style.opacity = '0';
                setTimeout(() => {
                    message.style.display = 'none';
                }, 500);
            });
        }, 5000);

        // Keyboard navigation support
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                // Allow default tab behavior
                return;
            }
            
            if (e.key === 'Enter') {
                const activeElement = document.activeElement;
                if (activeElement.tagName === 'INPUT') {
                    const form = activeElement.closest('form');
                    if (form && !form.classList.contains('form-hidden')) {
                        const button = form.querySelector('.login-btn');
                        if (button) {
                            button.click();
                        }
                    }
                }
            }
        });

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Set focus to first visible input
            const visibleForm = document.querySelector('.login-form:not(.form-hidden)');
            if (visibleForm) {
                const firstInput = visibleForm.querySelector('input');
                if (firstInput) {
                    firstInput.focus();
                }
            }
        });
    </script>
</body>
</html>