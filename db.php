<?php
// $db_host = getenv('DB_HOST');
// $db_user = getenv('DB_USERNAME');
// $db_pass = getenv('DB_PASSWORD');
// $db_name = getenv('DB_DATABASE');
// $db_port = getenv('DB_PORT');

// try {
//     $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4";
//     $pdo = new PDO($dsn, $db_user, $db_pass);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     return $pdo;
// } catch (PDOException $e) {
//     die("Connection failed: " . $e->getMessage());
// }
?>

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
