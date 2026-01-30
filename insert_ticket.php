<?php
// 1. Database Credentials
$servername = "localhost";
$username = "admin";        // The new user we just made
$password = "Campus2026";   // The specific password we just set
$dbname = "campusfix_db";

// 2. Create Connection
$conn = new mysqli($servername, $username, $password, $dbname);

// 3. Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 4. Check if the Form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get data from the form (these match the 'name' attributes in HTML)
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $building_name = $_POST['building']; // This sends the text like "Science Lab"
    $room = $_POST['room'];
    $category = $_POST['category'];
    $severity = $_POST['severity'];
    $description = $_POST['description'];
    
    // Default Status
    $status = 'Open';

    // 5. The SQL Command (The Order Ticket)
    // Note: We need to lookup the Building ID first, but for now let's just insert the text
    // Assuming your 'tickets' table has a 'building_id', we might need a quick lookup here.
    // For this test, let's just ensure we are inserting into valid columns.
    
    $sql = "INSERT INTO tickets (user_id, title, description, status, severity_level, created_at) 
            VALUES (1, '$category Issue in $building_name', '$description', '$status', '$severity', NOW())";
            
    // Note: I put 'user_id = 1' as a placeholder since we don't have a login system yet.

   if ($conn->query($sql) === TRUE) {
        // Redirect back to the main page with a success signal
        header("Location: index.html?status=success");
        exit(); // Always exit after a header redirect!
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>