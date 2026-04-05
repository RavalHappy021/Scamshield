<?php
include 'db.php';
$res = $conn->query("SELECT id, name FROM users WHERE role='admin' LIMIT 1");
$row = $res->fetch_assoc();
echo json_encode($row);
?>
