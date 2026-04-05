<?php
/**
 * 🛠️ ScamShield Local Configuration Override
 * 
 * Use this file to set your local database credentials or connect to your Aiven remote DB from localhost.
 * This file is IGNORED by Git, so your passwords stay safe!
 */

// --- 💡 OPTION A: Use Local XAMPP (Make sure MySQL is START in XAMPP Control Panel) ---
$db_host = "localhost";
$db_user = "root";
$db_pass = ""; 
$db_name = "scamshield_db";
$db_port = 3306; // Default XAMPP port (Change to 3307 or 3308 if needed)

// --- ☁️ OPTION B: Use Remote Aiven DB from Localhost (Uncomment and fill in your password) ---
/*
$db_host = "mysql-24172f92-think-programming.l.aivencloud.com";
$db_user = "avnadmin";
$db_pass = "YOUR_AIVEN_PASSWORD_HERE"; 
$db_name = "scamshield_db";
$db_port = 27029;

// If using SSL for Aiven locally, you might need extra mysqli_real_connect logic here
// But for basic local testing, Option A is recommended.
*/

// --- 🐍 Python API (Make sure your Python API is running on this port) ---
$api_base_url = "http://127.0.0.1:5000";
?>
