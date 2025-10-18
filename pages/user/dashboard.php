<?php
require_once __DIR__ . '/../../includes/auth_user.php';
require_once __DIR__ . '/../../config/dbconfig.php';

$userId = $_SESSION['user_id'];

$sqlUser = "SELECT student_id FROM users WHERE id = :id";
$stmtUser = $pdo->prepare($sqlUser);
$stmtUser->execute(['id' => $userId]);
$studentRow = $stmtUser->fetch();
$studentId = $studentRow['student_id'];

$sqlAssignedIds = "
  SELECT assigned_id
  FROM users_assigned
  WHERE student_id = :student_id
";
$stmtAssignedIds = $pdo->prepare($sqlAssignedIds);
$stmtAssignedIds->execute(['student_id' => $userId]);
$assignedIds = $stmtAssignedIds->fetchAll(PDO::FETCH_COLUMN);


$totalSeconds = 0;

if (!empty($assignedIds)) {
  $placeholders = implode(',', array_fill(0, count($assignedIds), '?'));

  $sqlDuty = "
    SELECT TIMESTAMPDIFF(SECOND, time_in, time_out) AS duration_seconds
    FROM duty_logs
    WHERE assigned_id IN ($placeholders)
      AND time_in IS NOT NULL
      AND time_out IS NOT NULL
  ";
  $stmtDuty = $pdo->prepare($sqlDuty);
  $stmtDuty->execute($assignedIds);
  $rows = $stmtDuty->fetchAll();

  foreach ($rows as $row) {
    $totalSeconds += (int) $row['duration_seconds'];
  }
}


$hours = floor($totalSeconds / 3600);
$minutes = floor(($totalSeconds % 3600) / 60);

// Completed Duties
$completedCount = 0;

if (!empty($assignedIds)) {
  $placeholders = implode(',', array_fill(0, count($assignedIds), '?'));

  $sqlCompleted = "
    SELECT COUNT(*) AS total_completed
    FROM duty_requests
    WHERE status = 'approved'
      AND assigned_id IN ($placeholders)
  ";
  $stmtCompleted = $pdo->prepare($sqlCompleted);
  $stmtCompleted->execute($assignedIds);
  $completedRow = $stmtCompleted->fetch();
  $completedCount = $completedRow['total_completed'] ?? 0;
}

// Pending Logs
$pendingCount = 0;

if (!empty($assignedIds)) {
  $placeholders = implode(',', array_fill(0, count($assignedIds), '?'));

  $sqlPending = "
    SELECT COUNT(*) AS total_pending
    FROM duty_requests
    WHERE status = 'pending'
      AND assigned_id IN ($placeholders)
  ";
  $stmtPending = $pdo->prepare($sqlPending);
  $stmtPending->execute($assignedIds);
  $pendingRow = $stmtPending->fetch();
  $pendingCount = $pendingRow['total_pending'] ?? 0;
}

// Assigned Admin (active only)
$sqlAssignment = "
  SELECT admin_id
  FROM users_assigned
  WHERE student_id = :student_id AND is_active = 1
  LIMIT 1
";
$stmtAssignment = $pdo->prepare($sqlAssignment);
$stmtAssignment->execute(['student_id' => $userId]);
$assignmentRow = $stmtAssignment->fetch();

$adminId = $assignmentRow['admin_id'] ?? null;
$adminName = 'Unassigned';

if ($adminId) {
  $sqlAdmin = "SELECT username FROM admins WHERE id = :admin_id";
  $stmtAdmin = $pdo->prepare($sqlAdmin);
  $stmtAdmin->execute(['admin_id' => $adminId]);
  $adminRow = $stmtAdmin->fetch();
  $adminName = $adminRow['username'] ?? 'Unknown';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>User Dashboard</title>
</head>

<body class="bg-gray-100">
  <div class="flex h-screen">
    <div class="w-64 bg-white shadow-md flex flex-col justify-between">
      <div>
        <div class="text-center py-5 border-b">
          <h2 class="text-lg font-bold"><?php echo htmlspecialchars($logged_in_user); ?></h2>
          <p class="text-gray-400"><?php echo htmlspecialchars($logged_in_student_id); ?></p>
        </div>
        <nav class="p-4 space-y-2">
          <a href="dashboard.php"
            class="block px-4 py-2 bg-gray-200 rounded-lg font-medium hover:bg-gray-300">Dashboard</a>
          <a href="duty_submission.php" class="block px-4 py-2 hover:bg-gray-200">Duty Submission</a>
          <a href="duty_history.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">History Overview</a>
          <a href="student_information.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">My Information</a>
        </nav>
      </div>
      <form action="/pages/auth/logout.php" method="POST" class="p-4">
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg">Log Out</button>
      </form>
    </div>

    <div class="flex-1 p-8 overflow-y-auto">
      <h1 class="text-2xl font-bold mb-4">Welcome, <?php echo htmlspecialchars($logged_in_user); ?> 👋</h1>
      <p class="text-gray-600 mb-8">Here’s your duty overview and recent updates.</p>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-purple-500">
          <h3 class="text-gray-500 text-sm font-semibold">Assigned Department</h3>
          <p class="text-3xl font-bold mt-2 text-purple-600"><?php echo htmlspecialchars($adminName); ?></p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-indigo-500">
          <h3 class="text-gray-500 text-sm font-semibold">Total Duty Hours</h3>
          <p class="text-3xl font-bold mt-2 text-indigo-600">
            <?php echo "{$hours} hrs {$minutes} mins"; ?>
          </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-yellow-500">
          <h3 class="text-gray-500 text-sm font-semibold">Pending Logs</h3>
          <p class="text-3xl font-bold mt-2 text-yellow-600"><?php echo $pendingCount; ?></p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500">
          <h3 class="text-gray-500 text-sm font-semibold">Completed Duties</h3>
          <p class="text-3xl font-bold mt-2 text-green-600"><?php echo $completedCount; ?></p>
        </div>
      </div>
    </div>
  </div>
</body>

</html>