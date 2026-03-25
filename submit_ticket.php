<?php
$servername = "localhost";
$username = "www-data"; 
$password = ""; 
$dbname = "campusfix_db";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Map your Perfect Form to your Real Table
    $title = $_POST['requester_name']; 
    $location = $_POST['room_number']; 
    $severity = intval($_POST['severity']); 
    $desc = $_POST['description'];

    $sql = "INSERT INTO tickets (title, location, severity_level, description, status) 
            VALUES (?, ?, ?, ?, 'open')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $title, $location, $severity, $desc);
    
    if ($stmt->execute()) {
        echo "<html><body style='font-family:sans-serif; text-align:center; padding-top:50px; background:#f4f4f4;'>";
        echo "<div style='max-width:500px; margin:auto; padding:30px; background:white; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1); border-top: 5px solid #0b1f3a;'>";
        echo "<h2 style='color:#0b1f3a;'>Ticket Submitted!</h2>";
        echo "<p>Your request has been logged successfully.</p>";
        echo "<a href='ticket.html' style='display:inline-block; margin-top:20px; padding:10px 20px; background:#0b1f3a; color:white; text-decoration:none; border-radius:6px; font-weight:bold;'>Return to Form</a>";
        echo "</div></body></html>";
    }
    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage();
}
?>
