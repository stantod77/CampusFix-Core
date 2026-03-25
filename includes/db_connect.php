<?php
/**
 * CampusFix - Single Source of Truth for Database Connection
 * Used by all PHP modules to interact with MariaDB on Raspberry Pi.
 */

// Configuration variables
$db_host = "127.0.0.1";
$db_user = "campus_admin";
$db_pass = "CampusFix2026!";
$db_name = "campusfix_db";

// Establish connection using Object-Oriented MySQLi
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Resilience: Check connection and handle errors gracefully
if ($conn->connect_error) {
    // In production, log error to file and show generic message
    // For our Capstone update, we'll give a clean, specific message
    error_log("Connection failed: " . $conn->connect_error);
    
    die("<div style='color:red; font-family:sans-serif; padding:20px; border:1px solid red;'>
            <h2>System Connection Error</h2>
            <p>We are currently experiencing database connectivity issues. Please contact the Campus Admin.</p>
         </div>");
}

// Set charset to ensure emojis and special characters work across C++ and PHP
$conn->set_charset("utf8mb4");

// The $conn variable is now globally available for any file that includes this script
?>
