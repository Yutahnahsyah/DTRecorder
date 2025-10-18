<?php
require_once __DIR__ . '/../includes/auth_user.php';
require_once __DIR__ . '/../config/dbconfig.php';

$user_id = $_SESSION['user_id'] ?? '';

$user_data = [
  'student_id' => '',
  'scholarship_type' => '',
  'last_name' => '',
  'first_name' => '',
  'middle_name' => '',
  'school_department' => '',
  'email' => '',
  'department_id' => '',
  'scholarship_id' => ''
];

$sql = "
SELECT 
    u.id,
    u.student_id,
    u.first_name,
    u.middle_name,
    u.last_name,
    u.email_address AS email,
    ui.department_id,
    ui.scholarship_id,
    d.department_name AS school_department,
    s.scholarship_name AS scholarship_type
FROM users u
LEFT JOIN users_info ui ON u.id = ui.user_id
LEFT JOIN departments d ON ui.department_id = d.id
LEFT JOIN scholarship_types s ON ui.scholarship_id = s.id
WHERE u.id = :user_id
";

// Re-fetch latest user data after update or initial load
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
  $user_data = array_merge($user_data, $row);
}

$success_message = ($_GET['message'] ?? '') === 'updated' ? "Your information has been successfully updated!" : "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $student_id = trim($_POST['student_id'] ?? $user_data['student_id']);
  $last_name = trim($_POST['last_name']);
  $first_name = trim($_POST['first_name']);
  $middle_name = trim($_POST['middle_name']);
  $email = trim($_POST['email'] ?? $user_data['email']);

  // Safely normalize department and scholarship IDs
  $raw_dept = $_POST['department_id'] ?? $user_data['department_id'];
  $raw_sch = $_POST['scholarship_id'] ?? $user_data['scholarship_id'];

  $department_id = ($raw_dept === '' || $raw_dept == 0 || $raw_dept === null) ? null : intval($raw_dept);
  $scholarship_id = ($raw_sch === '' || $raw_sch == 0 || $raw_sch === null) ? null : intval($raw_sch);

  // Validate only if values are present
  $valid_dept = true;
  $valid_sch = true;

  if ($department_id !== null) {
    $check_dept = $pdo->prepare("SELECT COUNT(*) FROM departments WHERE id = :id");
    $check_dept->execute([':id' => $department_id]);
    $valid_dept = $check_dept->fetchColumn() > 0;
  }

  if ($scholarship_id !== null) {
    $check_sch = $pdo->prepare("SELECT COUNT(*) FROM scholarship_types WHERE id = :id");
    $check_sch->execute([':id' => $scholarship_id]);
    $valid_sch = $check_sch->fetchColumn() > 0;
  }

  if (!$valid_dept || !$valid_sch) {
    $success_message = "Invalid department or scholarship selected.";
  } else {
    // Update users table
    $stmt = $pdo->prepare("
      UPDATE users 
      SET student_id = :student_id, last_name = :last_name, first_name = :first_name, 
          middle_name = :middle_name, email_address = :email 
      WHERE id = :user_id
    ");
    $stmt->execute([
      ':student_id' => $student_id,
      ':last_name' => $last_name,
      ':first_name' => $first_name,
      ':middle_name' => $middle_name,
      ':email' => $email,
      ':user_id' => $user_id
    ]);

    // Update users_info table
    $stmt = $pdo->prepare("
      UPDATE users_info 
      SET department_id = :department_id, scholarship_id = :scholarship_id 
      WHERE user_id = :user_id
    ");
    $stmt->execute([
      ':department_id' => $department_id,
      ':scholarship_id' => $scholarship_id,
      ':user_id' => $user_id
    ]);

    $success_message = "Your information has been successfully updated!";
    header("Location: student_information.php?message=updated");
    exit;
  }
}

// Fetch dropdown values
$departments = $pdo->query("SELECT id, department_name FROM departments ORDER BY department_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$scholarships = $pdo->query("SELECT id, scholarship_name FROM scholarship_types ORDER BY scholarship_name ASC")->fetchAll(PDO::FETCH_ASSOC);
