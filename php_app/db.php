<?php
// Secure Database Connection for ScamShield 
// Supports: Local XAMPP, InfinityFree, and Vercel/Render (Serverless)

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$is_local = (stripos($_SERVER['HTTP_HOST'], 'localhost') !== false || $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || stripos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
$is_vercel = getenv('VERCEL') == '1';

if ($is_local && !$is_vercel) {
    // 🏠 Local XAMPP Environment
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "scamshield_db";
    $db_port = 3306;
    $api_base_url = "http://127.0.0.1:5000";

    if (file_exists(__DIR__ . '/db_config_local.php')) {
        include(__DIR__ . '/db_config_local.php');
    }

    try {
        mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);
        $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
    }
    catch (mysqli_sql_exception $e) {
        $conn = false;
    }
}
else {
    // ☁️ Live Environment (Vercel, Render, Aiven)
    // Using getenv() to prioritize secure environment variables
    $db_host = getenv('DB_HOST') ?: "mysql-24172f92-think-programming.l.aivencloud.com";
    $db_user = getenv('DB_USER') ?: "avnadmin";
    $db_pass = getenv('DB_PASS'); // REMOVED HARDCODED PASS FOR SECURITY
    $db_name = getenv('DB_NAME') ?: "scamshield_db";
    $db_port = getenv('DB_PORT') ?: 27029;
    $api_base_url = getenv('API_BASE_URL') ?: "https://scamshield-cplu.onrender.com";

    $conn = mysqli_init();
    // Aiven and some cloud providers require SSL
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
    
    $success = @mysqli_real_connect($conn, $db_host, $db_user, $db_pass, $db_name, (int)$db_port, NULL, MYSQLI_CLIENT_SSL);

    if (!$success) {
        // Fallback to standard connection if SSL fails (though SSL is recommended for cloud DBs)
        $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, (int)$db_port);
    }
}

// 🛑 CRITICAL CHECK: Ensure connection is actually alive
if (!$conn || mysqli_connect_errno()) {
    header("Content-Type: text/plain");
    die("❌ Database Connection Failed!\nError: " . mysqli_connect_error());
}
?>
