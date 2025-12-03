<?php
$host = '127.0.0.1';
$port = 3306;
$db   = 'retech'; // your local database name
$user = 'root';
$pass = ''; // XAMPP default password is empty

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
