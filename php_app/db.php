<?php
// Secure Cloud Database Connection for ScamShield
// Handles Local (XAMPP), Vercel, and Aiven MySQL 8 with SSL

$is_local = ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == '127.0.0.1');
$is_vercel = (getenv('VERCEL') == '1');

if ($is_local && !$is_vercel) {
    // 🏠 Local XAMPP Environment
    $conn = mysqli_connect("localhost", "root", "", "scamshield_db");
    $api_base_url = "http://127.0.0.1:5000";
} else {
    // ☁️ Cloud / Vercel Environment - SENSITIVE DATA REMOVED FOR GITHUB SAFETY
    // Please set these in your Vercel Dashboard Settings
    $db_host = getenv('DB_HOST') ?: "mysql-24172f92-think-programming.l.aivencloud.com";
    $db_user = getenv('DB_USER') ?: "avnadmin";
    $db_pass = getenv('DB_PASS'); // NO HARDCODED PASSWORD
    $db_name = getenv('DB_NAME') ?: "scamshield_db";
    $db_port = getenv('DB_PORT') ?: 27029;

    // Final check for connection
    if (!$db_pass) {
        die("<div style='background:#fff3cd; color:#856404; padding:20px; border-radius:10px; margin:20px; font-family:sans-serif;'>
                <h3>⚠️ Configuration Incomplete</h3>
                <p>Please add <b>DB_PASS</b> to your Vercel Environment Variables.</p>
             </div>");
    }

    // Initialize Connection with SSL (Required for Aiven)
    $conn = mysqli_init();
    mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL); 
    
    $success = @mysqli_real_connect($conn, $db_host, $db_user, $db_pass, $db_name, $db_port, NULL, MYSQLI_CLIENT_SSL);

    if (!$success) {
        // Fallback for non-SSL
        $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
    }
    
    // Auto-detect API base URL
    if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'vercel.app') !== false) {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https://" : "http://";
        $api_base_url = $protocol . $_SERVER['HTTP_HOST'] . "/api";
    } else {
        $api_base_url = "https://scamshield-cplu.onrender.com";
    }
}

if (!$conn) {
    die("<div style='background:#f8d7da; color:#721c24; padding:20px; border-radius:10px; margin:20px; font-family:sans-serif;'>
            <h3>⚠️ Database Offline</h3>
            <p>Could not connect to " . ($is_local ? "Local" : "Cloud") . " Database.</p>
            <p>Error: " . mysqli_connect_error() . "</p>
         </div>");
}
?>
