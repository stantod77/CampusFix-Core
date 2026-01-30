<?php
session_start();

// Security Check: If they aren't logged in, kick them out!
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-success">
            <h4>🎉 Login Successful!</h4>
            <p>Welcome back, <strong><?php echo $_SESSION['user_name']; ?></strong></p>
            <p>Your Role: <span class="badge bg-primary"><?php echo $_SESSION['role']; ?></span></p>
        </div>
        
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>