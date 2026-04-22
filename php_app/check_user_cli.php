<?php
// Secure Database Connection for CLI tests
$conn = mysqli_connect("localhost", "root", "", "scamshield_db");
if (!$conn) {
    die("Database Offline.");
}

$query = mysqli_query($conn, "SELECT name, email, role FROM users WHERE name = 'happy raval'");
if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        echo "User info: ";
        print_r($row);
    }
} else {
    echo "User 'happy raval' not found.";
}
?>
