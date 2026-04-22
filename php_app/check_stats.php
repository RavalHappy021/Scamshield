<?php
include_once "db.php";
include_once "stats_helper.php";

echo "<h1>📊 System Diagnostic</h1>";

if ($conn) {
    echo "<p style='color:green'>✅ Database: CONNECTED</p>";
} else {
    echo "<p style='color:red'>❌ Database: FAILED (" . mysqli_connect_error() . ")</p>";
}

if (function_exists('getStats')) {
    echo "<p style='color:green'>✅ Stats Helper: FOUND</p>";
    $test_stats = getStats($conn);
    echo "<pre>Data found: "; print_r($test_stats); echo "</pre>";
} else {
    echo "<p style='color:red'>❌ Stats Helper: NOT FOUND (Function getStats missing)</p>";
}
?>
