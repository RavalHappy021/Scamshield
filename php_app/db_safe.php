<?php
// SIMPLE DB CONNECTION FOR INFINITYFREE
// No smart detection - just the facts to avoid server blocks.

$db_host = "sql200.infinityfree.com"; 
$db_user = "if0_41241884";            
$db_pass = "hPtOV955QP3PaC";          
$db_name = "if0_41241884_myproject";    

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    echo "Connection failed!";
    exit();
}

$api_base_url = "https://your-python-api.onrender.com"; 
?>
