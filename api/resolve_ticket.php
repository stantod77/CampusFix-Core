<?php
include('../auth_check.php');

$servername = "localhost";
$username = "www-data";
$password = "";
$dbname = "campusfix_db";

$id = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;

if ($id && $status) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $stmt = $conn->prepare("UPDATE tickets SET status = ? WHERE ticket_id = ?");
    $stmt->bind_param("si", $status, $id);
    
    if ($stmt->execute()) {
        // --- NEW: LOGGING LOGIC FOR AUDIT TRAIL ---
        $log_file = 'notifications.log';
        $timestamp = date("Y-m-d H:i:s");
        $log_entry = "[$timestamp] EVENT: Ticket #$id status changed to: " . strtoupper($status) . "\n";
        file_put_contents($log_file, $log_entry, FILE_APPEND);
        // ------------------------------------------

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    $conn->close();
}
?>
