<?php
$student_data = null;
$search_id = $_GET['student_id'] ?? '';
$action_type = $_GET['action_type'] ?? 'search';
$student_db_id_to_delete = $_GET['student_db_id'] ?? null;
$message = '';

$admin_db_id = $logged_in_admin_id;
$admin_identifier = $logged_in_admin ?? 'UNKNOWN_ADMIN';

$current_admin_students = [];

try {
  // --- 0. Delete Logic ---
  if ($action_type === 'delete' && !empty($student_db_id_to_delete)) {
    $delete_sql = "DELETE FROM users_assigned WHERE admin_id = :admin_id AND student_id = :student_id";
    $stmt = $pdo->prepare($delete_sql);
    $stmt->execute([
      ':admin_id' => $admin_db_id,
      ':student_id' => $student_db_id_to_delete
    ]);

    if ($stmt->rowCount() > 0) {
      $student_id_display = htmlspecialchars($student_db_id_to_delete); // fallback
      $lookup_sql = "SELECT student_id FROM users WHERE id = :id";
      $stmt_lookup = $pdo->prepare($lookup_sql);
      $stmt_lookup->execute([':id' => $student_db_id_to_delete]);
      $row_lookup = $stmt_lookup->fetch(PDO::FETCH_ASSOC);
      if ($row_lookup) {
        $student_id_display = htmlspecialchars($row_lookup['student_id']);
      }

      $message = "Student [{$student_id_display}] successfully unassigned from " . htmlspecialchars($admin_identifier) . ".";
    } else {
      $message = "Student registration not found or already deleted.";
    }

    $action_type = 'list';
  }

  // --- 1. Registration Logic (ADD Action) ---
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
        // Fetch admin name
        $admin_sql = "SELECT username FROM admins WHERE id = :admin_id LIMIT 1";
        $stmt_admin = $pdo->prepare($admin_sql);
        $stmt_admin->execute([':admin_id' => $registered_admin_id]);
        $admin_row = $stmt_admin->fetch(PDO::FETCH_ASSOC);

        $assigned_by = $admin_row ? htmlspecialchars($admin_row['username']) : "Unknown Admin";

        $message = "Student [" . htmlspecialchars($search_id) . "] is already assigned by " . $assigned_by . ".";
      } else {
        $insert_sql = "INSERT INTO users_assigned (admin_id, student_id) VALUES (:admin_id, :student_id)";
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
  }

  // --- 2. Current Student Lookup  ---
  if (!empty($search_id) && $action_type === 'search') {
    $lookup_sql = "
    SELECT 
      u.id AS user_db_id,
      u.first_name, u.middle_name, u.last_name, u.student_id, u.email_address,
      d.department_name, s.scholarship_name
    FROM users u
    JOIN users_assigned ua ON u.id = ua.student_id
    LEFT JOIN users_info ui ON u.id = ui.user_id
    LEFT JOIN departments d ON ui.department_id = d.id
    LEFT JOIN scholarship_types s ON ui.scholarship_id = s.id
    WHERE u.student_id = :student_id AND ua.admin_id = :admin_id
    LIMIT 1
  ";
    $stmt = $pdo->prepare($lookup_sql);
    $stmt->execute([
      ':student_id' => $search_id,
      ':admin_id' => $admin_db_id
    ]);
    $student_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student_data) {
      if (empty($message)) {
        $message = "Student data retrieved for review.";
      }
    } else {
      if (empty($message)) {
        $message = "Student [" . htmlspecialchars($search_id) . "] is not assigned here.";
      }
    }
  }

  // --- 3. Retrieve Student List for Current Admin ---
  $list_sql = "
    SELECT
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