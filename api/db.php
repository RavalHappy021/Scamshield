<?php
// Secure Database Connection for ScamShield 
// Supports: Local XAMPP, InfinityFree, and Vercel/Render (Serverless)

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
define('SCAMSHIELD_VERSION', '1.0.4'); // Force Deploy Trigger


$is_local = (stripos($_SERVER['HTTP_HOST'], 'localhost') !== false || $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || stripos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
$is_vercel = getenv('VERCEL') == '1';

// Aggressive cleaner for environment variables
function safe_clean($val) {
    if (!$val) return "";
    return trim(preg_replace('/[\x00-\x1F\x7F-\xA0]/', '', $val));
}

if ($is_local && !$is_vercel) {
    // ... (unchanged)
}
else {
    // ☁️ Live Environment
    $db_host = safe_clean(getenv('DB_HOST') ?: "mysql-24172f92-think-programming.l.aivencloud.com");
    $db_user = safe_clean(getenv('DB_USER') ?: "avnadmin");
    $db_pass = safe_clean(getenv('DB_PASS')); 
    $db_name = safe_clean(getenv('DB_NAME') ?: "scamshield_db");
    $db_port = (int)safe_clean(getenv('DB_PORT') ?: "27029");
    $api_base_url = safe_clean(getenv('API_BASE_URL') ?: "https://scamshield-cplu.onrender.com");

    try {
        mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);
        $conn = mysqli_init();
        mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
        $success = @mysqli_real_connect($conn, $db_host, $db_user, $db_pass, $db_name, $db_port, NULL, MYSQLI_CLIENT_SSL);
        
        if (!$success) {
            $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
        }
    } catch (Exception $e) {
        $conn = false;
        $error_msg = $e->getMessage();
        $host_len = strlen($db_host);
        die("❌ DB Error: $error_msg\nHostname Length: $host_len\nDebug: Check Vercel Env Vars for spaces.");
    }
}

// 🛑 CRITICAL CHECK: Ensure connection is actually alive
if (!$conn || mysqli_connect_errno()) {
    header("Content-Type: text/plain");
    die("❌ Database Connection Failed!\nError: " . mysqli_connect_error());
}
?>
