<?php
require_once __DIR__ . '/../../includes/auth_user.php';

$date_filter = $_GET['date'] ?? '';
$status_filter = $_GET['status'] ?? '';
$duty_logs = [];

try {
  $sql = "
    SELECT dr.duty_date, dr.time_in, dr.time_out, dr.remarks, dr.status,
           u.student_id, u.first_name, u.middle_name, u.last_name
    FROM duty_requests dr
    JOIN users u ON dr.assigned_id = u.id
    WHERE dr.assigned_id = :user_id
    AND dr.status IN ('approved', 'rejected', 'pending')
      " . (!empty($status_filter) ? "AND dr.status = :status" : "") . "
      " . (!empty($date_filter) ? "AND dr.duty_date = :date" : "") . "
    ORDER BY dr.duty_date DESC, dr.time_in ASC
  ";

  $params = [':user_id' => $_SESSION['user_id']];
  if (!empty($status_filter)) {
    $params[':status'] = $status_filter;
  }
  if (!empty($date_filter)) {
    $params[':date'] = $date_filter;
  }

  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $duty_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  error_log("User Duty History Error: " . $e->getMessage());
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
          <h2 class="text-lg font-bold">
            <?php echo htmlspecialchars($logged_in_user); ?>
          </h2>
          <p class="text-gray-400"><?php echo htmlspecialchars($logged_in_student_id); ?></p>
        </div>

        <nav class="p-4 space-y-2">
          <a href="dashboard.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">Dashboard</a>
          <a href="duty_submission.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">Duty Submission</a>
          <a href="duty_history.php"
            class="block px-4 py-2 bg-gray-200 rounded-lg font-medium hover:bg-gray-300">History Overview</a>
          <a href="student_information.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">My Information</a>
        </nav>
      </div>

      <form action="/pages/auth/logout.php" method="POST" class="p-4">
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg">Log Out</button>
      </form>
    </div>

    <div class="flex-1 p-8 overflow-y-auto">
      <div class="bg-white p-6 rounded-xl shadow-sm">

        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold">Personal Duty History Log</h2>
          <form method="GET" id="filterForm" class="flex items-center space-x-3">
            <input type="date" name="date" id="dateInput" value="<?= htmlspecialchars($date_filter) ?>"
              class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />

            <input type="hidden" name="status" id="statusInput" value="<?= htmlspecialchars($status_filter) ?>" />

            <button type="submit"
              class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm shadow-md hover:shadow-lg">
              Filter
            </button>

            <button type="button" onclick="setStatusAndSubmit('approved')"
              class="px-4 py-2 rounded-lg text-sm font-semibold shadow-md
          <?= $status_filter === 'approved' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-green-100' ?>">
              Approved
            </button>

            <button type="button" onclick="setStatusAndSubmit('pending')"
              class="px-4 py-2 rounded-lg text-sm font-semibold shadow-md
          <?= $status_filter === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-yellow-100' ?>">
              Pending
            </button>

            <button type="button" onclick="setStatusAndSubmit('rejected')"
              class="px-4 py-2 rounded-lg text-sm font-semibold shadow-md
          <?= $status_filter === 'rejected' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-red-100' ?>">
              Rejected
            </button>

            <button type="button" onclick="window.location.href='duty_history.php'"
              class="px-4 py-2 rounded-lg text-sm font-semibold bg-gray-300 text-gray-800 hover:bg-gray-400 shadow-md">
              Clear
            </button>
          </form>
        </div>

        <div class="overflow-hidden rounded-xl border">
          <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 border-b text-gray-600">
              <tr>
                <th class="py-3 px-4 font-bold">Student Name</th>
                <th class="py-3 px-4 font-bold">Student ID</th>
                <th class="py-3 px-4 font-bold">Date</th>
                <th class="py-3 px-4 font-bold">Time In</th>
                <th class="py-3 px-4 font-bold">Time Out</th>
                <th class="py-3 px-4 font-bold">Task Description</th>
                <th class="py-3 px-4 font-bold text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($duty_logs as $log): ?>
                <?php $fullName = htmlspecialchars(trim("{$log['last_name']}, {$log['first_name']}, {$log['middle_name']}")); ?>
                <tr class="border-b hover:bg-gray-100">
                  <td class="py-3 px-4 font-medium"><?= $fullName ?></td>
                  <td class="py-3 px-4 font-medium"><?= htmlspecialchars($log['student_id']) ?></td>
                  <td class="py-3 px-4 font-medium"><?= htmlspecialchars($log['duty_date']) ?></td>
                  <td class="py-3 px-4 font-medium"><?= htmlspecialchars($log['time_in']) ?></td>
                  <td class="py-3 px-4 font-medium"><?= htmlspecialchars($log['time_out']) ?></td>
                  <td class="py-3 px-4 font-medium"><?= htmlspecialchars($log['remarks']) ?></td>
                  <td class="py-3 px-4 flex justify-center">
                    <?php
                    $status = $log['status'];
                    $badgeClass = match ($status) {
                      'approved' => 'bg-green-100 text-green-700',
                      'rejected' => 'bg-red-100 text-red-700',
                      'pending' => 'bg-yellow-100 text-yellow-700',
                      default => 'bg-gray-100 text-gray-700'
                    };
                    ?>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                      <?= ucfirst($status) ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($duty_logs)): ?>
                <tr>
                  <td colspan="7" class="py-3 px-4 text-center text-gray-500">No duty logs found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <script>
      function setStatusAndSubmit(status) {
        document.getElementById('statusInput').value = status;
        document.getElementById('filterForm').submit();
      }
    </script>

  </div>
</body>

</html>