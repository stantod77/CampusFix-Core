<?php
session_start();
$servername = "localhost";
$username = "www-data";
$password = "";
$dbname = "campusfix_db";

$conn = new mysqli($servername, $username, $password, $dbname);

$email = $_POST['email'];
$pass = $_POST['password']; // In production, use password_verify()

$sql = "SELECT user_id, full_name FROM users WHERE email = ? AND password_hash = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $pass);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['full_name'] = $user['full_name'];
    header("Location: dashboard.php");
} else {
    echo "Invalid login. <a href='login.html'>Try again</a>";
}
?>
