<?php
require_once __DIR__ . '/header.php';
requireLogin();

require_once SRC . '/config/connection.php';
require_once SRC . '/intern/intern.php';
require_once SRC . '/intern/edit-intern.php';

require_once TEMP . '/header.php';

flashMessage();
?>

<div class="min-h-screen bg-gray-100 flex w-full">

    <?php require_once TEMP . '/sidebar.php'; ?>

    <!-- Content -->
    <main class="flex-1 px-8 pt-4 pb-14 max-h-screen overflow-y-auto mb-6">

        <?php require_once TEMP . '/navbar.php'; ?>

        <?php if (empty($_GET['id'])): ?>
            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                Profile
            </h1>

            <!-- Profile Header -->
            <div class="bg-white rounded-lg shadow p-6 mb-6 flex items-center justify-between">

                <div class="flex items-center gap-6">

                    <!-- Avatar -->
                    <div class="w-14 h-14 rounded-full bg-blue-600 flex items-center justify-center text-white text-lg font-bold">
                        <?= htmlspecialchars($initials); ?>
                    </div>

                    <!-- Basic Info -->
                    <div>
                        <h2 class="text-md font-bold text-gray-800">
                            <?= htmlspecialchars($intern['first_name'] ?? ''); ?>
                            <?= htmlspecialchars($intern['middle_name'] ?? ''); ?>
                            <?= htmlspecialchars($intern['last_name'] ?? ''); ?>
                        </h2>

                        <p class="text-sm text-gray-500">
                            Student ID: <?= htmlspecialchars($intern['student_id'] ?? ''); ?>
                        </p>

                        <p class="text-sm text-gray-500">
                            <?= htmlspecialchars($intern['email'] ?? ''); ?>
                        </p>
                    </div>

                </div>

                <!-- Edit Button -->
                <a href="<?= htmlspecialchars(BASE_URL . '/profile.php?id=' . $intern['intern_id'] ?? ''); ?>"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                    Edit Profile
                </a>

            </div>


            <div class="grid md:grid-cols-2 gap-6">

                <!-- Personal Information -->
                <div class="bg-white rounded-lg shadow p-6">

                    <h3 class="text-lg font-semibold mb-4 text-gray-800">
                        Personal Information
                    </h3>

                    <div class="space-y-4 text-sm">

                        <div class="flex justify-between">
                            <span class="text-gray-500">First Name</span>
                            <span class="font-medium text-gray-800">
                                <?= htmlspecialchars($intern['first_name'] ?? ''); ?>
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Middle Name</span>
                            <span class="font-medium text-gray-800">
                                <?= htmlspecialchars($intern['middle_name'] ?? ''); ?>
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Last Name</span>
                            <span class="font-medium text-gray-800">
                                <?= htmlspecialchars($intern['last_name'] ?? ''); ?>
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Email</span>
                            <span class="font-medium text-gray-800">
                                <?= htmlspecialchars($intern['email'] ?? ''); ?>
                            </span>
                        </div>

                    </div>

                </div>


                <!-- Internship Information -->
                <div class="bg-white rounded-lg shadow p-6">

                    <h3 class="text-lg font-semibold mb-4 text-gray-800">
                        Internship Information
                    </h3>

                    <div class="space-y-4 text-sm">

                        <div class="flex justify-between">
                            <span class="text-gray-500">Required Hours</span>
                            <span class="font-medium text-gray-800">
                                <?= htmlspecialchars($intern['required_hours'] ?? ''); ?> hrs
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Completed Hours</span>
                            <span class="font-medium text-gray-800">120 hrs</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Remaining Hours</span>
                            <span class="font-medium text-gray-800">380 hrs</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Intern Since</span>
                            <span class="font-medium text-gray-800">January 10, 2025</span>
                        </div>

                    </div>

                </div>

            </div>


            <!-- Progress Section -->
            <div class="bg-white rounded-lg shadow p-6 mt-6">

                <h3 class="text-lg font-semibold mb-4 text-gray-800">
                    Internship Progress
                </h3>

                <div>

                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-500">Progress</span>
                        <span class="font-medium">24%</span>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full" style="width: 24%"></div>
                    </div>

                </div>

            </div>

        <?php else: ?>

            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                Edit Profile
            </h1>

            <div class="bg-white rounded-lg shadow p-6 max-w-screen">

                <form action="<?= htmlspecialchars(BASE_URL . '/profile.php?id=' . $intern['intern_id'] ?? ''); ?>" class="space-y-6" method="POST">

                    <!-- Profile Header -->
                    <div class="flex items-center gap-4 mb-4">

                        <div class="w-14 h-14 rounded-full bg-blue-600 flex items-center justify-center text-lg text-white font-bold">
                            <?= $initials; ?>
                        </div>

                        <div>
                            <p class="text-md font-bold text-gray-800">
                                <?= htmlspecialchars($intern['first_name'] ?? ''); ?>
                                <?= htmlspecialchars($intern['middle_name'] ?? ''); ?>
                                <?= htmlspecialchars($intern['last_name'] ?? ''); ?>
                            </p>
                            <p class="text-sm text-gray-500">Update your personal information</p>
                        </div>

                    </div>


                    <!-- Student ID -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">
                            Student ID
                        </label>

                        <input
                            type="text"
                            name="studentId"
                            value="<?= htmlspecialchars($intern['student_id'] ?? '') ?>"
                            class="w-full border rounded px-3 py-2 text-gray-600">
                    </div>


                    <!-- Name Fields -->
                    <div class="grid md:grid-cols-3 gap-4">

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">
                                First Name
                            </label>

                            <input
                                type="text"
                                name="firstName"
                                value="<?= htmlspecialchars($intern['first_name'] ?? '') ?>"
                                class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">
                                Middle Name
                            </label>

                            <input
                                type="text"
                                name="middleName"
                                value="<?= htmlspecialchars($intern['middle_name'] ?? '') ?>"
                                class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">
                                Last Name
                            </label>

                            <input
                                type="text"
                                name="lastName"
                                value="<?= htmlspecialchars($intern['last_name'] ?? '') ?>"
                                class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        </div>

                    </div>


                    <!-- Email -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="<?= htmlspecialchars($intern['email'] ?? '') ?>"
                            class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                    </div>


                    <!-- Required Hours -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">
                            Required Hours
                        </label>

                        <input
                            type="text"
                            name="requiredHours"
                            value="<?= htmlspecialchars($intern['required_hours'] ?? '') ?>"
                            class="w-full border rounded px-3 py-2 text-gray-600">
                    </div>


                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 pt-4">

                        <a
                            href="<?= htmlspecialchars(BASE_URL . '/profile.php') ?>"
                            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Save Changes
                        </button>

                    </div>

                </form>

            </div>

        <?php endif; ?>

    </main>

</div>

<?php require_once TEMP . '/footer.php'; ?>