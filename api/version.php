<?php
require_once __DIR__ . '/db.php';
header("Content-Type: text/plain");
echo "ScamShield Live Version Check\n";
echo "-----------------------------\n";
echo "Internal Version: " . SCAMSHIELD_VERSION . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Server Time: " . date("Y-m-d H:i:s") . "\n";
echo "Database Status: " . ($conn ? "CONNECTED" : "FAILED") . "\n";
?>
