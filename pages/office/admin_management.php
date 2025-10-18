<?php
require_once __DIR__ . '/../../includes/auth_admin.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico" />
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Office Dashboard</title>
</head>

<body class="bg-gray-100">
  <div class="flex h-screen">

    <div class="w-64 bg-white shadow-md flex flex-col justify-between">
      <div>
        <div class="text-center py-5 border-b">
          <h2 class="text-lg font-bold"><?php echo htmlspecialchars($logged_in_admin); ?></h2>
        </div>
        <nav class="p-4 space-y-2">
          <a href="dashboard.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">Dashboard</a>
          <a href="duty_approval.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">Duty Approval</a>
          <a href="duty_history.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">History</a>
          <a href="student_list.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">Student List</a>
          <a href="admin_management.php"
            class="block px-4 py-2 bg-gray-200 rounded-lg font-medium hover:bg-gray-300">Admin Management</a>
        </nav>
      </div>
      <form action="/pages/auth/logout.php" method="POST" class="p-4">
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg">Log Out</button>
      </form>
    </div>

    <div class="flex-1 p-8 overflow-y-auto">
      <div class="bg-white p-6 rounded-xl shadow-sm max-w-md mx-auto">
        <h2 class="text-xl font-bold mb-6">Create New Admin</h2>

        <?php if (!empty($_GET['message'])): ?>
          <?php
          $message = $_GET['message'];
          $isSuccess = str_starts_with($message, 'Success:');
          $colorClass = $isSuccess ? 'text-green-600' : 'text-red-600';
          ?>
          <div class="mb-4 text-center text-sm font-medium <?= $colorClass ?>">
            <?= htmlspecialchars(substr($message, strpos($message, ':') + 1)) ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="/../../config/admin_management_handler.php" class="space-y-6">
          <div class="flex gap-4">
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-900">Username</label>
              <input type="text" name="username" required
                class="mt-2 block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm" />
            </div>

            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-900">Password</label>
              <input type="password" name="password" required
                class="mt-2 block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm" />
            </div>
          </div>

          <div>
            <button type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md">
              Create Admin
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>