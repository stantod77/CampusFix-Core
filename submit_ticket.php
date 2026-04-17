<?php
$servername = "localhost";
$username = "www-data";
$password = "";
$dbname = "campusfix_db";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Collect and Sanitize Input
    // We use 'title' as a summary of the issue since your DB requires it
    $name = $_POST['requester_name'] ?? 'Anonymous';
    $room = $_POST['room_number'] ?? 'Unknown';
    $severity = intval($_POST['severity'] ?? 3);
    $description = $_POST['description'] ?? '';
    $title = $name . " | Room: " . $room;

    // Check if description is empty to prevent the 'Null' error
    if (empty($description)) {
        throw new Exception("Description is required.");
    }

    // SQL matching your image_b0dfc6.png structure
    $sql = "INSERT INTO tickets (title, description, location, severity_level, status) 
            VALUES (?, ?, ?, ?, 'open')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $description, $room, $severity);

    if ($stmt->execute()) {
        // Professional Success Page with Gateway Redirect
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <title>Success | CampusFix</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <style>
                body { background: #f8f9fa; height: 100vh; display: flex; align-items: center; justify-content: center; }
                .success-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-top: 5px solid #0b1f3a; text-center; max-width: 500px; width: 100%; }
                .btn-navy { background: #0b1f3a; color: white; font-weight: bold; border-radius: 4px; padding: 12px 24px; text-decoration: none; }
                .btn-navy:hover { background: #1a3a61; color: white; }
            </style>
        </head>
        <body>
            <div class='success-card text-center'>
                <h2 style='color: #0b1f3a;' class='fw-bold mb-3'>Ticket Submitted!</h2>
                <p class='text-muted mb-4'>Your request for <strong>$room</strong> has been logged in the system.</p>
                <a href='index.php' class='btn btn-navy'>Home</a>
            </div>
        </body>
        </html>";
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo "<div style='padding:20px; color:red; font-family:sans-serif;'>";
    echo "<strong>System Error:</strong> " . $e->getMessage();
    echo "<br><a href='ticket.html'>Go back and try again</a>";
    echo "</div>";
}
?>
