<?php
require_once __DIR__ . '/../../includes/auth_admin.php';
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
          <a href="duty_approval.php" class="block px-4 py-2 bg-gray-200 rounded-lg font-medium hover:bg-gray-300">Duty
            Approval</a>
          <a href="duty_history.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">History</a>
          <a href="student_list.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">Student List</a>
        </nav>
      </div>
      <form action="#" method="POST" class="p-4">
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg">Log Out</button>
      </form>
    </div>

    <div class="flex-1 p-8 overflow-y-auto">
      <div class="bg-white p-6 rounded-xl shadow-sm">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h2 class="text-xl font-bold">Duties</h2>
          </div>

          <div class="relative">
            <input type="text" placeholder="Search"
              class="border rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>

        <div class="flex space-x-6 border-b mb-4">
          <button class="text-sm font-medium pb-2 border-b-2 border-black">All Duties</button>
          <button class="text-sm text-gray-500 hover:text-black">Available</button>
          <button class="text-sm text-gray-500 hover:text-black">Pending</button>
          <button class="text-sm text-gray-500 hover:text-black">Completed</button>
        </div>

        <div class="grid grid-cols-6 font-semibold text-sm text-gray-600 border-b pb-2 mb-2">
          <div>ID</div>
          <div>Name</div>
          <div>Course</div>
          <div>Date Published</div>
          <div>Deadline</div>
          <div class="text-center">Status</div>
        </div>

      </div>
    </div>
  </div>
</body>

</html>