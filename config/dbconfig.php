<?php
$dsn = "mysql:host=localhost;dbname=dtrecorder;charset=utf8mb4";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
  $pdo = new PDO($dsn, 'root', '', $options);
} catch (PDOException $e) {
  die("Database connection failed: " . $e->getMessage());
}