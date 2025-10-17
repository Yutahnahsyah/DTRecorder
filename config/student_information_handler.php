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

$success_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $student_id = trim($_POST['student_id']);
  $last_name = trim($_POST['last_name']);
  $first_name = trim($_POST['first_name']);
  $middle_name = trim($_POST['middle_name']);
  $email = trim($_POST['email']);
  $department_id = intval($_POST['department_id']);
  $scholarship_id = intval($_POST['scholarship_id']);

  $valid_dept = $pdo->prepare("SELECT COUNT(*) FROM departments WHERE id = :id");
  $valid_dept->execute([':id' => $department_id]);
  $valid_dept = $valid_dept->fetchColumn();

  $valid_sch = $pdo->prepare("SELECT COUNT(*) FROM scholarship_types WHERE id = :id");
  $valid_sch->execute([':id' => $scholarship_id]);
  $valid_sch = $valid_sch->fetchColumn();

  if ($valid_dept == 0 || $valid_sch == 0) {
    $success_message = "Invalid department or scholarship selected.";
  } else {
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

    $_SESSION['student_id'] = $student_id;
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name'] = $last_name;
    $_SESSION['email'] = $email;
  }
}

// Re-fetch latest user data after update or initial load
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
  $user_data = array_merge($user_data, $row);
}

$departments = $pdo->query("SELECT id, department_name FROM departments ORDER BY department_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$scholarships = $pdo->query("SELECT id, scholarship_name FROM scholarship_types ORDER BY scholarship_name ASC")->fetchAll(PDO::FETCH_ASSOC);
