<?php
$_SERVER['HTTP_HOST'] = 'localhost';
include "db.php";

$old_db = "scamshield_db";
$new_db = "scamshield_db_recovery";

echo "Attempting to swap...\n";

try {
    echo "Processing $old_db...\n";
    // Try dropping tables individually first
    $tables = ['users', 'job_history', 'job_checks', 'contact_messages'];
    foreach ($tables as $t) {
        echo "Dropping table $old_db.$t... ";
        try {
            $conn->query("DROP TABLE IF EXISTS $old_db.$t");
            echo "OK\n";
        } catch (Exception $e) {
            echo "FAIL: " . $e->getMessage() . "\n";
        }
    }
    
    echo "Dropping database $old_db... ";
    try {
        $conn->query("DROP DATABASE IF EXISTS $old_db");
        echo "OK\n";
    } catch (Exception $e) {
        echo "FAIL: " . $e->getMessage() . "\n";
    }

    echo "Creating database $old_db... ";
    $conn->query("CREATE DATABASE IF NOT EXISTS $old_db");
    echo "OK\n";

    foreach ($tables as $t) {
        echo "Moving $t to $old_db... ";
        try {
            $conn->query("RENAME TABLE $new_db.$t TO $old_db.$t");
            echo "OK\n";
        } catch (Exception $e) {
            echo "FAIL: " . $e->getMessage() . "\n";
        }
    }

} catch (Exception $e) {
    echo "GLOBAL ERROR: " . $e->getMessage() . "\n";
}
?>
