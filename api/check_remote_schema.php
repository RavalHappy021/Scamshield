<?php
$_SERVER['HTTP_HOST'] = 'remote';
include 'db.php';
$res = $conn->query("DESCRIBE job_history");
if (!$res) {
    die("Query failed: " . $conn->error);
}
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
