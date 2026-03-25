<?php
header('Content-Type: application/json');
include('../db_config.php');

// Get ID and Status from the URL (e.g., ?id=101&status=open)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$newStatus = isset($_GET['status']) ? $_GET['status'] : 'resolved';

// Validation
if ($id <= 0) {
    echo json_encode(["success" => false, "error" => "Invalid ID"]);
    exit;
}

// 1. Execute the Update
$sql = "UPDATE tickets SET status = '$newStatus' WHERE ticket_id = $id";

if ($conn->query($sql) === TRUE) {
    // 2. Log the change for your Testing Report
    $logEntry = date("Y-m-d H:i:s") . " | Ticket #$id changed to " . strtoupper($newStatus) . "\n";
    file_put_contents('notifications.log', $logEntry, FILE_APPEND);

    echo json_encode(["success" => true, "status" => $newStatus]);
} else {
    echo json_encode(["success" => false, "error" => $conn->error]);
}

$conn->close();
?>
