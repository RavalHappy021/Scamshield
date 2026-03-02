<?php
// Check if running locally or on InfinityFree
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    // Local XAMPP Connection
    $conn = mysqli_connect("localhost", "root", "", "scamshield_db");
} else {
    // InfinityFree Live Connection
    $conn = mysqli_connect("sql200.infinityfree.com", "if0_41241884", "ScamScam54321", "if0_41241884_myproject");
}

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
