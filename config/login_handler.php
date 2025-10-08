<?php
session_start();
require_once 'dbconfig.php'; // Assumes $pdo is initialized here

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  if (empty($username) || empty($password)) {
    $error_message = "Both fields are required.";
  } else {
    try {
      // --- Check Admin Table ---
      $sql_admin = "SELECT id, username, password FROM admins WHERE username = :username";
      $stmt_admin = $pdo->prepare($sql_admin);
      $stmt_admin->execute([':username' => $username]);
      $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);

      if ($admin && $password === $admin['password']) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['user_name'] = $admin['username'];
        $_SESSION['logged_in'] = true;
        $_SESSION['is_admin'] = true;

        header('Location: /pages/admin/dashboard.php');
        exit;
      }

      // --- Check User Table ---
      $sql_user = "SELECT id, password_hash, first_name, last_name, middle_name
                   FROM users
                   WHERE email_address = :username OR student_id = :username
                   LIMIT 1";
      $stmt_user = $pdo->prepare($sql_user);
      $stmt_user->execute([':username' => $username]);
      $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

      if ($user && password_verify($password, $user['password_hash'])) {
        $middle = trim($user['middle_name'] ?? '');
        $fullName = $user['first_name'] . ' ' . ($middle ? $middle . ' ' : '') . $user['last_name'];

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $fullName;
        $_SESSION['logged_in'] = true;
        $_SESSION['is_admin'] = false;

        header('Location: /pages/user/dashboard.php');
        exit;
      } else {
        $error_message = "Invalid login credentials.";
      }

    } catch (PDOException $e) {
      error_log("PDO Error: " . $e->getMessage());
      $error_message = "An unexpected error occurred. Please try again.";
    }
  }
}