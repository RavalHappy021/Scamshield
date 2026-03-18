<?php
// Check if running on Vercel or locally
if (getenv('VERCEL') == '1' || ($_SERVER['HTTP_HOST'] != 'localhost' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1' && $_SERVER['HTTP_HOST'] != '127.0.0.1')) {
    // Live Connections (InfinityFree / Vercel)
    $db_host = getenv('DB_HOST') ?: "sql200.infinityfree.com";
    $db_user = getenv('DB_USER') ?: "if0_41241884";
    $db_pass = getenv('DB_PASS') ?: "ScamScam54321";
    $db_name = getenv('DB_NAME') ?: "if0_41241884_myproject";

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    
    // Auto-detect environment
    if (strpos($_SERVER['HTTP_HOST'], 'vercel.app') !== false) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
        $api_base_url = $protocol . $_SERVER['HTTP_HOST'] . "/api";
    } else {
        // Fallback to existing Render API for InfinityFree or others
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
