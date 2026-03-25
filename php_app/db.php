<?php
// Secure Database Connection for ScamShield (InfinityFree + Render Version)

$is_local = (stripos($_SERVER['HTTP_HOST'], 'localhost') !== false || $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || stripos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);

if ($is_local) {
    // 🏠 Local XAMPP Environment
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "scamshield_db";
    $api_base_url = "http://127.0.0.1:5000";

    // 🚀 Local Override (Create db_config_local.php to change these without affecting Git)
    if (file_exists(__DIR__ . '/db_config_local.php')) {
        include(__DIR__ . '/db_config_local.php');
    }

    try {
        mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);
        $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port ?? 3306);
    } catch (mysqli_sql_exception $e) {
        // Fallback for some Windows systems that handle 'localhost' vs '127.0.0.1' differently
        if ($db_host === "localhost") {
            try {
                $conn = mysqli_connect("127.0.0.1", $db_user, $db_pass, $db_name, $db_port ?? 3306);
                $db_host = "127.0.0.1"; // Successful fallback
            } catch (mysqli_sql_exception $e2) {
                $conn = false;
            }
        } else {
            $conn = false;
        }
    }
} else {
    // ☁️ Live Environment (InfinityFree + Render)
    // Please set these in your InfinityFree / Render dashboard
    $db_host = getenv('DB_HOST') ?: "mysql-24172f92-think-programming.l.aivencloud.com";
    $db_user = getenv('DB_USER') ?: "avnadmin";
    $db_pass = getenv('DB_PASS'); // NO HARDCODED PASSWORD (Security Policy)
    $db_name = getenv('DB_NAME') ?: "scamshield_db";
    $db_port = getenv('DB_PORT') ?: 27029;

    if (!$db_pass) {
        die("Error: DB_PASS is missing in your hosting environment variables.");
    }

    $conn = mysqli_init();
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL); 
    $success = @mysqli_real_connect($conn, $db_host, $db_user, $db_pass, $db_name, $db_port, NULL, MYSQLI_CLIENT_SSL);

    if (!$success) {
        $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
    }
    
    // Stable Render API
    $api_base_url = "https://scamshield-cplu.onrender.com";
}

if (!$conn) {
    echo "<div style='background: #fee; border: 1px solid #f99; padding: 20px; border-radius: 8px; font-family: sans-serif; max-width: 600px; margin: 40px auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1);'>
            <h2 style='color: #d33; margin-top: 0;'>❌ Database Connection Error</h2>
            <p>We couldn't connect to the MySQL database on <b>$db_host</b>.</p>
            <hr style='border: 0; border-top: 1px solid #fcc; margin: 15px 0;'>
            <p><b>How to fix this:</b></p>
            <ol>
                <li>Open <b>XAMPP Control Panel</b>.</li>
                <li>Ensure the <b>MySQL</b> module is <b>STARTED</b> (it should be green).</li>
                <li>Make sure you have created the database <b>$db_name</b> in phpMyAdmin.</li>
            </ol>
            <p style='font-size: 0.9em; color: #666;'>If you are using a non-standard port, you can change it in <code>php_app/db_config_local.php</code>.</p>
            <a href='index.php' style='display: inline-block; background: #d33; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-top: 10px;'>🔄 Retry Connection</a>
          </div>";
    die();
}
?>
