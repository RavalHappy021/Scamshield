<?php
$_SERVER['HTTP_HOST'] = 'localhost';
include "db.php";

$temp_db = "scamshield_db_recovery";
$conn->select_db($temp_db);

$tables = ['users', 'job_history', 'job_checks', 'contact_messages'];

foreach ($tables as $table) {
    echo "[$table]: ";
    try {
        $res = $conn->query("SELECT COUNT(*) as count FROM $table");
        if ($res) {
            echo "SUCCESS (" . $res->fetch_assoc()['count'] . " rows)\n";
        } else {
            echo "FAILED QUERY\n";
        }
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}
?>
