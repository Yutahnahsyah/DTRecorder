<?php
session_start();
require_once 'dbconfig.php';

// Restrict access to office admins only
if ($_SESSION['admin_category'] !== 'office') {
  header('Location: /pages/admin/dashboard.php');
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST['username'] ?? '');
  $password = trim($_POST['password'] ?? '');
  $category = 'personnel'; // Enforced role

  if (empty($username) || empty($password)) {
    header("Location: /pages/office/admin_management.php?message=Error: All fields are required.");
    exit;
  }

  try {
    // Check for duplicate username
    $check_sql = "SELECT COUNT(*) FROM admins WHERE username = :username";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([':username' => $username]);

    if ($check_stmt->fetchColumn() > 0) {
      header("Location: /pages/office/admin_management.php?message=Error: Username already exists.");
      exit;
    }

    // Insert new personnel admin
    $insert_sql = "INSERT INTO admins (username, password, category) VALUES (:username, :password, :category)";
    $insert_stmt = $pdo->prepare($insert_sql);
    $insert_stmt->execute([
      ':username' => $username,
      ':password' => $password, // Consider password_hash() for security
      ':category' => $category
    ]);

    header("Location: /pages/office/admin_management.php?message=Success: Personnel admin created.");
    exit;

  } catch (PDOException $e) {
    error_log("Admin creation error: " . $e->getMessage());
    header("Location: /pages/office/admin_management.php?message=Error: Something went wrong.");
    exit;
  }
}
?>
