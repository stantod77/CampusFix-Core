<?php
require_once 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $b_id = intval($_POST['building_id']);
    $c_id = intval($_POST['category_id']);
    $s_lvl = intval($_POST['severity_level']);
    
    // Combine contact info into the description
    $full_desc = "From: " . $_POST['student_name'] . " (" . $_POST['student_email'] . ") - " . $_POST['description'];
    $desc = mysqli_real_escape_string($conn, $full_desc);

    $sql = "INSERT INTO Tickets (building_id, category_id, severity_level, description, status) 
            VALUES ($b_id, $c_id, $s_lvl, '$desc', 'Open')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Thank you! Ticket submitted.'); window.location.href='ticket.html';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
