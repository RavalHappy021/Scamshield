<?php
$_SERVER['HTTP_HOST'] = 'localhost';
include "db.php";

$temp_db = "scamshield_db_recovery";
$backup_dir = "C:\\xampp\\mysql\\data\\scamshield_db_backup";
$mysql_data_dir = "C:\\xampp\\mysql\\data\\$temp_db";

echo "Database: $temp_db\n";
$conn->query("CREATE DATABASE IF NOT EXISTS $temp_db");
$conn->select_db($temp_db);

function tryRestore($conn, $tableName, $schema, $source_ibd, $dest_ibd) {
    echo "Restoring $tableName: ";
    $conn->query("DROP TABLE IF EXISTS $tableName");
    if (!$conn->query($schema)) { return "ERR_CREATE: " . $conn->error; }
    $conn->query("ALTER TABLE $tableName DISCARD TABLESPACE");
    if (!copy($source_ibd, $dest_ibd)) { return "ERR_COPY"; }
    try {
        if ($conn->query("ALTER TABLE $tableName IMPORT TABLESPACE")) {
            $res = $conn->query("SELECT COUNT(*) as count FROM $tableName");
            $count = $res ? $res->fetch_assoc()['count'] : "??";
            return "SUCCESS ($count rows)";
        } else { return "FAILED: " . $conn->error; }
    } catch (Exception $e) { return "EXC: " . $e->getMessage(); }
}

$tables = [
    "users" => [
        "schema" => "CREATE TABLE `users` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(100) DEFAULT NULL,
          `email` varchar(100) DEFAULT NULL,
          `password` varchar(255) DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          `role` varchar(20) DEFAULT 'user',
          PRIMARY KEY (`id`),
          UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "alt_schema" => "CREATE TABLE `users` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(100) DEFAULT NULL,
          `email` varchar(100) DEFAULT NULL,
          `password` varchar(255) DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`),
          UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ],
    "job_history" => [
        "schema" => "CREATE TABLE `job_history` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `job_text` text DEFAULT NULL,
          `result` varchar(10) DEFAULT NULL,
          `reason` text DEFAULT NULL,
          `confidence` float DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          `user_id` int(11) DEFAULT NULL,
          `username` varchar(100) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ],
    "job_checks" => [
        "schema" => "CREATE TABLE `job_checks` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) DEFAULT NULL,
          `job_text` text DEFAULT NULL,
          `result` varchar(50) DEFAULT NULL,
          `confidence` float DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          `email` varchar(100) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ],
    "contact_messages" => [
        "schema" => "CREATE TABLE `contact_messages` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(100) NOT NULL,
            `subject` VARCHAR(200) NOT NULL,
            `message` TEXT NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ]
];

foreach ($tables as $name => $cfg) {
    $src = "$backup_dir\\$name.ibd";
    $dst = "$mysql_data_dir\\$name.ibd";
    $status = tryRestore($conn, $name, $cfg['schema'], $src, $dst);
    if (strpos($status, "FAILED") !== false && isset($cfg['alt_schema'])) {
        echo "Retrying $name... ";
        $status = tryRestore($conn, $name, $cfg['alt_schema'], $src, $dst);
    }
    echo "$status\n";
}
?>
