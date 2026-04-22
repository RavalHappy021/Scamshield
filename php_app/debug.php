<?php
// Enable all error reporting to find the cause of the 500 error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🛠 ScamShield InfinityFree Debugger</h1>";

// Include your database connection file
include_once "db.php";

echo "<h3>1. Checking Host Detection</h3>";
$host = $_SERVER['HTTP_HOST'] ?? 'unknown';
echo "Current Host: <b>" . $host . "</b><br>";
$is_infinity = (strpos($host, 'infinityfreeapp.com') !== false);
echo "Detected as InfinityFree? <b>" . ($is_infinity ? "YES" : "NO") . "</b><br>";

echo "<h3>2. Checking Database Connection</h3>";
if (!isset($conn)) {
    echo "<p style='color:red'>❌ Variable \$conn is not even defined! (Include failed?)</p>";
} elseif (!$conn) {
    echo "<p style='color:red'>❌ Database Connection Failed!</p>";
    echo "<b>Error Message:</b> " . mysqli_connect_error() . "<br>";
    echo "<b>Tip:</b> Check your DB Host, User, and Password in db.php line 17-20.";
} else {
    echo "<p style='color:green'>✅ Database Connected Successfully!</p>";
    
    echo "<h3>3. Checking Tables</h3>";
    $result = mysqli_query($conn, "SHOW TABLES");
    if (!$result) {
        echo "Error fetching tables: " . mysqli_error($conn);
    } else {
        echo "<ul>";
        while($row = mysqli_fetch_array($result)) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
        if (mysqli_num_rows($result) == 0) {
            echo "<p style='color:orange'>⚠ Database is connected but NO TABLES were found. Did you import your SQL file?</p>";
        }
    }
}
?>
