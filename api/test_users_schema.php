<?php
$_SERVER['HTTP_HOST'] = 'localhost';
include "db.php";

$temp_db = "scamshield_db_recovery";
$backup_dir = "C:\\xampp\\mysql\\data\\scamshield_db_backup";
$mysql_data_dir = "C:\\xampp\\mysql\\data\\$temp_db";
$conn->select_db($temp_db);

$schemas = [
    "v1_with_role" => "CREATE TABLE `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) DEFAULT NULL,
      `email` varchar(100) DEFAULT NULL,
      `password` varchar(255) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `role` varchar(20) DEFAULT 'user',
      PRIMARY KEY (`id`),
      UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
    
    "v2_no_role" => "CREATE TABLE `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) DEFAULT NULL,
      `email` varchar(100) DEFAULT NULL,
      `password` varchar(255) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    "v3_simple_no_unique" => "CREATE TABLE `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) DEFAULT NULL,
      `email` varchar(100) DEFAULT NULL,
      `password` varchar(255) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    "v4_mariadb_default" => "CREATE TABLE `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) DEFAULT NULL,
      `email` varchar(100) DEFAULT NULL,
      `password` varchar(255) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `role` varchar(20) DEFAULT 'user',
      PRIMARY KEY (`id`),
      UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

foreach ($schemas as $ver => $sql) {
    echo "Testing $ver... ";
    $conn->query("DROP TABLE IF EXISTS users");
    $conn->query($sql);
    $conn->query("ALTER TABLE users DISCARD TABLESPACE");
    copy("$backup_dir\\users.ibd", "$mysql_data_dir\\users.ibd");
    try {
        if ($conn->query("ALTER TABLE users IMPORT TABLESPACE")) {
            echo "SUCCESS!\n";
            break;
        } else {
            echo "FAILED: " . $conn->error . "\n";
        }
    } catch (Exception $e) {
        echo "EXC: " . $e->getMessage() . "\n";
    }
}
?>
