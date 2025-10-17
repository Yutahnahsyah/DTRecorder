<?php
require_once __DIR__ . '/../../includes/auth_user.php';
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
            <?= htmlspecialchars($logged_in_user); ?>
          </h2>
          <p class="text-gray-400"><?= htmlspecialchars($logged_in_student_id); ?></p>
        </div>

        <nav class="p-4 space-y-2">
          <a href="dashboard.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">Dashboard</a>
          <a href="duty_submission.php"
            class="block px-4 py-2 bg-gray-200 rounded-lg font-medium hover:bg-gray-300">Duty Submission</a>
          <a href="duty_history.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">History Overview</a>
          <a href="student_information.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">My Information</a>
        </nav>
      </div>

      <form action="/pages/auth/logout.php" method="POST" class="p-4">
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg">Log Out</button>
      </form>
    </div>

    <div class="flex-1 p-8 overflow-y-auto">
      <div class="bg-white p-6 rounded-xl shadow-sm max-w-xl mx-auto">
        <h2 class="text-xl font-bold mb-6">Submit Duty Record</h2>

        <?php if (!empty($_GET['message'])): ?>
          <?php
          $message = $_GET['message'];
          $isSuccess = stripos($message, 'success') !== false;
          $colorClass = $isSuccess ? 'text-green-600' : 'text-red-600';
          ?>
          <div class="mb-4 text-center text-sm font-medium <?= $colorClass ?>">
            <?= htmlspecialchars($message) ?>
          </div>
        <?php endif; ?>

        <form action="../../config/duty_submission_handler.php" method="POST" class="space-y-6">

          <div>
            <label for="duty_date" class="block text-sm font-medium leading-6 text-gray-900">Duty Date</label>
            <div class="mt-2">
              <input id="duty_date" name="duty_date" type="date" required min="1900-01-01" max="9999-12-31"
                class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
          </div>

          <div>
            <label for="time_in" class="block text-sm font-medium leading-6 text-gray-900">Time In</label>
            <div class="mt-2">
              <input id="time_in" name="time_in" type="time" required
                class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
          </div>


          <div>
            <label for="time_out" class="block text-sm font-medium leading-6 text-gray-900">Time Out</label>
            <div class="mt-2">
              <input id="time_out" name="time_out" type="time" required
                class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
          </div>

          <div>
            <label for="remarks" class="block text-sm font-medium leading-6 text-gray-900">Task Description</label>
            <div class="mt-2">
              <textarea id="remarks" name="remarks" rows="3" required
                class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
            </div>
          </div>

          <div>
            <button type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md">
              Submit Duty Record
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>