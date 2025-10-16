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
            class="block px-4 py-2 rounded-lg hover:bg-gray-200">Dashboard</a>
          <a href="duty_submission.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">Duty Submission</a>
          <a href="duty_history.php" class="block px-4 py-2 bg-gray-200 rounded-lg font-medium hover:bg-gray-300">History Overview</a>
          <a href="student_information.php" class="block px-4 py-2 rounded-lg hover:bg-gray-200">My Information</a>
        </nav>
      </div>

      <form action="/pages/auth/logout.php" method="POST" class="p-4">
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg">Log Out</button>
      </form>
    </div>

    <!-- Main content placeholder -->
    <main class="flex-1 p-8 overflow-y-auto">
      <!-- Add your dashboard content here -->
    </main>
  </div>
</body>

</html>