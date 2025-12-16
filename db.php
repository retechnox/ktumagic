<!-- $host = '127.0.0.1';
$port = 3306;
$db   = 'retech'; // your local database name
$user = 'root';
$pass = ''; // XAMPP default password is empty -->

<?php
// db.php (PDO version)
$db_host = getenv('DB_HOST')?:'127.0.0.1';
$db_user = getenv('DB_USERNAME')?:'root';
$db_pass = getenv('DB_PASSWORD')?:'';
$db_name = getenv('DB_DATABASE')?:retech ;
$db_port = getenv('DB_PORT') ?: '3306';

try {
    $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>