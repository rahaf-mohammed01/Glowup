<?php
// Start session with better configuration
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', 3600);
    ini_set('session.gc_maxlifetime', 3600);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

include('auth_middleware.php');
requireCustomer();

// Include database connection
include('db.php');

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$error = '';
$userData = [];

// Get current user data ALWAYS from database
$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? null;

// Debug: Log session data
error_log("EditProfile.php - Session user_id: " . ($user_id ?? 'NULL'));
error_log("EditProfile.php - Session username: " . ($username ?? 'NULL'));

if ($user_id) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
} else if ($username) {
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
} else {
    // No valid session data, redirect to login
    session_destroy();
    header('Location: login.php');
    exit();
}

$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

if (!$userData) {
    // User not found, redirect to login
    session_destroy();
    header('Location: login.php');
    exit();
}

// Ensure we have the user ID for updates
$user_id = $userData['id'];
$_SESSION['user_id'] = $user_id; // Update session with correct user_id

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Debug: Log the POST data
        error_log("POST data received: " . print_r($_POST, true));

        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid security token. Please refresh the page and try again.');
        }

        // Get and sanitize form data - FIXED: Proper handling of empty strings vs NULL
        $username = !empty($_POST['username']) ? trim($_POST['username']) : '';
        $fullName = !empty($_POST['fullName']) ? trim($_POST['fullName']) : '';
        $email = !empty($_POST['email']) ? trim($_POST['email']) : '';
        $phone = !empty($_POST['phone']) ? trim($_POST['phone']) : null;
        $dateOfBirth = !empty($_POST['dateOfBirth']) ? $_POST['dateOfBirth'] : null;

        // FIXED: Gender handling - treat empty string as null but preserve selected values
        $gender = isset($_POST['gender']) && $_POST['gender'] !== '' ? $_POST['gender'] : null;

        $streetAddress = !empty($_POST['streetAddress']) ? trim($_POST['streetAddress']) : null;
        $city = !empty($_POST['city']) ? trim($_POST['city']) : null;
        $postalCode = !empty($_POST['postalCode']) ? trim($_POST['postalCode']) : null;
        $country = isset($_POST['country']) && $_POST['country'] !== '' ? $_POST['country'] : null;

        // Password fields
        $currentPassword = $_POST['currentPassword'] ?? '';
        $newPassword = $_POST['newPassword'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';

        // Debug: Log sanitized data including gender
        error_log("Sanitized data - Username: '$username', Email: '$email', Full Name: '$fullName'");
        error_log("Phone: " . ($phone ?? 'NULL') . ", DOB: " . ($dateOfBirth ?? 'NULL') . ", Gender: " . ($gender ?? 'NULL'));

        // Handle profile picture upload
        $profilePicturePath = $userData['profile_picture']; // Keep existing if no new upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/profiles/';

            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileInfo = pathinfo($_FILES['avatar']['name']);
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExtension = strtolower($fileInfo['extension']);

            if (in_array($fileExtension, $allowedTypes)) {
                // Check file size (max 5MB)
                if ($_FILES['avatar']['size'] <= 5 * 1024 * 1024) {
                    $newFileName = 'profile_' . $user_id . '_' . time() . '.' . $fileExtension;
                    $targetPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
                        // Delete old profile picture if exists
                        if (!empty($userData['profile_picture']) && file_exists($userData['profile_picture'])) {
                            unlink($userData['profile_picture']);
                        }
                        $profilePicturePath = $targetPath;
                        error_log("Profile picture uploaded successfully: " . $targetPath);
                    } else {
                        error_log("Failed to move uploaded file");
                        $error = "Failed to upload profile picture.";
                    }
                } else {
                    $error = "Profile picture must be less than 5MB.";
                }
            } else {
                $error = "Profile picture must be a JPG, JPEG, PNG, or GIF file.";
            }
        }

        // Validation
        $errors = [];

        if (empty($username)) {
            $errors[] = 'Username is required.';
        } elseif (strlen($username) < 3 || strlen($username) > 30) {
            $errors[] = 'Username must be between 3 and 30 characters.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username can only contain letters, numbers, and underscores.';
        } else {
            // Check if username is already taken by another user
            $usernameCheckQuery = "SELECT id FROM users WHERE username = ? AND id != ?";
            $usernameStmt = $conn->prepare($usernameCheckQuery);
            $usernameStmt->bind_param("si", $username, $user_id);
            $usernameStmt->execute();
            $usernameResult = $usernameStmt->get_result();
            if ($usernameResult->num_rows > 0) {
                $errors[] = 'This username is already taken.';
            }
            $usernameStmt->close();
        }

        if (empty($fullName)) {
            $errors[] = 'Full name is required.';
        } elseif (strlen($fullName) < 2 || strlen($fullName) > 100) {
            $errors[] = 'Full name must be between 2 and 100 characters.';
        }

        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } else {
            // Check if email is already taken by another user
            $emailCheckQuery = "SELECT id FROM users WHERE email = ? AND id != ?";
            $emailStmt = $conn->prepare($emailCheckQuery);
            $emailStmt->bind_param("si", $email, $user_id);
            $emailStmt->execute();
            $emailResult = $emailStmt->get_result();
            if ($emailResult->num_rows > 0) {
                $errors[] = 'This email address is already registered to another account.';
            }
            $emailStmt->close();
        }

        if (!empty($phone) && !preg_match('/^[\+]?[0-9\s\-\(\)]{10,20}$/', $phone)) {
            $errors[] = 'Please enter a valid phone number.';
        }

        if (!empty($dateOfBirth)) {
            $dob = DateTime::createFromFormat('Y-m-d', $dateOfBirth);
            if (!$dob || $dob->format('Y-m-d') !== $dateOfBirth) {
                $errors[] = 'Please enter a valid date of birth.';
            } else {
                $today = new DateTime();
                $age = $today->diff($dob)->y;
                if ($age < 13 || $age > 120) {
                    $errors[] = 'Age must be between 13 and 120 years.';
                }
            }
        }

        error_log("=== GENDER DEBUG START ===");
        error_log("POST data for gender: " . (isset($_POST['gender']) ? "'{$_POST['gender']}'" : 'NOT SET'));
        error_log("Current DB gender value: " . ($userData['gender'] ?? 'NULL'));
        error_log("All POST data: " . print_r($_POST, true));

        // FIXED: Gender handling - ALWAYS preserve existing value unless explicitly changed
        if (isset($_POST['gender'])) {
            // Gender field was submitted in the form
            if ($_POST['gender'] !== '') {
                // User selected a specific gender
                $gender = $_POST['gender'];
                error_log("Gender explicitly selected: '$gender'");
            } else {
                // User selected "Select Gender" (empty option) - preserve existing or set null
                $gender = $userData['gender']; // PRESERVE existing value instead of setting to null
                error_log("Gender empty selected, preserving existing: " . ($gender ?? 'NULL'));
            }
        } else {
            // Gender field not in POST (shouldn't happen with proper form)
            $gender = $userData['gender'];
            error_log("Gender not in POST, using existing: " . ($gender ?? 'NULL'));
        }

        error_log("Final gender value: " . ($gender ?? 'NULL'));
        error_log("=== GENDER DEBUG END ===");

        if (!empty($postalCode) && !preg_match('/^[A-Za-z0-9\s\-]{3,10}$/', $postalCode)) {
            $errors[] = 'Please enter a valid postal code.';
        }

        // Password validation
        $updatePassword = false;
        if (!empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword)) {
            if (empty($currentPassword)) {
                $errors[] = 'Current password is required to change password.';
            } else {
                // Verify current password
                $passwordQuery = "SELECT password FROM users WHERE id = ?";
                $passwordStmt = $conn->prepare($passwordQuery);
                $passwordStmt->bind_param("i", $user_id);
                $passwordStmt->execute();
                $passwordResult = $passwordStmt->get_result();
                $passwordData = $passwordResult->fetch_assoc();
                $passwordStmt->close();

                if (!password_verify($currentPassword, $passwordData['password'])) {
                    $errors[] = 'Current password is incorrect.';
                }
            }

            if (empty($newPassword)) {
                $errors[] = 'New password is required.';
            } elseif (strlen($newPassword) < 8) {
                $errors[] = 'New password must be at least 8 characters long.';
            }

            if ($newPassword !== $confirmPassword) {
                $errors[] = 'New password confirmation does not match.';
            }

            if (empty($errors)) {
                $updatePassword = true;
            }
        }

        if (!empty($errors)) {
            throw new Exception(implode('<br>', $errors));
        }

        // FIXED: Build the update query with profile picture included
        if ($updatePassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE users SET 
                            username = ?, 
                            full_name = ?, 
                            email = ?, 
                            phone = ?, 
                            date_of_birth = ?, 
                            gender = ?, 
                            street_address = ?, 
                            city = ?, 
                            postal_code = ?, 
                            country = ?,
                            profile_picture = ?,
                            password = ?,
                            updated_at = NOW()
                            WHERE id = ?";

            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param(
                "ssssssssssssi",
                $username,
                $fullName,
                $email,
                $phone,
                $dateOfBirth,
                $gender,
                $streetAddress,
                $city,
                $postalCode,
                $country,
                $profilePicturePath,
                $hashedPassword,
                $user_id
            );
        } else {
            $updateQuery = "UPDATE users SET 
                            username = ?, 
                            full_name = ?, 
                            email = ?, 
                            phone = ?, 
                            date_of_birth = ?, 
                            gender = ?, 
                            street_address = ?, 
                            city = ?, 
                            postal_code = ?, 
                            country = ?,
                            profile_picture = ?,
                            updated_at = NOW()
                            WHERE id = ?";

            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param(
                "sssssssssssi",
                $username,
                $fullName,
                $email,
                $phone,
                $dateOfBirth,
                $gender,
                $streetAddress,
                $city,
                $postalCode,
                $country,
                $profilePicturePath,
                $user_id
            );
        }

        // Debug: Log the query and values
        error_log("Update query: " . $updateQuery);
        error_log("Values: username='$username', full_name='$fullName', email='$email', gender='" . ($gender ?? 'NULL') . "', user_id=$user_id");

        if (!$updateStmt) {
            error_log("Prepare failed: " . $conn->error);
            throw new Exception('Database preparation error: ' . $conn->error);
        }

        if ($updateStmt->execute()) {
            $affectedRows = $updateStmt->affected_rows;
            error_log("Update executed successfully. Affected rows: " . $affectedRows);

            // Update session variables with new data
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['full_name'] = $fullName;
            $_SESSION['phone'] = $phone;
            $_SESSION['date_of_birth'] = $dateOfBirth;
            $_SESSION['gender'] = $gender;
            $_SESSION['street_address'] = $streetAddress;
            $_SESSION['city'] = $city;
            $_SESSION['postal_code'] = $postalCode;
            $_SESSION['country'] = $country;
            $_SESSION['profile_picture'] = $profilePicturePath;

            // Set success message in session
            $_SESSION['profile_success'] = 'Profile updated successfully!';

            error_log("Session updated, redirecting to Account.php");

            // Redirect to Account.php to show updated information
            header('Location: Account.php?updated=1');
            exit();
        } else {
            error_log("Update execution failed: " . $updateStmt->error);
            throw new Exception('Failed to update profile: ' . $updateStmt->error);
        }

        $updateStmt->close();
    } catch (Exception $e) {
        error_log("Exception in EditProfile.php: " . $e->getMessage());
        $error = $e->getMessage();
    }
}

