<?php
session_start(); // 1. Start the session immediately

require 'db_connect.php'; // 2. Connect to DB

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 3. Sanitize input (Security)
    $email = $conn->real_escape_string($_POST['email']);
    $password_input = $_POST['password'];

    // 4. Find the user
    $sql = "SELECT user_id, full_name, role, password_hash FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // 5. Verify Password
        // Note: We check TWO things here:
        // A) Is it a real encrypted hash? (Future secure passwords)
        // B) Is it the simple text from our Seed Data? (So your test data works)
        if (password_verify($password_input, $row['password_hash']) || $password_input === $row['password_hash']) {
            
            // Success! Create the ID Card (Session Variables)
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_name'] = $row['full_name'];
            $_SESSION['role'] = $row['role'];

            // Redirect to Dashboard
            header("Location: dashboard.php");
            exit();
            
        } else {
            echo "<script>alert('Incorrect Password!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.history.back();</script>";
    }
}
$conn->close();
?>