<?php
require_once 'dbconfig.php';

$output_message = '';
$message_type = ''; // 'success' or 'error'

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = $_POST["email"] ?? "";

  $token = bin2hex(random_bytes(16));
  $token_hash = hash("sha256", $token);
  date_default_timezone_set('Asia/Manila');
  $expiry = date("Y-m-d H:i:s", time() + 60 * 30); // 30 minutes from now

  try {
    $sql = "UPDATE users
              SET reset_token_hash = :token_hash,
                  reset_token_expiration = :expiry
              WHERE email_address = :email";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':token_hash' => $token_hash,
      ':expiry' => $expiry,
      ':email' => $email
    ]);

    if ($stmt->rowCount() > 0) {
      $mail = require 'mailer.php';

      $mail->setFrom("noreply@dtrecorder.com");
      $mail->addAddress($email);
      $mail->Subject = "Password Reset";
      $mail->Body = <<<END
        Click <a href="http://127.0.0.1:5500/pages/auth/confirm_forgot_password.php?token=$token">here</a>
        to reset your password.
      END;

      try {
        $mail->send();
        $output_message = "Email sent successfully. Please check your inbox.";
        $message_type = "success";
      } catch (Exception $e) {
        error_log("Mailer error for $email: {$mail->ErrorInfo}");
        $output_message = "An error occurred while sending the email. Please contact support.";
        $message_type = "error";
      }
    } else {
      $output_message = "No account found with that email address.";
      $message_type = "error";
    }

  } catch (PDOException $e) {
    error_log("PDO Error: " . $e->getMessage());
    $output_message = "A database error occurred. Please try again.";
    $message_type = "error";
  }
}
?>