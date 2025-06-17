<?php
// Database configuration
$servername = "localhost";
$username = "root";  // Replace with your MySQL username
$password = "";  // Replace with your MySQL password
$dbname = "glowup_store";     // Replace with your database name

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset
    $conn->set_charset("utf8");
    
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}
?>