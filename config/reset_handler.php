<?php
session_start();
require_once 'dbconfig.php'; // Assumes $pdo is initialized here

$error_message = '';
$token = $_GET["token"] ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $token = $_POST["token"] ?? '';
  $password = $_POST["password"] ?? '';
  $password_confirmation = $_POST["password_confirmation"] ?? '';

  if (empty($token) || empty($password) || empty($password_confirmation)) {
    $error_message = "All fields are required.";
  } else {
    $token_hash = hash("sha256", $token);

    try {
      $sql = "SELECT * FROM users WHERE reset_token_hash = :token_hash";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([':token_hash' => $token_hash]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$user) {
        $error_message = "Token not found.";
      } elseif (strtotime($user["reset_token_expiration"]) <= time()) {
        $error_message = "Token has expired.";
      } elseif ($password !== $password_confirmation) {
        $error_message = "Passwords must match.";
      } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        if ($password_hash === false) {
          error_log("Password hashing failed for user ID: " . $user['id']);
          $error_message = "An internal error occurred. Please try again.";
        } else {
          $update_sql = "
            UPDATE users
            SET password_hash = :password_hash,
                reset_token_hash = NULL,
                reset_token_expiration = NULL
            WHERE id = :user_id
          ";
          $update_stmt = $pdo->prepare($update_sql);
          $update_stmt->execute([
            ':password_hash' => $password_hash,
            ':user_id' => $user['id']
          ]);

          header('Location: /pages/auth/login.php');
          exit;
        }
      }

    } catch (PDOException $e) {
      error_log("PDO Error: " . $e->getMessage());
      $error_message = "A database error occurred. Please try again.";
    }
  }
} else {
  // Validate token on initial GET
  if (empty($token)) {
    $error_message = "Token is missing.";
  } else {
    $token_hash = hash("sha256", $token);

    try {
      $sql = "SELECT * FROM users WHERE reset_token_hash = :token_hash";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([':token_hash' => $token_hash]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$user) {
        $error_message = "Token not found.";
      } elseif (strtotime($user["reset_token_expiration"]) <= time()) {
        $error_message = "Token has expired.";
      }
    } catch (PDOException $e) {
      error_log("PDO Error: " . $e->getMessage());
      $error_message = "A database error occurred. Please try again.";
    }
  }
}