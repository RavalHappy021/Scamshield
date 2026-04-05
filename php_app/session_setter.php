<?php
session_start();
$_SESSION['user_id'] = 1; 
$_SESSION['user'] = 'happy raval';
$_SESSION['role'] = 'admin';
echo "Admin session set. <a href='admin_dashboard.php'>Go to Dashboard</a>";
?>
