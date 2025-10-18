<?php
require_once __DIR__ . '/../../includes/auth_user.php';
require_once __DIR__ . '/../../config/dbconfig.php';
require_once __DIR__ . '/../../config/student_information_handler.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico" />
  <script src="https://cdn.tailwindcss.com"></script>
  <title>User Dashboard</title>
</head>

<body class="bg-gray-100">
  <div class="flex h-screen">

    <div class="w-64 bg-white shadow-md flex flex-col justify-between">
      <div>
        <div class="text-center py-5 border-b">
          <h2 class="text-lg font-bold"><?= htmlspecialchars($logged_in_user); ?></h2>
          <p class="text-gray-400"><?= htmlspecialchars($logged_in_student_id); ?></p>
        </div>
        <nav class="p-4 space-y-2">
          <a href="dashboard.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">Dashboard</a>
          <a href="duty_submission.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">Duty Submission</a>
          <a href="duty_history.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">History Overview</a>
          <a href="student_information.php"
            class="block px-4 py-2 bg-gray-200 rounded-lg font-medium hover:bg-gray-300">My Information</a>
        </nav>
      </div>

      <form action="/pages/auth/logout.php" method="POST" class="p-4">
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg">Log Out</button>
      </form>
    </div>

    <div class="flex-1 p-8 overflow-y-auto">
      <div class="bg-white p-6 rounded-xl shadow-sm max-w-xl mx-auto">
        <h2 class="text-xl font-bold mb-6">Personal Information</h2>

        <?php if ($success_message): ?>
          <?php
          $isSuccess = stripos($success_message, 'success') !== false;
          $colorClass = $isSuccess ? 'text-green-600' : 'text-red-600';
          ?>
          <div class="mb-4 text-center text-sm font-medium <?= $colorClass ?>">
            <?= htmlspecialchars($success_message) ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="student_information.php" class="space-y-6">
          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-900">Student ID</label>
              <input type="text" name="student_id" value="<?= htmlspecialchars($user_data['student_id']) ?>" disabled
                class="mt-2 block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-900">Email</label>
              <input type="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" disabled
                class="mt-2 block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm" />
            </div>
          </div>

          <div class="grid grid-cols-3 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-900">First Name</label>
              <input type="text" name="first_name" value="<?= htmlspecialchars($user_data['first_name']) ?>" required
                class="mt-2 block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-900">Middle Name</label>
              <input type="text" name="middle_name" value="<?= htmlspecialchars($user_data['middle_name']) ?>"
                class="mt-2 block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-900">Last Name</label>
              <input type="text" name="last_name" value="<?= htmlspecialchars($user_data['last_name']) ?>" required
                class="mt-2 block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-900">Scholarship Type</label>
              <input type="text" name="scholarship_type" value="<?= htmlspecialchars($user_data['scholarship_type']) ?>"
                disabled
                class="mt-2 block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 bg-gray-100 shadow-sm ring-1 ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-900">Department</label>
              <input type="text" name="school_department"
                value="<?= htmlspecialchars($user_data['school_department']) ?>" disabled
                class="mt-2 block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 bg-gray-100 shadow-sm ring-1 ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm" />
            </div>
          </div>
          <div>
            <button type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md">
              Update Information
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>