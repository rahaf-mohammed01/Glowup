<?php
// auth_middleware.php - Include this file in pages that need role-based access control

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Get current user role
 */
function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

/**
 * Check if user has specific role
 */
function hasRole($role) {
    return getUserRole() === $role;
}

/**
 * Check if user has any of the specified roles
 */
function hasAnyRole($roles) {
    $userRole = getUserRole();
    return in_array($userRole, $roles);
}

/**
 * Redirect user based on their role
 */
function redirectByRole() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
    
    $role = getUserRole();
    switch($role) {
        case 'admin':
            header('Location: admin.php');
            break;
        case 'supplier':
            header('Location: supplier.php');
            break;
        case 'customer':
            header('Location: home.php');
            break;
        default:
            header('Location: admin-supplier-login.php');
            break;
    }
    exit();
}

/**
 * Require login - redirect to login if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location:admin-supplier-login.php');
        exit();
    }
}

/**
 * Require specific role - redirect to appropriate page if wrong role
 */
function requireRole($role) {
    if (!isLoggedIn()) {
        header('Location:  admin-supplier-login.php');
        exit();
    }
    
    if (!hasRole($role)) {
        // Redirect to user's appropriate dashboard
        redirectByRole();
    }
}

/**
 * Require any of the specified roles
 */
function requireAnyRole($roles) {
    if (!isLoggedIn()) {
        header('Location:  admin-supplier-login.php');
        exit();
    }
    
    if (!hasAnyRole($roles)) {
        // Redirect to user's appropriate dashboard
        redirectByRole();
    }
}

/**
 * Require admin role
 */
function requireAdmin() {
    requireRole('admin');
}

/**
 * Require supplier role
 */
function requireSupplier() {
    requireRole('supplier');
}

/**
 * Require customer role
 */
function requireCustomer() {
    requireRole('customer');
}

/**
 * Get current user ID
 */
function getUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get user info (updated to include user_id)
 */
function getUserInfo() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'user_id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'role' => $_SESSION['role'] ?? ''
    ];
}

/**
 * Check if user can access admin features
 */
function canAccessAdmin() {
    return hasRole('admin');
}

/**
 * Check if user can access supplier features
 */
function canAccessSupplier() {
    return hasAnyRole(['admin', 'supplier']);
}

/**
 * Check if user can access customer features
 */
function canAccessCustomer() {
    return hasAnyRole(['admin', 'customer']);
}

?>