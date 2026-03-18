<?php
require_once __DIR__ . '/header.php';

require_once TEMP . '/header.php';

require_once SRC . '/config/connection.php';
require_once SRC . '/intern/intern.php';
require_once SRC . '/user/user.php';
require_once SRC . '/auth/register.php';

flashMessage();
?>

<div class="min-h-screen w-full flex justify-center items-start py-8 overflow-y-auto bg-gray-50 overflow-auto max-h-screen">
    <div class="shadow-lg bg-white rounded-lg p-8 max-w-md w-full">
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Company Corporation</h1>
            <p class="text-gray-500 text-sm">OJT Daily Time Record Registration</p>
        </div>

        <!-- Register Form -->
        <form action="<?= htmlspecialchars(BASE_URL . '/register.php'); ?>" method="POST" class="space-y-4">

            <!-- Student ID -->
            <div>
                <label for="studentId" class="block text-sm font-medium text-gray-700 mb-1">
                    Student ID
                </label>
                <input
                    type="text"
                    name="studentId"
                    id="studentId"
                    placeholder="Enter student ID"
                    required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Name Row -->
            <div class="grid grid-cols-3 gap-3">

                <div>
                    <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">
                        First Name
                    </label>
                    <input
                        type="text"
                        name="firstName"
                        id="firstName"
                        required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="middleName" class="block text-sm font-medium text-gray-700 mb-1">
                        Middle Name
                    </label>
                    <input
                        type="text"
                        name="middleName"
                        id="middleName"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">
                        Last Name
                    </label>
                    <input
                        type="text"
                        name="lastName"
                        id="lastName"
                        required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Enter email"
                    required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- Required Hours -->
            <div>
                <label for="requiredHours" class="block text-sm font-medium text-gray-700 mb-1">
                    Required Internship Hours
                </label>
                <input
                    type="number"
                    name="requiredHours"
                    id="requiredHours"
                    value="500"
                    min="0"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Create password"
                    required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">
                    Confirm Password
                </label>
                <input
                    type="password"
                    name="confirmPassword"
                    id="confirmPassword"
                    placeholder="Confirm password"
                    required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- Register Button -->
            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">
                Register
            </button>

        </form>

        <!-- Login Link -->
        <p class="text-center text-sm text-gray-600 mt-4">
            Already have an account?
            <a href="<?= htmlspecialchars(BASE_URL . '/login.php'); ?>" class="text-blue-600 font-medium hover:underline">
                Login here
            </a>
        </p>

        <!-- Footer -->
        <p class="text-center text-xs text-gray-400 mt-6">
            &copy; <?= date('Y'); ?> Company Corporation. All rights reserved.
        </p>
    </div>
</div>

<?php require_once TEMP . '/footer.php'; ?>