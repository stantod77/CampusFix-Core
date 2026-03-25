<?php
header('Content-Type: application/json');
include('../db_config.php');

// Pulling the columns Kaleb needs for his logic
$sql = "SELECT ticket_id, title, location, severity_level, status, priority_score, created_at FROM tickets ORDER BY created_at DESC";
$result = $conn->query($sql);
$tickets = [];

while($row = $result->fetch_assoc()) {
    $tickets[] = $row;
}

// Send the whole array to the Java app
echo json_encode($tickets);
?>
