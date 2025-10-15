<?php
require_once __DIR__ . '/../../includes/auth_admin.php';
require_once __DIR__ . '/../../config/duty_request_handler.php';
require_once __DIR__ . '/../../config/duty_approval_handler.php';

$message = $_GET['message'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico" />
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Admin Dashboard</title>
</head>

<body class="bg-gray-100">
  <div class="flex h-screen">

    <!-- Sidebar -->
    <div class="w-64 bg-white shadow-md flex flex-col justify-between">
      <div>
        <div class="text-center py-5 border-b">
          <h2 class="text-lg font-bold"><?php echo htmlspecialchars($logged_in_admin); ?></h2>
        </div>
        <nav class="p-4 space-y-2">
          <a href="dashboard.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">Dashboard</a>
          <a href="duty_approval.php" class="block px-4 py-2 bg-gray-200 rounded-lg font-medium hover:bg-gray-300">Duty
            Approval</a>
          <a href="duty_history.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">History</a>
          <a href="student_list.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">Student List</a>
        </nav>
      </div>
      <form action="/pages/auth/logout.php" method="POST" class="p-4">
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg">Log Out</button>
      </form>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-8 overflow-y-auto">
      <div class="bg-white p-6 rounded-xl shadow-sm">

        <!-- Header and Search -->
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold">Duty Requests List</h2>
          <form method="GET" id="filterForm" class="flex items-center space-x-3">
            <div class="relative">
              <input type="text" name="search_term" id="searchInput" placeholder="Search by name or id"
                value="<?php echo htmlspecialchars($search_term); ?>"
                class="border rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>

            <button type="submit"
              class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm shadow-md hover:shadow-lg">
              Search
            </button>

            <button type="button" onclick="window.location.href='duty_approval.php'"
              class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg text-sm shadow-md hover:shadow-lg">
              Clear
            </button>
          </form>
        </div>

        <!-- Message -->
        <?php if (!empty($message)): ?>
          <div
            class="mb-4 text-center font-medium <?= strpos($message, 'approved') !== false ? 'text-green-600' : 'text-red-500' ?>">
            <?= htmlspecialchars($message) ?>
          </div>
        <?php endif; ?>

        <!-- Duty Table -->
        <div class="overflow-hidden rounded-xl border">
          <table class="min-w-full text-sm text-left" id="studentTable">
            <thead class="bg-gray-100 border-b text-gray-600">
              <tr>
                <th class="py-3 px-4 font-bold">Student Name</th>
                <th class="py-3 px-4 font-bold">Student ID</th>
                <th class="py-3 px-4 font-bold">Date</th>
                <th class="py-3 px-4 font-bold">Time In</th>
                <th class="py-3 px-4 font-bold">Time Out</th>
                <th class="py-3 px-4 font-bold">Task Description</th>
                <th class="py-3 px-4 font-bold text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($pending_requests) > 0): ?>
                <?php foreach ($pending_requests as $request): ?>
                  <?php $fullName = htmlspecialchars(trim("{$request['last_name']}, {$request['first_name']}, {$request['middle_name']}")); ?>
                  <tr class="border-b hover:bg-gray-100 duty-row">
                    <td class="py-3 px-4 font-medium"><?php echo $fullName; ?></td>
                    <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($request['student_id']); ?>
                    </td>
                    <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($request['duty_date']); ?>
                    </td>
                    <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($request['time_in']); ?>
                    </td>
                    <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($request['time_out']); ?>
                    </td>
                    <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($request['remarks']); ?>
                    </td>
                    <td class="py-3 px-2 flex justify-center space-x-2">
                      <a href="../../config/duty_approval_handler.php?action=approve&id=<?php echo $request['id']; ?>"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-1 px-3 rounded-lg text-xs">
                        Approve
                      </a>
                      <a href="../../config/duty_approval_handler.php?action=reject&id=<?php echo $request['id']; ?>"
                        class="bg-red-500 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded-lg text-xs">
                        Reject
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr class="border-b">
                  <td colspan="7" class="py-3 px-4 text-center text-gray-500">No pending duty requests.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</body>

</html>