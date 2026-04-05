<?php 
// DEBUG VERSION - NO INCLUDES AT TOP
echo "<!-- GUEST ACCESS DEBUG: Page Loaded -->";

// include "navbar.php"; // Commented out temporarily for testing
// include "db.php";     // Commented out temporarily for testing

?>
<!DOCTYPE html>
<html>
<head>
    <title>DEBUG GUEST - ScamShield</title>
</head>
<body style="background: #0f2027; color: white; font-family: sans-serif; padding: 50px; text-align: center;">
    <h1>🛡️ Guest Access Debug</h1>
    <p>If you see this page, then Guest Access is WORKING on the server.</p>
    <p>Current Time: <?php echo date('H:i:s'); ?></p>
    <hr>
    <a href="index.php" style="color: #00d2ff;">Go back to Home</a>
</body>
</html>
