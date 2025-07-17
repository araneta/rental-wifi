<?php
$host = "localhost";
$user = "root";
$pass = "willamette";
$dbname = "rentalwifi";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
