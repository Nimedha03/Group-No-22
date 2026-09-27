<?php
// ---------------------------------------------------------
// Database connection (XAMPP default MySQL credentials)
// Edit these if your MySQL setup is different.
// ---------------------------------------------------------
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "docshare_db";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error() .
        "<br>Make sure XAMPP's MySQL is running and you imported database.sql.");
}
?>
