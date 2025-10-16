<?php
session_start();
require_once 'dbconfig.php'; // Assumes $pdo is initialized here

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $fn = $_POST['firstName'] ?? '';
  $mn = $_POST['middleName'] ?? '';
  $ln = $_POST['lastName'] ?? '';
  $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
  $sid = $_POST['studentId'] ?? '';
  $pass = $_POST['password'] ?? '';
  $c_pass = $_POST['confirm_password'] ?? '';

  if (empty($fn) || empty($ln) || empty($email) || empty($sid) || empty($pass)) {
    $error_message = "All required fields must be filled out.";
  } elseif ($pass !== $c_pass) {
    $error_message = "Passwords do not match.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error_message = "Invalid email format.";
  } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@phinmaed\.com$/', $email)) {
    $error_message = "Email must end with @phinmaed.com.";
  } else {
    $password_hash = password_hash($pass, PASSWORD_DEFAULT);

    if ($password_hash === false) {
      error_log("Password hashing failed for user: " . $email);
      $error_message = "An internal server error occurred during registration.";
    } else {
      try {
        $sql = "INSERT INTO users (first_name, middle_name, last_name, email_address, student_id, password_hash) 
                VALUES (:fn, :mn, :ln, :email, :sid, :pass_hash)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          ':fn' => $fn,
          ':mn' => $mn,
          ':ln' => $ln,
          ':email' => $email,
          ':sid' => $sid,
          ':pass_hash' => $password_hash
        ]);

        $user_id = $pdo->lastInsertId();

        $info_sql = "INSERT INTO users_info (user_id, department_id, scholarship_id) VALUES (:user_id, NULL, NULL)";
        $stmt_info = $pdo->prepare($info_sql);
        $stmt_info->execute([':user_id' => $user_id]);

        header('Location: /pages/auth/login.php');
        exit;

      } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
          $error_message = "The Email or Student ID is already in use.";
        } else {
          error_log("Registration Query Error: " . $e->getMessage());
          $error_message = "An unexpected database error occurred. Please try again.";
        }
      }
    }
  }
}