<?php
$search_id = $_GET['student_id'] ?? '';
$action_type = $_GET['action_type'] ?? 'search';
$duty_id = $_GET['id'] ?? null;
$message = '';

$admin_db_id = $logged_in_admin_id;
$admin_identifier = $logged_in_admin ?? 'UNKNOWN_ADMIN';

$pending_requests = [];

try {
  // --- 0. Approval / Rejection Logic ---
  if (($action_type === 'approve' || $action_type === 'reject') && !empty($duty_id)) {
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
    } elseif ($row_lookup['admin_id'] != $admin_db_id) {
      $message = "You are not authorized to review this duty.";
    } elseif ($row_lookup['status'] !== 'pending') {
      $message = "Duty request has already been reviewed.";
    } else {
      $new_status = $action_type === 'approve' ? 'approved' : 'rejected';
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
        ':admin_id' => $admin_db_id,
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
          ':approved_by' => $admin_db_id
        ]);
        $message = "Duty request approved and logged.";
      } else {
        $message = "Duty request rejected.";
      }

      $action_type = 'search';
    }
  }

  // --- 1. Retrieve Duty Requests for Current Admin ---
  $list_sql = "
    SELECT dr.id, dr.duty_date, dr.time_in, dr.time_out, dr.remarks, u.student_id,
           u.first_name, u.middle_name, u.last_name
    FROM duty_requests dr
    JOIN users_assigned ua ON dr.assigned_id = ua.assigned_id
    JOIN users u ON ua.student_id = u.id
    WHERE ua.admin_id = :admin_id AND dr.status = 'pending'
    " . (!empty($search_id) ? "AND u.student_id = :search_id" : "") . "
    ORDER BY dr.duty_date DESC, dr.time_in ASC
  ";

  $params = [':admin_id' => $admin_db_id];
  if (!empty($search_id)) {
    $params[':search_id'] = $search_id;
  }

  $stmt_list = $pdo->prepare($list_sql);
  $stmt_list->execute($params);
  $pending_requests = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  error_log("PDO Error: " . $e->getMessage());
  $message = "A database error occurred. Please try again.";
}
