<?php
require_once __DIR__ . '/../../includes/auth_admin.php';
require_once __DIR__ . '/../../config/dbconfig.php';
require_once __DIR__ . '/../../config/student_list_handler.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Admin Dashboard</title>
</head>

<body class="bg-gray-100">
  <div class="flex h-screen">

    <div class="w-64 bg-white shadow-md flex flex-col justify-between">
      <div>
        <div class="text-center py-5 border-b">
          <h2 class="text-lg font-bold">
            <?php echo htmlspecialchars($logged_in_admin); ?>
          </h2>
        </div>
        <nav class="p-4 space-y-2">
          <a href="dashboard.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">Dashboard</a>
          <a href="duty_approval.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">Duty Approval</a>
          <a href="duty_history.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">History</a>
          <a href="student_list.php"
            class="block px-4 py-2 bg-gray-200 rounded-lg font-medium hover:bg-gray-300">Student List</a>
        </nav>
      </div>
      <form action="/pages/auth/logout.php" method="POST" class="p-4">
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg">Log
          Out</button>
      </form>
    </div>

    <div class="flex-1 p-8 overflow-y-auto">
      <div class="bg-white p-6 rounded-xl shadow-sm">

        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold">Student List</h2>

          <form method="GET" action="student_list.php" id="studentForm" class="flex items-center space-x-3">
            <input type="hidden" name="action_type" id="actionType" value="search">
            <div class="relative">
              <input type="text" name="student_id" placeholder="Enter Student-ID"
                value="<?php echo htmlspecialchars($search_id); ?>"
                class="border rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>

            <button type="submit" onclick="setAction('search')"
              class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition duration-150 ease-in-out shadow-md hover:shadow-lg">
              Search
            </button>

            <button type="submit" onclick="setAction('add')"
              class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition duration-150 ease-in-out shadow-md hover:shadow-lg">
              Add
            </button>
          </form>
        </div>

        <div class="overflow-hidden rounded-xl border">
          <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 border-b text-gray-600">
              <tr>
                <th class="py-3 px-4 font-semibold">Student Name</th>
                <th class="py-3 px-4 font-semibold">Student ID</th>
                <th class="py-3 px-4 font-semibold">Department</th>
                <th class="py-3 px-4 font-semibold">Scholarship</th>
                <th class="py-3 px-4 font-semibold">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($message)): ?>
                <tr class="border-b">
                  <td colspan="5"
                    class="py-3 px-4 text-center <?php echo strpos($message, 'successfully') !== false ? 'text-green-600' : 'text-red-500'; ?> font-medium">
                    <?php echo nl2br(htmlspecialchars($message)); ?>
                  </td>
                </tr>
              <?php endif; ?>

              <?php if ($student_data): ?>
                <tr class="border-b hover:bg-gray-50">
                  <td class="py-3 px-4 font-medium text-gray-800">
                    <?php echo htmlspecialchars($student_data['first_name'] . ' ' . $student_data['middle_name'] . ' ' . $student_data['last_name']); ?>
                  </td>
                  <td class="py-3 px-4 text-gray-600">
                    <?php echo htmlspecialchars($student_data['student_id']); ?>
                  </td>
                  <td class="py-3 px-4 text-gray-600">
                    <?php echo htmlspecialchars($student_data['department_name']); ?>
                  </td>
                  <td class="py-3 px-4 text-gray-600">
                    <?php echo htmlspecialchars($student_data['scholarship_name']); ?>
                  </td>
                  <td class="py-3 px-4 text-gray-600">
                  </td>
                </tr>
              <?php endif; ?>

              <?php if (count($current_admin_students) > 0): ?>
                <?php foreach ($current_admin_students as $student):
                  $fullName = htmlspecialchars($student['first_name'] . ', ' . $student['middle_name'] . ', ' . $student['last_name']);
                  ?>
                  <tr class="border-b hover:bg-gray-100">
                    <td class="py-2 px-4 font-medium text-gray-800">
                      <?php echo $fullName; ?>
                    </td>
                    <td class="py-2 px-4 text-gray-600">
                      <?php echo htmlspecialchars($student['student_id']); ?>
                    </td>
                    <td class="py-2 px-4 text-gray-600">
                      <?php echo htmlspecialchars($student['department_name']); ?>
                    </td>
                    <td class="py-2 px-4 text-gray-600">
                      <?php echo htmlspecialchars($student['scholarship_name']); ?>
                    </td>
                    <td class="py-3 px-2">
                      <a href="student_list.php?action_type=delete&student_db_id=<?php echo htmlspecialchars($student['user_db_id']); ?>"
                        onclick="return confirmDelete('<?php echo addslashes($fullName); ?>')"
                        class="bg-red-500 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded-lg text-xs transition duration-150 ease-in-out">
                        Delete
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr class="border-b">
                  <td colspan="5" class="py-3 px-4 text-center text-gray-500">
                    No students are currently assigned.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <script>
      function setAction(action) {
        document.getElementById('actionType').value = action;
      }
      function confirmDelete(studentName) {
        return confirm(`Are you sure you want to unassign ${studentName}? This action will remove them from your list.`);
      }
    </script>
</body>

</html>