// Set avatar source - check for profile picture
$avatarSrc = 'https://via.placeholder.com/120x120/C5AB96/ffffff?text=' . strtoupper(substr(($userData['username'] ?? 'US'), 0, 2));
if (!empty($userData['profile_picture']) && file_exists($userData['profile_picture'])) {
    $avatarSrc = $userData['profile_picture'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowUp - Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="EditProfile.css">
     <script src="EditProfile.js"></script>

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
                                <span class="wishlist-counter" id="wishlistCounter"></span>
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

            0%,
            100% {
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

    <div class="profile-container">
        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="profile-header">
            <div class="avatar-upload">
                <img src="<?php echo $avatarSrc; ?>"
                    alt="Profile Picture" class="profile-avatar" id="profileAvatar">
                <div class="avatar-upload-overlay" onclick="document.getElementById('avatarInput').click()">
                    <i class="fas fa-camera"></i>
                </div>
            </div>
            <div class="profile-name">Edit Profile</div>
            <div class="profile-email">Update your personal information</div>
        </div>

        <form class="edit-form" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="file" id="avatarInput" name="avatar" class="avatar-upload-input" accept="image/*" onchange="previewAvatar(this)">

            <div class="form-section">
                <h3 class="section-title">
                    <span class="section-icon">👤</span>
                    Personal Information
                </h3>
                <div class="form-group">
                    <label class="form-label">Username *</label>
                    <input type="text" class="form-control" name="username"
                        value="<?php echo htmlspecialchars($userData['username'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" class="form-control" name="fullName"
                        value="<?php echo htmlspecialchars($userData['full_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" class="form-control" name="email"
                        value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" name="phone"
                        value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="dateOfBirth"
                        value="<?php echo htmlspecialchars($userData['date_of_birth'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Gender</label>
                    <select class="form-select" name="gender" id="genderSelect">
                        <option value="">Select Gender</option>
                        <option value="Female" <?php echo (($userData['gender'] ?? '') === 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Male" <?php echo (($userData['gender'] ?? '') === 'Male') ? 'selected' : ''; ?>>Male</option>
                    </select>
                </div>

                <!-- Add this JavaScript to debug form submission -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.querySelector('.edit-form');
                        const genderSelect = document.getElementById('genderSelect');

                        // Debug: Log initial gender value
                        console.log('Initial gender value:', genderSelect.value);
                        console.log('Initial gender text:', genderSelect.options[genderSelect.selectedIndex].text);

                        // Debug: Monitor gender changes
                        genderSelect.addEventListener('change', function() {
                            console.log('Gender changed to:', this.value);
                            console.log('Gender text:', this.options[this.selectedIndex].text);
                        });

                        // Debug: Check form data before submission
                        form.addEventListener('submit', function(e) {
                            const formData = new FormData(form);
                            console.log('Form submission - Gender value:', formData.get('gender'));
                            console.log('Form submission - Gender select value:', genderSelect.value);

                            // Log all form data
                            for (let [key, value] of formData.entries()) {
                                console.log(key + ':', value);
                            }
                        });
                    });
                </script>
            </div>

            <div class="form-section">
                <h3 class="section-title">
                    <span class="section-icon">📍</span>
                    Shipping Address
                </h3>
                <div class="form-group">
                    <label class="form-label">Street Address</label>
                    <input type="text" class="form-control" name="streetAddress"
                        value="<?php echo htmlspecialchars($userData['street_address'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" name="city"
                        value="<?php echo htmlspecialchars($userData['city'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Postal Code</label>
                    <input type="text" class="form-control" name="postalCode"
                        value="<?php echo htmlspecialchars($userData['postal_code'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Country</label>
                    <select class="form-select" name="country">
                        <option value="">Select Country</option>
                        <option value="Saudi Arabia" <?php echo ($userData['country'] ?? '') === 'Saudi Arabia' ? 'selected' : ''; ?>>Saudi Arabia</option>
                        <option value="United States" <?php echo ($userData['country'] ?? '') === 'United States' ? 'selected' : ''; ?>>United States</option>
                        <option value="United Kingdom" <?php echo ($userData['country'] ?? '') === 'United Kingdom' ? 'selected' : ''; ?>>United Kingdom</option>
                        <option value="Canada" <?php echo ($userData['country'] ?? '') === 'Canada' ? 'selected' : ''; ?>>Canada</option>
                        <option value="Australia" <?php echo ($userData['country'] ?? '') === 'Australia' ? 'selected' : ''; ?>>Australia</option>
                    </select>
                </div>
            </div>

            <div class="form-section">
    <h3 class="section-title">
        <span class="section-icon">🔒</span>
        Security Settings
    </h3>
    <div class="form-group">
        <label class="form-label">Current Password (required to change password)</label>
        <div class="password-input-container">
            <input type="password" class="form-control password-input" name="currentPassword" id="currentPassword" placeholder="Enter current password">
            <span class="password-toggle-inside" onclick="togglePassword('currentPassword')">
                <i class="fas fa-eye" id="currentPassword-icon"></i>
            </span>
        </div>
    </div>
    <div class="form-group">
        <label class="form-label">New Password</label>
        <div class="password-input-container">
            <input type="password" class="form-control password-input" name="newPassword" id="newPassword" placeholder="Enter new password (minimum 8 characters)">
            <span class="password-toggle-inside" onclick="togglePassword('newPassword')">
                <i class="fas fa-eye" id="newPassword-icon"></i>
            </span>
        </div>
        <div class="password-strength" id="passwordStrength"></div>
        <div class="password-same-warning" id="passwordSameWarning"></div>
    </div>
    <div class="form-group">
        <label class="form-label">Confirm New Password</label>
        <div class="password-input-container">
            <input type="password" class="form-control password-input" name="confirmPassword" id="confirmPassword" placeholder="Confirm new password">
            <span class="password-toggle-inside" onclick="togglePassword('confirmPassword')">
                <i class="fas fa-eye" id="confirmPassword-icon"></i>
            </span>
        </div>
        <div class="password-match-indicator" id="passwordMatch"></div>
    </div>
</div>

            


            <div class="action-buttons">
                <button type="submit" class="btn-glowup">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="Account.php" class="btn-glowup btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Account
                </a>
                <button type="button" class="btn-glowup btn-danger" onclick="confirmDeleteAccount()">
                    <i class="fas fa-trash"></i> Delete Account
                </button>
            </div>
        </form>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Warning:</strong> This action cannot be undone. Are you sure you want to delete your account?</p>
                    <p>This will permanently remove:</p>
                    <ul>
                        <li>Your profile information</li>
                        <li>Order history</li>
                        <li>Wishlist items</li>
                        <li>Preferences and settings</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-glowup btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="delete_account.php" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" class="btn-glowup btn-danger">Yes, Delete My Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

  <div class="c-footer" title="back to top">
        <button type="button" id="backtotop" onclick="scrollToTop()">&#8593;</button>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
    
        // Avatar preview functionality
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profileAvatar').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Show/hide back to top button based on scroll
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop > 300) {
                document.body.classList.add('scrolled');
            } else {
                document.body.classList.remove('scrolled');
            }
        });

        // Confirm delete account
        function confirmDeleteAccount() {
            const modal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
            modal.show();
        }

        // Enhanced form submission with loading state
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.edit-form');

            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                submitBtn.disabled = true;

                // Re-enable button after 10 seconds in case of server issues
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                }, 10000);
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });

            // Debug form submission
            const genderSelect = document.querySelector('select[name="gender"]');
            if (genderSelect) {
                genderSelect.addEventListener('change', function() {
                    console.log('Gender selected:', this.value);
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
    const currentPassword = document.getElementById('currentPassword');
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const passwordStrength = document.getElementById('passwordStrength');
    const passwordMatch = document.getElementById('passwordMatch');
    const passwordSameWarning = document.getElementById('passwordSameWarning');

    // Password strength checker
    function checkPasswordStrength(password) {
        if (password.length === 0) {
            return { strength: '', text: '' };
        }
        
        let score = 0;
        let feedback = [];

        // Length check
        if (password.length >= 8) score++;
        else feedback.push('at least 8 characters');

        // Uppercase check
        if (/[A-Z]/.test(password)) score++;
        else feedback.push('uppercase letter');

        // Lowercase check
        if (/[a-z]/.test(password)) score++;
        else feedback.push('lowercase letter');

        // Number check
        if (/\d/.test(password)) score++;
        else feedback.push('number');

        // Special character check
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score++;
        else feedback.push('special character');

        if (score < 2) {
            return { strength: 'weak', text: 'Weak - Missing: ' + feedback.slice(0, 3).join(', ') };
        } else if (score < 4) {
            return { strength: 'medium', text: 'Medium - Consider adding: ' + feedback.slice(0, 2).join(', ') };
        } else {
            return { strength: 'strong', text: 'Strong password!' };
        }
    }

    // Check if new password is same as current password
    function checkPasswordSame() {
        const currentPass = currentPassword.value;
        const newPass = newPassword.value;

        if (newPass === '' || currentPass === '') {
            passwordSameWarning.innerHTML = '';
            passwordSameWarning.className = 'password-same-warning';
            return false;
        }

        if (currentPass === newPass) {
            passwordSameWarning.innerHTML = '<i class="fas fa-exclamation-triangle"></i> New password must be different from current password';
            passwordSameWarning.className = 'password-same-warning same-password';
            return true;
        } else {
            passwordSameWarning.innerHTML = '';
            passwordSameWarning.className = 'password-same-warning';
            return false;
        }
    }

    // Password match checker
    function checkPasswordMatch() {
        const newPass = newPassword.value;
        const confirmPass = confirmPassword.value;

        if (confirmPass === '') {
            passwordMatch.innerHTML = '';
            passwordMatch.className = 'password-match-indicator empty';
            return;
        }

        if (newPass === confirmPass) {
            passwordMatch.innerHTML = '<i class="fas fa-check"></i> Passwords match';
            passwordMatch.className = 'password-match-indicator match';
        } else {
            passwordMatch.innerHTML = '<i class="fas fa-times"></i> Passwords do not match';
            passwordMatch.className = 'password-match-indicator no-match';
        }
    }

    // Event listeners
    currentPassword.addEventListener('input', function() {
        checkPasswordSame();
    });

    newPassword.addEventListener('input', function() {
        const result = checkPasswordStrength(this.value);
        if (result.text) {
            passwordStrength.innerHTML = result.text;
            passwordStrength.className = 'password-strength ' + result.strength;
        } else {
            passwordStrength.innerHTML = '';
            passwordStrength.className = 'password-strength';
        }
        checkPasswordMatch();
        checkPasswordSame();
    });

    confirmPassword.addEventListener('input', checkPasswordMatch);

    // Form validation
    const form = document.querySelector('.edit-form');
    form.addEventListener('submit', function(e) {
        const newPass = newPassword.value;
        const confirmPass = confirmPassword.value;
        const currentPass = currentPassword.value;

        // If any password field is filled, validate all
        if (newPass || confirmPass || currentPass) {
            if (!currentPass) {
                e.preventDefault();
                alert('Please enter your current password to change your password.');
                currentPassword.focus();
                return;
            }

            if (!newPass) {
                e.preventDefault();
                alert('Please enter a new password.');
                newPassword.focus();
                return;
            }

            if (newPass.length < 8) {
                e.preventDefault();
                alert('New password must be at least 8 characters long.');
                newPassword.focus();
                return;
            }

            if (currentPass === newPass) {
                e.preventDefault();
                alert('New password must be different from your current password.');
                newPassword.focus();
                return;
            }

            if (newPass !== confirmPass) {
                e.preventDefault();
                alert('New password and confirmation do not match.');
                confirmPassword.focus();
                return;
            }
        }
    });
});

// Password visibility toggle function
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
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