<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: /pages/auth/login.php");
  exit();
}

require_once '../../config/dbconfig.php'; // Assumes $pdo is initialized

$logged_in_user_id = $_SESSION['user_id'];

try {
  $sql = "SELECT first_name, middle_name, last_name, student_id FROM users WHERE id = :id LIMIT 1";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':id' => $logged_in_user_id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    // Admin no longer exists — force logout
    session_destroy();
    header("Location: /pages/auth/login.php");
    exit();
  }

  $logged_in_user = trim("{$user['first_name']} {$user['middle_name']} {$user['last_name']}");
  $logged_in_student_id = $user['student_id'];

} catch (PDOException $e) {
  error_log("User session validation error: " . $e->getMessage());
  header("Location: /pages/auth/login.php");
  exit();
}
