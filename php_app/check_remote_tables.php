<?php
$_SERVER['HTTP_HOST'] = 'remote';
include 'db.php';
$res = $conn->query("SHOW TABLES");
if (!$res) {
    die("Query failed: " . $conn->error);
}
while ($row = $res->fetch_row()) {
    echo $row[0] . "\n";
}
?>
