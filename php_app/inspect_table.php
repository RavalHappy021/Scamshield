<?php
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
include "db.php";
$result = mysqli_query($conn, "DESCRIBE contact_messages");
$output = "";
while ($row = mysqli_fetch_assoc($result)) {
    $output .= print_r($row, true);
}
file_put_contents("schema_output.txt", $output);
echo "Output written to schema_output.txt\n";
?>
