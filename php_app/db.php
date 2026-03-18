<?php
// Check if running on Vercel or locally
if (getenv('VERCEL') == '1' || ($_SERVER['HTTP_HOST'] != 'localhost' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1' && $_SERVER['HTTP_HOST'] != '127.0.0.1')) {
    
    // Live Connections (InfinityFree / Vercel / Aiven)
    $db_host = getenv('DB_HOST') ?: "sql200.infinityfree.com";
    $db_user = getenv('DB_USER') ?: "if0_41241884";
    $db_pass = getenv('DB_PASS') ?: "ScamScam54321";
    $db_name = getenv('DB_NAME') ?: "if0_41241884_myproject";
    $db_port = getenv('DB_PORT') ?: 3306;

    // Aiven and some modern hosts require SSL
    $conn = mysqli_init();
    
    // If it's Aiven (detected by host or port), we might need to skip certificate verification for simplicity in this environment
    // or just enable SSL.
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL); 
    
    $success = mysqli_real_connect($conn, $db_host, $db_user, $db_pass, $db_name, $db_port, NULL, MYSQLI_CLIENT_SSL);

    if (!$success) {
        // Fallback to non-SSL if SSL fails (for InfinityFree)
        $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
    }
    
    // Auto-detect API environment
    if (strpos($_SERVER['HTTP_HOST'], 'vercel.app') !== false) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
        $api_base_url = $protocol . $_SERVER['HTTP_HOST'] . "/api";
    } else {
        $api_base_url = "https://scamshield-cplu.onrender.com";
    }
} else {
    // Local XAMPP Connection
    $conn = mysqli_connect("localhost", "root", "", "scamshield_db");
    $api_base_url = "http://127.0.0.1:5000";
}

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
