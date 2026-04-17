<?php
    echo "<h1>System Status: ONLINE</h1>";
    echo "<p><b>PHP Version:</b> " . phpversion() . "</p>";
    echo "<p><b>Server Software:</b> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
    
    // Simple math to prove the engine is running
    $check = 10 * 10;
    echo "<p>Math Check (10x10): <b>" . $check . "</b></p>";
?>