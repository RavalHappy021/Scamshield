<?php 
// FORCE GUEST ACCESS - NO LOGIN CHECK
session_start();
include "db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PUBLIC TEST | ScamShield</title>
</head>
<body style="background: #0f2027; color: white; padding: 50px;">
    <h1>Public Job Checker TEST</h1>
    <p>If you see this, the server is NOT redirecting you.</p>
    <hr>
    <?php include "check_job.php"; ?>
</body>
</html>
