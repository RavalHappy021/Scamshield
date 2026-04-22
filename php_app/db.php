<?php
// Secure Database Connection for ScamShield (Updated for InfinityFree + Localhost)

// Disable mysqli exception throwing to handle errors manually
mysqli_report(MYSQLI_REPORT_OFF);

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$is_local = ($host == 'localhost' || ($_SERVER['REMOTE_ADDR'] ?? '') == '127.0.0.1' || $host == '127.0.0.1');
$is_infinity = (strpos($host, 'infinityfreeapp.com') !== false);

if ($is_local) {
    // 🏠 Local XAMPP Environment
    $conn = @mysqli_connect("localhost", "root", "", "scamshield_db");
    $api_base_url = "http://127.0.0.1:5000";
} elseif ($is_infinity) {
    // ♾️ InfinityFree Hosting
    $db_host = "sql200.infinityfree.com";
    $db_user = "if0_41241884";
    $db_pass = "hPtOV955QP3PaC";
    $db_name = "if0_41241884_myproject";

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (!$conn) {
        die("❌ Database connection failed! Error: " . mysqli_connect_error());
    }
    $api_base_url = "https://your-python-api.onrender.com";
} else {
    // ☁️ Other Live Environments
    $db_host = getenv('DB_HOST') ?: "mysql-24172f92-think-programming.l.aivencloud.com";
    $db_user = getenv('DB_USER') ?: "avnadmin";
    $db_pass = getenv('DB_PASS');
    $db_name = getenv('DB_NAME') ?: "scamshield_db";
    $db_port = getenv('DB_PORT') ?: 27029;

    $conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
    $api_base_url = "http://127.0.0.1:5000";
}

// Check for connection error but DON'T output HTML here as it breaks pages.
// Scripts that need the connection should check if($conn) themselves.
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
}
?>