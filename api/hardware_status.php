<?php
header('Content-Type: application/json');
include('../db_config.php'); 

// Count all tickets where status is exactly 'Open'
$sql = "SELECT COUNT(*) as open_count FROM tickets WHERE status = 'Open'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Output JSON: active is 1 if count > 0, otherwise 0
echo json_encode([
    "active" => ($row['open_count'] > 0 ? 1 : 0),
    "count" => (int)$row['open_count'],
    "timestamp" => date("Y-m-d H:i:s")
]);
?>
