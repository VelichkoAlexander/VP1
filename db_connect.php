<?php
$host = 'localhost';
$dbname = 'wp1';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

} catch (PDOException $e) {
    echo $e->getMessage();
    die;
}
