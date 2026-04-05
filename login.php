<?php
require_once __DIR__ . '/header.php';

require_once SRC . '/auth/logout.php';
require_once SRC . '/config/connection.php';
require_once SRC . '/intern/intern.php';
require_once SRC . '/user/user.php';
require_once SRC . '/auth/login.php';

require_once TEMP . '/header.php';

flashMessage();
?>

<div class="min-h-screen w-full flex justify-center items-center overflow-y-auto">
    <div class="shadow-lg bg-white rounded-lg p-8 max-w-md my-10 w-full">

        <!-- Company Name -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Company Corporation</h1>
            <p class="text-gray-500 text-sm">OJT Daily Time Record System</p>
        </div>

        <!-- Login Form -->
        <form class="space-y-4" action="<?= e(BASE_URL . '/login.php', false); ?>" method="POST">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <input
                    type="email"
                    placeholder="Enter your email"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-4 focus:ring-blue-200 duration-100"
                    id="email"
                    name="email">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input
                    type="password"
                    placeholder="Enter your password"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-4 focus:ring-blue-200 duration-100"
                    id="password"
                    name="password">
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input
                        name="rememberMe"
                        type="checkbox"
                        class="accent-blue-600">
                    Remember me
                </label>

                <a href="#" class="text-blue-600 hover:underline">
                    Forgot password?
                </a>
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition cursor-pointer">
                Login
            </button>

        </form>

        <!-- Registration Link -->
        <p class="text-center text-sm text-gray-600 mt-4">
            Don't have an account?
            <a href="<?= e(BASE_URL . '/register.php', false); ?>" class="text-blue-600 font-medium hover:underline">
                Register here
            </a>
        </p>

        <!-- Footer -->
        <p class="text-center text-xs text-gray-400 mt-6">
            &copy; 2026 Company Corporation. All rights reserved.
        </p>

    </div>
</div>

<?php require_once TEMP . '/footer.php'; ?>