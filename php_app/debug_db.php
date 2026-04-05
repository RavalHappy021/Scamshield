<?php
/**
 * 🛠️ ScamShield LIVE DATABASE DIAGNOSTIC TOOL
 * 
 * Instructions:
 * 1. Upload this file to your InfinityFree 'htdocs/php_app/' folder.
 * 2. Visit your-site-url/php_app/debug_db.php in your browser.
 * 3. Copy-paste the output here so I can fix your database connection!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🛡️ ScamShield Live Diagnostic Tool</h2><hr>";

// --- Step 1: PHP Environment check ---
echo "<b>[1] Checking Environment:</b><br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Host: " . $_SERVER['HTTP_HOST'] . " (" . $_SERVER['SERVER_ADDR'] . ")<br>";
echo "OpenSSL Extension: " . (extension_loaded('openssl') ? "<span style='color:green;'>✅ ENABLED</span>" : "<span style='color:red;'>❌ DISABLED</span>") . "<br>";
echo "MySQLi Extension: " . (extension_loaded('mysqli') ? "<span style='color:green;'>✅ ENABLED</span>" : "<span style='color:red;'>❌ DISABLED</span>") . "<br>";

// --- Step 2: Attempting to connect to Aiven (Live Config from db.php) ---
echo "<br><b>[2] Testing Aiven Connection (External):</b><br>";
$db_host = getenv('DB_HOST') ?: "mysql-24172f92-think-programming.l.aivencloud.com";
$db_user = getenv('DB_USER') ?: "avnadmin";
$db_pass = getenv('DB_PASS'); // REMOVED HARDCODED PASS
$db_name = getenv('DB_NAME') ?: "scamshield_db";
$db_port = getenv('DB_PORT') ?: 27029;

echo "Connecting to $db_host on port $db_port...<br>";

mysqli_report(MYSQLI_REPORT_OFF); // Don't crash, just show errors manually
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

$start_time = microtime(true);
$success = @mysqli_real_connect($conn, $db_host, $db_user, $db_pass, $db_name, $db_port, NULL, MYSQLI_CLIENT_SSL);
$end_time = microtime(true);

if ($success) {
    echo "<span style='color:green;'>✅ SUCCESS with SSL!</span> (took " . round($end_time - $start_time, 2) . "s)<br>";
} else {
    echo "<span style='color:orange;'>⚠️ SSL Connection Failed.</span> Error: " . mysqli_connect_error() . "<br>";
    
    echo "Retrying without SSL...<br>";
    $conn2 = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
    if ($conn2) {
        echo "<span style='color:green;'>✅ SUCCESS WITHOUT SSL!</span><br>";
    } else {
        echo "<span style='color:red;'>❌ TOTAL FAILURE TO CONNECT.</span> Error: " . mysqli_connect_error() . "<br>";
        echo "<small>Tip: If this is a 'Connection Refused' or 'Network Unreachable' error, InfinityFree is likely blocking external databases.</small><br>";
    }
}

// --- Step 3: Advice ---
echo "<br><b>[3] Summary:</b><br>";
echo "Paste the error message above into our chat so I can give you the fix!";
?>
