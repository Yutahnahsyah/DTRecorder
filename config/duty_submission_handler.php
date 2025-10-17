<?php
session_start();
require_once __DIR__ . '/../includes/auth_user.php'; // ensures session and user_id are set
require_once __DIR__ . '/../config/dbconfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userId = $logged_in_user_id ?? null;

  $duty_date = $_POST['duty_date'] ?? '';
  $time_in = $_POST['time_in'] ?? '';
  $time_out = $_POST['time_out'] ?? '';
  $remarks = $_POST['remarks'] ?? '';

  // Check if student is assigned
  $assignmentCheck = $pdo->prepare("
  SELECT COUNT(*) FROM users_assigned 
  WHERE student_id = :student_id
  ");
  $assignmentCheck->execute([':student_id' => $userId]);
  $isAssigned = $assignmentCheck->fetchColumn();

  if ($isAssigned == 0) {
    header("Location: /pages/user/duty_submission.php?message=" . urlencode("Duty submission is invalid since you are not assigned."));
    exit;
  }

  // Basic validation
  if (!$userId || !$duty_date || !$time_in || !$time_out || !$remarks) {
    header("Location: /pages/user/duty_submission.php?message=" . urlencode("All fields are required."));
    exit;
  }

  // Combine date and time into full datetime strings
  $timeInFull = $duty_date . ' ' . $time_in . ':00';   // e.g., 2025-10-17 14:30:00
  $timeOutFull = $duty_date . ' ' . $time_out . ':00';

  try {
    $pdo->beginTransaction();

    $insert_sql = "
      INSERT INTO duty_requests (
        assigned_id, duty_date, time_in, time_out, remarks, status, submitted_at
      ) VALUES (
        :assigned_id, :duty_date, :time_in, :time_out, :remarks, 'pending', NOW()
      )
    ";

    $stmt = $pdo->prepare($insert_sql);
    $stmt->execute([
      ':assigned_id' => $userId,
      ':duty_date' => $duty_date,
      ':time_in' => $timeInFull,
      ':time_out' => $timeOutFull,
      ':remarks' => $remarks
    ]);

    $pdo->commit();
    header("Location: /pages/user/duty_submission.php?message=" . urlencode("Duty record submitted successfully."));
    exit;

  } catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Duty Submission Error: " . $e->getMessage());
    header("Location: /pages/user/duty_submission.php?message=" . urlencode("Submission failed. Please try again."));
    exit;
  }
} else {
  header("Location: /pages/user/duty_submission.php");
  exit;
}
