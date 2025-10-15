<?php
$search_id = $_GET['student_id'] ?? '';
$action_type = $_GET['action_type'] ?? 'search';
$assigned_id_to_delete = $_GET['assigned_id'] ?? null;
$message = '';

$admin_db_id = $logged_in_admin_id;
$admin_identifier = $logged_in_admin ?? 'UNKNOWN_ADMIN';

$current_admin_students = [];

try {
  // --- 0. Delete Logic ---
  if ($action_type === 'delete' && !empty($assigned_id_to_delete)) {
    $lookup_sql = "
      SELECT u.student_id
      FROM users_assigned ua
      JOIN users u ON ua.student_id = u.id
      WHERE ua.assigned_id = :assigned_id
      LIMIT 1
    ";
    $stmt_lookup = $pdo->prepare($lookup_sql);
    $stmt_lookup->execute([':assigned_id' => $assigned_id_to_delete]);
    $row_lookup = $stmt_lookup->fetch(PDO::FETCH_ASSOC);
    $student_id_display = $row_lookup ? htmlspecialchars($row_lookup['student_id']) : 'UNKNOWN';

    $delete_sql = "DELETE FROM users_assigned WHERE assigned_id = :assigned_id";
    $stmt = $pdo->prepare($delete_sql);
    $stmt->execute([':assigned_id' => $assigned_id_to_delete]);

    $message = ($stmt->rowCount() > 0)
      ? "Student [{$student_id_display}] successfully removed from " . htmlspecialchars($admin_identifier) . "."
      : "Assignment not found or already deleted.";

    header("Location: student_list.php?message=" . urlencode($message));
    exit;
  }

  // --- 1. Registration Logic ---
  if ($action_type === 'add' && !empty($search_id)) {
    $check_sql = "
      SELECT u.id AS user_db_id, ua.admin_id AS registered_admin_id
      FROM users u
      LEFT JOIN users_assigned ua ON u.id = ua.student_id
      WHERE u.student_id = :student_id
      LIMIT 1
    ";
    $stmt_check = $pdo->prepare($check_sql);
    $stmt_check->execute([':student_id' => $search_id]);
    $row_check = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($row_check) {
      $user_id = $row_check['user_db_id'];
      $registered_admin_id = $row_check['registered_admin_id'];

      if ($registered_admin_id) {
        $admin_sql = "SELECT username FROM admins WHERE id = :admin_id LIMIT 1";
        $stmt_admin = $pdo->prepare($admin_sql);
        $stmt_admin->execute([':admin_id' => $registered_admin_id]);
        $admin_row = $stmt_admin->fetch(PDO::FETCH_ASSOC);
        $assigned_by = $admin_row ? htmlspecialchars($admin_row['username']) : "Unknown Admin";

        $message = "Student [" . htmlspecialchars($search_id) . "] is already assigned by " . $assigned_by . ".";
      } else {
        $insert_sql = "INSERT INTO users_assigned (admin_id, student_id, assigned_at) VALUES (:admin_id, :student_id, NOW())";
        $stmt_insert = $pdo->prepare($insert_sql);
        $stmt_insert->execute([
          ':admin_id' => $admin_db_id,
          ':student_id' => $user_id
        ]);
        $message = "Student [" . htmlspecialchars($search_id) . "] successfully assigned under " . htmlspecialchars($admin_identifier) . ".";
      }
    } else {
      $message = "No student found with ID: " . htmlspecialchars($search_id) . ". Cannot register.";
    }

    header("Location: student_list.php?message=" . urlencode($message));
    exit;
  }

  // --- 2. Retrieve Student List for Current Admin ---
  $list_sql = "
    SELECT
      ua.assigned_id,
      u.id AS user_db_id,
      u.first_name, u.middle_name, u.last_name, u.student_id,
      d.department_name, s.scholarship_name
    FROM users u
    JOIN users_assigned ua ON u.id = ua.student_id
    LEFT JOIN users_info ui ON u.id = ui.user_id
    LEFT JOIN departments d ON ui.department_id = d.id
    LEFT JOIN scholarship_types s ON ui.scholarship_id = s.id
    WHERE ua.admin_id = :admin_id
    ORDER BY u.last_name, u.first_name
  ";
  $stmt_list = $pdo->prepare($list_sql);
  $stmt_list->execute([':admin_id' => $admin_db_id]);
  $current_admin_students = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  error_log("PDO Error: " . $e->getMessage());
  $message = "A database error occurred. Please try again.";
}
