<?php
include "db.php";
$query = mysqli_query($conn, "SELECT * FROM users WHERE name = 'happy raval'");
if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        print_r($row);
    }
} else {
    echo "User 'happy raval' not found.";
}
?>
