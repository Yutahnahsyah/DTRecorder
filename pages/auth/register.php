<?php require_once '../../config/register_handler.php'; ?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Register</title>
</head>

<body class="h-full">
  <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <img src="/assets/img/logo.png" alt="DTRecorder" class="mx-auto h-10 w-auto" />
      <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
        Register your account
      </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form action="" method="POST" class="space-y-6">
        <div class="flex gap-x-4">
          <div class="flex-1">
            <label for="firstName" class="block text-sm/6 font-medium text-gray-900">First Name <span
                class="text-red-500">*</span></label>
            <div class="mt-2">
              <input id="firstName" type="text" name="firstName" pattern="[A-Za-z\s]+" title="Letters only" required
                class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
          </div>
          <div class="flex-1">
            <label for="lastName" class="block text-sm/6 font-medium text-gray-900">Last Name <span
                class="text-red-500">*</span></label>
            <div class="mt-2">
              <input id="lastName" type="text" name="lastName" pattern="[A-Za-z\s]+" title="Letters only" required
                class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
            </div>
          </div>
        </div>

        <div>
          <label for="middleName" class="block text-sm/6 font-medium text-gray-900">Middle Name (Optional)</label>
          <div class="mt-2">
            <input id="middleName" type="text" name="middleName" pattern="[A-Za-z\s]+" title="Letters only"
              class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
          </div>
        </div>

        <div>
          <label for="email" class="block text-sm/6 font-medium text-gray-900">Email Address <span
              class="text-red-500">*</span></label>
          <div class="mt-2">
            <input id="email" type="email" name="email" required
              class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
          </div>
        </div>

        <div>
          <label for="studentId" class="block text-sm/6 font-medium text-gray-900">Student Id <span
              class="text-red-500">*</span></label>
          <div class="mt-2">
            <input id="studentId" type="text" name="studentId" required
              class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label for="password" class="block text-sm/6 font-medium text-gray-900">Password <span
                class="text-red-500">*</span></label>
          </div>
          <div class="mt-2">
            <input id="password" type="password" name="password" required
              class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label for="confirm_password" class="block text-sm/6 font-medium text-gray-900">Confirm Password <span
                class="text-red-500">*</span></label>
          </div>
          <div class="mt-2">
            <input id="confirm_password" type="password" name="confirm_password" required
              class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
          </div>
        </div>

        <div>
          <button type="submit"
            class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Register
          </button>
        </div>

        <?php if (!empty($error_message)): ?>
          <div class="mt-4 text-center text-sm text-red-600 font-medium">
            <?= htmlspecialchars($error_message) ?>
          </div>
        <?php endif; ?>
      </form>

      <p class="mt-10 text-center text-sm text-gray-600">
        Already have an account?
        <a href="/pages/auth/login.php" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
          <br>Sign in here</a>
      </p>
    </div>
  </div>
</body>

</html>