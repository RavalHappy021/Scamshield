<?php
// Database Connectivity Test Tool for InfinityFree
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🛠 ScamShield Database Debugger</h1>";

$host = $_SERVER['HTTP_HOST'];
echo "<p><b>Host detected:</b> $host</p>";

include_once "db.php";

echo "<h2>Checking Connection Variables:</h2>";
echo "<ul>";
echo "<li><b>DB Host:</b> " . ($db_host ?? "Not set") . "</li>";
echo "<li><b>DB User:</b> " . ($db_user ?? "Not set") . "</li>";
echo "<li><b>DB Name:</b> " . ($db_name ?? "Not set") . "</li>";
echo "</ul>";

if ($conn) {
    echo "<h3 style='color: green;'>✅ Connection Successful!</h3>";
    echo "<p>PHP reached the database server successfully.</p>";
    
    // Check if table exists
    $res = @$conn->query("SHOW TABLES LIKE 'users'");
    if ($res && $res->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table 'users' exists.</p>";
    } else {
        echo "<p style='color: red;'>❌ Table 'users' NOT found! Did you import the SQL file?</p>";
    }
} else {
    echo "<h3 style='color: red;'>❌ Connection Failed!</h3>";
    echo "<p><b>Error Message:</b> " . mysqli_connect_error() . "</p>";
    echo "<h3>How to fix:</h3>";
    echo "<ul>";
    echo "<li>Double check your username (starts with if0_) and password in <b>db.php</b>.</li>";
    echo "<li>Make sure you have created the database in your InfinityFree Control Panel.</li>";
    echo "<li>The database name must match EXACTLY (usually starts with if0_ followed by your account name and then the DB name).</li>";
    echo "</ul>";
}

echo "<hr><a href='index.php'>Go to Homepage</a>";
?>
