<?php require_once '../../config/login_handler.php'; ?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Login</title>
</head>

<body class="h-full">
  <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <img src="/assets/img/logo.png" alt="DTRecorder" class="mx-auto h-10 w-auto" />
      <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
        Sign in
      </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form action="" method="POST" class="space-y-6">
        <div>
          <label for="username" class="block text-sm font-medium leading-6 text-gray-900">Student ID / Email Address /
            Username</label>
          <div class="mt-2">
            <input id="username" type="text" name="username" 
                title="Must not start with a space" required
              class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
            <div class="text-sm">
              <a href="/pages/auth/forgot_password.php"
                class="font-semibold text-indigo-600 hover:text-indigo-500">Forgot password?</a>
            </div>
          </div>

          <div class="mt-2">
            <input id="password" type="password" name="password" pattern="^[A-Z][A-Za-z\s]*"
                title="Must not start with a space" required
              class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
          </div>
        </div>

        <div>
          <button type="submit"
            class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Sign in
          </button>
        </div>

        <?php if (!empty($error_message)): ?>
          <div class="mt-4 text-center text-sm text-red-600 font-medium">
            <?= htmlspecialchars($error_message) ?>
          </div>
        <?php endif; ?>
      </form>

      <p class="mt-10 text-center text-sm text-gray-500">
        Don't have an account?
        <a href="/pages/auth/register.php" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
          <br>Register here
        </a>
      </p>
    </div>
  </div>
</body>

</html>