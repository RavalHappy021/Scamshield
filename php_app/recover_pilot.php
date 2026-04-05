<?php
$_SERVER['HTTP_HOST'] = 'localhost';
include "db.php";

$temp_db = "scamshield_db_recovery";
$backup_dir = "C:\\xampp\\mysql\\data\\scamshield_db_backup";
$mysql_data_dir = "C:\\xampp\\mysql\\data\\$temp_db";

echo "Opening connection to MySQL...\n";
if (!$conn) die("Connection failed.\n");

$conn->query("CREATE DATABASE IF NOT EXISTS $temp_db");
$conn->select_db($temp_db);

function tryRestore($conn, $tableName, $schema, $source_ibd, $dest_ibd) {
    echo "--- Restoring table $tableName ---\n";
    $conn->query("DROP TABLE IF EXISTS $tableName");
    if (!$conn->query($schema)) {
        echo "Error creating table $tableName: " . $conn->error . "\n";
        return false;
    }
    
    echo "Discarding tablespace...\n";
    $conn->query("ALTER TABLE $tableName DISCARD TABLESPACE");
    
    echo "Copying $source_ibd to $dest_ibd...\n";
    if (!copy($source_ibd, $dest_ibd)) {
        echo "Error copying ibd file for $tableName.\n";
        return false;
    }
    
    echo "Importing tablespace...\n";
    try {
        if ($conn->query("ALTER TABLE $tableName IMPORT TABLESPACE")) {
            echo "SUCCESS: $tableName restored!\n";
            return true;
        } else {
            echo "FAILED: $tableName import failed: " . $conn->error . "\n";
            return false;
        }
    } catch (Exception $e) {
        echo "EXCEPTION: " . $e->getMessage() . "\n";
        return false;
    }
}

// Table: users
$schema_users = "CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$source_users = "$backup_dir\\users.ibd";
$dest_users = "$mysql_data_dir\\users.ibd";

if (!tryRestore($conn, "users", $schema_users, $source_users, $dest_users)) {
    echo "Retrying users without role column...\n";
    $schema_users_no_role = "CREATE TABLE `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) DEFAULT NULL,
      `email` varchar(100) DEFAULT NULL,
      `password` varchar(255) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    tryRestore($conn, "users", $schema_users_no_role, $source_users, $dest_users);
}

// Table: job_history
$schema_history = "CREATE TABLE `job_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_text` text DEFAULT NULL,
  `result` varchar(10) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `confidence` float DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$source_history = "$backup_dir\\job_history.ibd";
$dest_history = "$mysql_data_dir\\job_history.ibd";
tryRestore($conn, "job_history", $schema_history, $source_history, $dest_history);

?>
