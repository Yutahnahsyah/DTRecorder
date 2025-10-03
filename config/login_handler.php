<?php
session_start();

require_once 'dbconfig.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header('Location: /pages/auth/login.php');
  exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
  die("Error: Both fields are required.");
}

$sql = "SELECT id, password, first_name, last_name, middle_name 
        FROM users 
        WHERE email_address = ? OR student_id = ?
        LIMIT 1";

if ($stmt = mysqli_prepare($conn, $sql)) {

  mysqli_stmt_bind_param($stmt, "ss", $username, $username);

  if (mysqli_stmt_execute($stmt)) {

    $result = mysqli_stmt_get_result($stmt);

    $user = mysqli_fetch_assoc($result);

    if ($user && $password === $user['password']) {

      $fullName = $user['first_name'] . ' ' . $user['last_name'];
      if (!empty($user['middle_name'])) {
        $middle = trim($user['middle_name']);
        if (!empty($middle)) {
          $fullName = $user['first_name'] . ' ' . $middle . ' ' . $user['last_name'];
        }
      }

      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_name'] = $fullName;
      $_SESSION['logged_in'] = true;

      mysqli_stmt_close($stmt);
      mysqli_close($conn);

      header('Location: /pages/admin/dashboard.php');
      exit;
    } else {
      mysqli_stmt_close($stmt);
      mysqli_close($conn);
      die("Error: Invalid username/ID or password.");
    }
  } else {
    error_log("Login Query Error: " . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    die("An unexpected error occurred. Please try again.");
  }
} else {
  error_log("Login Prepare Error: " . mysqli_error($conn));
  mysqli_close($conn);
  die("An unexpected error occurred. Please try again.");
}

if (isset($conn) && $conn) {
  mysqli_close($conn);
}
