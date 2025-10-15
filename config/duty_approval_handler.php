<?php
require_once 'dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$action_type = $_GET['action'] ?? '';
$duty_id = $_GET['id'] ?? null;
$message = '';
$logged_in_admin_id = $_SESSION['user_id'] ?? null;

if (($action_type === 'approve' || $action_type === 'reject') && !empty($duty_id)) {
  try {
    $lookup_sql = "
      SELECT dr.*, ua.admin_id
      FROM duty_requests dr
      JOIN users_assigned ua ON dr.assigned_id = ua.assigned_id
      WHERE dr.id = :duty_id
      LIMIT 1
    ";
    $stmt_lookup = $pdo->prepare($lookup_sql);
    $stmt_lookup->execute([':duty_id' => $duty_id]);
    $row_lookup = $stmt_lookup->fetch(PDO::FETCH_ASSOC);

    if (!$row_lookup) {
      $message = "Duty request not found.";
    } elseif ($row_lookup['status'] !== 'pending') {
      $message = "Duty request has already been reviewed.";
    } else {
      $new_status = $action_type === 'approve' ? 'approved' : 'rejected';

      $pdo->beginTransaction();

      $update_sql = "
        UPDATE duty_requests
        SET status = :status,
            reviewed_at = NOW(),
            reviewed_by = :admin_id
        WHERE id = :duty_id
      ";
      $stmt_update = $pdo->prepare($update_sql);
      $stmt_update->execute([
        ':status' => $new_status,
        ':admin_id' => $logged_in_admin_id,
        ':duty_id' => $duty_id
      ]);

      if ($new_status === 'approved') {
        $insert_sql = "
          INSERT INTO duty_logs (
            assigned_id, duty_date, time_in, time_out, remarks, approved_by, logged_at
          ) VALUES (
            :assigned_id, :duty_date, :time_in, :time_out, :remarks, :approved_by, NOW()
          )
        ";
        $stmt_insert = $pdo->prepare($insert_sql);
        $stmt_insert->execute([
          ':assigned_id' => $row_lookup['assigned_id'],
          ':duty_date' => $row_lookup['duty_date'],
          ':time_in' => $row_lookup['time_in'],
          ':time_out' => $row_lookup['time_out'],
          ':remarks' => $row_lookup['remarks'],
          ':approved_by' => $logged_in_admin_id
        ]);
        $message = "Duty request approved and logged.";
      } else {
        $message = "Duty request rejected.";
      }

      $pdo->commit();
      header("Location: /pages/admin/duty_approval.php?message=" . urlencode($message));
      exit;
    }
  } catch (PDOException $e) {
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    error_log("Duty Approval Error: " . $e->getMessage());
    $message = "A database error occurred. Please try again.";
  }
}
