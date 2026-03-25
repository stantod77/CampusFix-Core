<?php
require_once 'includes/db_connect.php';

if ($conn->ping()) {
    echo "✅ [SUCCESS] Single Source of Truth Verified.\n";
    echo "Host: " . $conn->host_info . "\n";
} else {
    echo "❌ [FAILURE] Connection lost.\n";
}
?>
