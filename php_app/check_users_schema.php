<?php
$_SERVER['HTTP_HOST'] = 'localhost';
include 'db.php';
$res = $conn->query("SELECT id, name, email, created_at, role FROM users LIMIT 5");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
