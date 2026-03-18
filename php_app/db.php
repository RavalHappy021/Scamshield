<?php
// Check if running locally or on InfinityFree
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    // Local XAMPP Connection
    $conn = mysqli_connect("localhost", "root", "", "scamshield_db");
    $api_base_url = "http://127.0.0.1:5000";
} else {
    // Vercel / Live Connection
    $conn = mysqli_connect("sql200.infinityfree.com", "if0_41241884", "ScamScam54321", "if0_41241884_myproject");
    
    // Auto-detect Vercel URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    $api_base_url = $protocol . $_SERVER['HTTP_HOST'] . "/api";
}

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
