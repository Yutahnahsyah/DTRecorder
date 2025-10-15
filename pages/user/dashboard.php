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
            <?php echo htmlspecialchars($logged_in_user); ?>
          </h2>
          <p class="text-gray-400"><?php echo htmlspecialchars($logged_in_student_id); ?></p>
        </div>

        <nav class="p-4 space-y-2">
          <a href="dashboard.php"
            class="block px-4 py-2 bg-gray-200 rounded-lg font-medium hover:bg-gray-300">Dashboard</a>
          <a href="duties_schedule.php" class="block px-4 py-2 hover:bg-gray-200">Duties & Schedule</a>
          <a href="history.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">History</a>
          <a href="my_information.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">My Information</a>
          <a href="time_logs.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">Time Logs</a>
        </nav>
      </div>

      <form action="/pages/auth/logout.php" method="POST" class="p-4">
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg">Log Out</button>
      </form>
    </div>

    <!-- Dashboard Content-->

  </div>
</body>

</html>