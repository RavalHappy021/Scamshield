<?php
$_SERVER['HTTP_HOST'] = 'localhost';
include "db.php";

// Database scamshield_db is default in db.php for localhost
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
