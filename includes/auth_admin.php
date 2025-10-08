<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
  header("Location: /pages/auth/login.php");
  exit();
}

require_once '../../config/dbconfig.php'; // Assumes $pdo is initialized

$logged_in_admin_id = $_SESSION['user_id'];

try {
  $sql = "SELECT username FROM admins WHERE id = :id LIMIT 1";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':id' => $logged_in_admin_id]);
  $admin = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$admin) {
    // Admin no longer exists — force logout
    session_destroy();
    header("Location: /pages/auth/login.php");
    exit();
  }

  $logged_in_admin = $admin['username'];

} catch (PDOException $e) {
  error_log("Admin session validation error: " . $e->getMessage());
  header("Location: /pages/auth/login.php");
  exit();
}
