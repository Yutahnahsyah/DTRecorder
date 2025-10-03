<?php
session_start();

require_once 'dbconfig.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header('Location: /pages/admin/register.php');
  exit;
}

$fn = $_POST['firstName'] ?? '';
$mn = $_POST['middleName'] ?? '';
$ln = $_POST['lastName'] ?? '';
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$sid = $_POST['studentId'] ?? '';
$pass = $_POST['password'] ?? '';
$c_pass = $_POST['confirm_password'] ?? '';

if (empty($fn) || empty($ln) || empty($email) || empty($sid) || empty($pass)) {
  exit("Error: All fields are required.");
}
if ($pass !== $c_pass) {
  exit("Error: Passwords do not match.");
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  exit("Error: Invalid email format.");
}

$sql = "INSERT INTO users (first_name, middle_name, last_name, email_address, student_id, password) 
        VALUES (?, ?, ?, ?, ?, ?)";

if ($stmt = mysqli_prepare($conn, $sql)) {

  mysqli_stmt_bind_param($stmt, "ssssss", $fn, $mn, $ln, $email, $sid, $pass);

  if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header('Location: /pages/auth/login.php?registration=success');
    exit;
  } else {
    $error_code = mysqli_errno($conn);

    if ($error_code == 1062) {
      exit("Error: The Email or Student ID is already in use.");
    } else {
      error_log("Registration Query Error: " . mysqli_error($conn));
      exit("An unexpected database error occurred. Please try again.");
    }
  }
} else {
  error_log("Registration Prepare Error: " . mysqli_error($conn));
  exit("An unexpected database error occurred. Please try again.");
}

if (isset($conn) && $conn) {
  mysqli_close($conn);
}
