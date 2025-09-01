<?php
// config.php
$DB_HOST = 'localhost';
$DB_NAME = 'dbnfh2ssoad6ig';
$DB_USER = 'uulevslgtrnau';
$DB_PASS = 'ld4dy42tkorz';
 
try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo "DB Connection failed: " . htmlspecialchars($e->getMessage());
    exit;
}
 
