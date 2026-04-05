<?php
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
include "db.php";
$sql = "ALTER TABLE contact_messages MODIFY id INT(11) AUTO_INCREMENT";
if (mysqli_query($conn, $sql)) {
    echo "Table contact_messages successfully altered to include AUTO_INCREMENT.\n";
} else {
    echo "Error: " . mysqli_error($conn) . "\n";
}
?>
