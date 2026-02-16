<?php
$host = 'localhost';
$dbname = 'ezpay_db';
$username = 'root';
$password = ''; // Default XAMPP password is empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Attempt to create database if it doesn't exist
    try {
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = file_get_contents(__DIR__ . '/database_setup.sql');
        $pdo->exec($sql);
        // Reconnect to the new database
         $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
         $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e2) {
         die("Database connection failed: " . $e2->getMessage());
    }
}
?>
