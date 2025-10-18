<?php
$search_id = $_GET['student_id'] ?? '';
$action_type = $_SERVER['REQUEST_METHOD'] === 'POST'
  ? ($_POST['action_type'] ?? '')
  : ($_GET['action_type'] ?? 'search');
$assigned_id_to_delete = $_GET['assigned_id'] ?? null;
$message = '';

$admin_db_id = $logged_in_admin_id;
$admin_identifier = $logged_in_admin ?? 'UNKNOWN_ADMIN';

$current_admin_students = [];

$departments = $pdo->query("SELECT id, department_name FROM departments")->fetchAll(PDO::FETCH_ASSOC);
$scholarships = $pdo->query("SELECT id, scholarship_name FROM scholarship_types")->fetchAll(PDO::FETCH_ASSOC);

try {
  // --- 0. Delete Logic ---
  if ($action_type === 'delete' && !empty($assigned_id_to_delete)) {
    try {
      $pdo->beginTransaction();

      // 1. Lookup student_id for display and rejection
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

      // 2. Soft-delete assignment
      $delete_sql = "UPDATE users_assigned
      SET is_active = 0
      WHERE assigned_id = :assigned_id
      ";
      $stmt_delete = $pdo->prepare($delete_sql);
      $stmt_delete->execute([':assigned_id' => $assigned_id_to_delete]);

      // 3. Reject all pending duty requests for this student
      $reject_sql = "
      UPDATE duty_requests
      SET status = 'rejected',
          reviewed_at = NOW(),
          reviewed_by = :admin_id
      WHERE assigned_id = :assigned_id
        AND status = 'pending'
      ";
      $stmt_reject = $pdo->prepare($reject_sql);
      $stmt_reject->execute([
        ':assigned_id' => $assigned_id_to_delete,
        ':admin_id' => $admin_db_id
      ]);

      $pdo->commit();

      $message = ($stmt_delete->rowCount() > 0)
        ? "Student [{$student_id_display}] successfully removed from " . htmlspecialchars($admin_identifier)
        : "Assignment not found or already deleted.";

    } catch (PDOException $e) {
      $pdo->rollBack();
      error_log("Unassign Error: " . $e->getMessage());
      $message = "Failed to unassign student and reject pending requests.";
    }

    header("Location: student_list.php?message=" . urlencode($message));
    exit;
  }

  // --- 1. Registration Logic (assign student to admin) ---
  if ($action_type === 'add' && !empty($search_id)) {
    $check_sql = "
  SELECT u.id AS user_db_id, ua.admin_id AS registered_admin_id
  FROM users u
  LEFT JOIN users_assigned ua ON u.id = ua.student_id AND ua.is_active = 1
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
        // Check if student was previously assigned to this admin (reactivate) or create new
        $reuse_sql = "
  SELECT assigned_id FROM users_assigned
  WHERE student_id = :student_id AND admin_id = :admin_id AND is_active = 0
  ORDER BY assigned_at DESC LIMIT 1
  ";
        $stmt_reuse = $pdo->prepare($reuse_sql);
        $stmt_reuse->execute([
          ':student_id' => $user_id,
          ':admin_id' => $admin_db_id
        ]);
        $reuse_row = $stmt_reuse->fetch(PDO::FETCH_ASSOC);

        if ($reuse_row) {
          // Reactivate existing assignment
          $reactivate_sql = "
    UPDATE users_assigned
    SET is_active = 1, assigned_at = NOW()
    WHERE assigned_id = :assigned_id
    ";
          $stmt_reactivate = $pdo->prepare($reactivate_sql);
          $stmt_reactivate->execute([':assigned_id' => $reuse_row['assigned_id']]);

          // Ensure users_info exists (NULLs if unassigned)
          $ensure_ui = $pdo->prepare("
            INSERT INTO users_info (user_id, department_id, scholarship_id)
            VALUES (:user_id, NULL, NULL)
            ON DUPLICATE KEY UPDATE user_id = user_id
          ");
          $ensure_ui->execute([':user_id' => $user_id]);

          $message = "Student [" . htmlspecialchars($search_id) . "] was previously assigned under " . htmlspecialchars($admin_identifier) . ". Reusing existing assignment.";
        } else {
          // Create new assignment
          $insert_sql = "INSERT INTO users_assigned (admin_id, student_id, assigned_at) VALUES (:admin_id, :student_id, NOW())";
          $stmt_insert = $pdo->prepare($insert_sql);
          $stmt_insert->execute([
            ':admin_id' => $admin_db_id,
            ':student_id' => $user_id
          ]);

          // Ensure users_info row exists for this user (NULLs for unassigned)
          $ensure_ui = $pdo->prepare("
            INSERT INTO users_info (user_id, department_id, scholarship_id)
            VALUES (:user_id, NULL, NULL)
            ON DUPLICATE KEY UPDATE user_id = user_id
          ");
          $ensure_ui->execute([':user_id' => $user_id]);

          $message = "Student [" . htmlspecialchars($search_id) . "] successfully assigned under " . htmlspecialchars($admin_identifier) . ".";
        }
      }
    } else {
      $message = "No student found with ID: " . htmlspecialchars($search_id) . ". Cannot register.";
    }

    header("Location: student_list.php?message=" . urlencode($message));
    exit;
  }

  // --- 1.5 Update Department or Scholarship ---
  if ($action_type === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $assigned_id = $_POST['assigned_id'] ?? '';

    // detect presence of keys (even if empty string was submitted)
    $has_dept_key = array_key_exists('department_id', $_POST);
    $has_sch_key = array_key_exists('scholarship_id', $_POST);

    $raw_dept = $has_dept_key ? $_POST['department_id'] : null;
    $raw_sch = $has_sch_key ? $_POST['scholarship_id'] : null;

    // Normalize: explicit empty string => NULL (Unassigned)
    $department_id = ($raw_dept === '' || $raw_dept === null) ? null : intval($raw_dept);
    $scholarship_id = ($raw_sch === '' || $raw_sch === null) ? null : intval($raw_sch);

    try {
      // Update department if the key was present in POST
      if ($has_dept_key) {
        if ($department_id !== null) {
          $stmt = $pdo->prepare("
            UPDATE users_info ui
            JOIN users_assigned ua ON ui.user_id = ua.student_id
            SET ui.department_id = :dept_id
            WHERE ua.assigned_id = :assigned_id
          ");
          $stmt->bindValue(':dept_id', $department_id, PDO::PARAM_INT);
          $stmt->bindValue(':assigned_id', $assigned_id, PDO::PARAM_INT);
          $stmt->execute();
          $message = "Department updated successfully.";
        } else {
          // explicit Unassigned -> set NULL
          $stmt = $pdo->prepare("
            UPDATE users_info ui
            JOIN users_assigned ua ON ui.user_id = ua.student_id
            SET ui.department_id = NULL
            WHERE ua.assigned_id = :assigned_id
          ");
          $stmt->execute([':assigned_id' => $assigned_id]);
          $message = "Department set to Unassigned.";
        }
      }

      // Update scholarship if the key was present in POST
      if ($has_sch_key) {
        if ($scholarship_id !== null) {
          $stmt = $pdo->prepare("
            UPDATE users_info ui
            JOIN users_assigned ua ON ui.user_id = ua.student_id
            SET ui.scholarship_id = :sch_id
            WHERE ua.assigned_id = :assigned_id
          ");
          $stmt->bindValue(':sch_id', $scholarship_id, PDO::PARAM_INT);
          $stmt->bindValue(':assigned_id', $assigned_id, PDO::PARAM_INT);
          $stmt->execute();
          $message = "Scholarship updated successfully.";
        } else {
          // explicit Unassigned -> set NULL
          $stmt = $pdo->prepare("
            UPDATE users_info ui
            JOIN users_assigned ua ON ui.user_id = ua.student_id
            SET ui.scholarship_id = NULL
            WHERE ua.assigned_id = :assigned_id
          ");
          $stmt->execute([':assigned_id' => $assigned_id]);
          $message = "Scholarship set to Unassigned.";
        }
      }

      header("Location: student_list.php?message=" . urlencode($message));
      exit;

    } catch (PDOException $e) {
      error_log("Update Error: " . $e->getMessage());
      $message = "Failed to update student information.";
      header("Location: student_list.php?message=" . urlencode($message));
      exit;
    }
  }

  // --- 2. Retrieve Student List for Current Admin ---
  $list_sql = "
  SELECT
    ua.assigned_id,
    u.id AS user_db_id,
    u.first_name,
    u.middle_name,
    u.last_name,
    u.student_id,
    ui.department_id,
    ui.scholarship_id,
    d.department_name,
    s.scholarship_name,
    SUM(TIMESTAMPDIFF(MINUTE, dr.time_in, dr.time_out)) AS total_minutes
  FROM users u
  JOIN users_assigned ua ON u.id = ua.student_id
  LEFT JOIN users_info ui ON u.id = ui.user_id
  LEFT JOIN departments d ON ui.department_id = d.id
  LEFT JOIN scholarship_types s ON ui.scholarship_id = s.id
  LEFT JOIN duty_requests dr ON dr.assigned_id = ua.assigned_id AND dr.status = 'approved'
  WHERE ua.admin_id = :admin_id AND ua.is_active = 1
  GROUP BY ua.assigned_id, u.id, u.first_name, u.middle_name, u.last_name, u.student_id,
           ui.department_id, ui.scholarship_id, d.department_name, s.scholarship_name
  ORDER BY u.last_name, u.first_name
  ";

  $stmt_list = $pdo->prepare($list_sql);
  $stmt_list->execute([':admin_id' => $admin_db_id]);
  $current_admin_students = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  error_log("PDO Error: " . $e->getMessage());
  $message = "A database error occurred. Please try again.";
}
