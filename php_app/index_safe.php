<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once "db.php";
include_once "stats_helper.php";
$stats = getStats($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ScamShield | Live</title>
    <?php include "header_assets.php"; ?>
</head>
<body>
    <?php include "navbar.php"; ?>
    <div class="container py-5">
        <h1 class="text-white">🚀 ScamShield is Live!</h1>
        <p class="text-white-50">Database Connection: <b><?php echo ($conn ? "CONNECTED ✅" : "FAILED ❌"); ?></b></p>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-dark text-white p-4">
                    <h3><?php echo $stats['total_scams']; ?></h3>
                    <p>Scams Blocked</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
