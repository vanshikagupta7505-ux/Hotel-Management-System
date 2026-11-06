<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$host = "127.0.0.1";
$port = 3306;            // XAMPP me aksar 3306 ya 3307
$user = "root";
$pass = "";
$db   = "hotel_db";

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>