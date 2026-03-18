<?php
// Secure Database Connection for ScamShield (InfinityFree + Render Version)

$is_local = ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == '127.0.0.1');

if ($is_local) {
    // 🏠 Local XAMPP Environment
    $conn = mysqli_connect("localhost", "root", "", "scamshield_db");
    $api_base_url = "http://127.0.0.1:5000";
} else {
    // ☁️ Live Environment (InfinityFree + Render)
    // Please set these in your InfinityFree / Render dashboard
    $db_host = getenv('DB_HOST') ?: "mysql-24172f92-think-programming.l.aivencloud.com";
    $db_user = getenv('DB_USER') ?: "avnadmin";
    $db_pass = getenv('DB_PASS'); // NO HARDCODED PASSWORD (Security Policy)
    $db_name = getenv('DB_NAME') ?: "scamshield_db";
    $db_port = getenv('DB_PORT') ?: 27029;

    if (!$db_pass) {
        die("Error: DB_PASS is missing in your hosting environment variables.");
    }

    $conn = mysqli_init();
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL); 
    $success = @mysqli_real_connect($conn, $db_host, $db_user, $db_pass, $db_name, $db_port, NULL, MYSQLI_CLIENT_SSL);

    if (!$success) {
        $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
    }
    
    // Stable Render API
    $api_base_url = "https://scamshield-cplu.onrender.com";
}

if (!$conn) {
    die("Database Offline. Please check your hosting environment variables.");
}
?>
