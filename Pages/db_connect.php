<?php
$host = 'localhost';
$dbname = 'good_smile_db';
$username = 'root'; // Thay bằng username MySQL của bạn
$password = '';     // Thay bằng password MySQL của bạn

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
