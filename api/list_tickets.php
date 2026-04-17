<?php
header('Content-Type: application/json');
include('../db_config.php');

// Explicitly selecting 7 columns in order
$sql = "SELECT 
            ticket_id,      -- d[0]
            title,          -- d[1]
            location,       -- d[2]
            description,    -- d[3]
            severity_level, -- d[4]
            status,         -- d[5]
            created_at      -- d[6]
        FROM tickets 
        ORDER BY created_at DESC";

$result = $conn->query($sql);
$tickets = [];

if ($result) {
    while($row = $result->fetch_row()) {
        $tickets[] = $row;
    }
}

echo json_encode($tickets);
?>
