<?php
session_start();
session_destroy(); // Destroy the ID card
header("Location: test_login_form.html"); // Go back to login
exit();
?>