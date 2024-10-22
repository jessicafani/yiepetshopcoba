<?php
$servername = "localhost"; // atau alamat server database Anda
$username = "root"; // username database Anda
$password = ""; // password database Anda
$dbname = "db_yie"; // nama database Anda

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);
// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
