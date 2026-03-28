<?php
require_once __DIR__ . '/header.php';
requireLogin();

require_once SRC . '/config/connection.php';
require_once SRC . '/intern/intern.php';

require_once TEMP . '/header.php';
?>

<div class="min-h-screen bg-gray-100 flex w-full">

    <?php require_once TEMP . '/sidebar.php'; ?>

    <!-- Content -->
    <main class="flex-1 px-8 py-4">

        <?php require_once TEMP . '/navbar.php'; ?>

        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            Dashboard
        </h1>

        <!-- Intern Info -->
        <div class="grid md:grid-cols-3 gap-6 mb-6">

            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-sm text-gray-500">Student Number</p>
                <h2 class="text-lg font-semibold">
                    <?= htmlspecialchars($intern['student_number'] ?? '') ?>
                </h2>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-sm text-gray-500">Required Hours</p>
                <h2 class="text-lg font-semibold">
                    <?= htmlspecialchars($intern['required_hours'] ?? '') ?> hrs
                </h2>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-sm text-gray-500">Completed Hours</p>
                <h2 class="text-lg font-semibold text-green-600">120 hrs</h2>
            </div>

        </div>

        <!-- Progress -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h3 class="font-semibold mb-3">Internship Progress</h3>

            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-blue-600 h-4 rounded-full w-1/4"></div>
            </div>

            <p class="text-sm text-gray-500 mt-2">
                120 / <?= htmlspecialchars($intern['required_hours'] ?? '') ?> hours completed
            </p>
        </div>

        <!-- Recent Logs -->
        <div class="bg-white rounded-lg shadow p-6">

            <h3 class="font-semibold mb-4">
                Recent Time Logs
            </h3>

            <table class="w-full text-sm text-left">

                <thead class="border-b">
                    <tr>
                        <th class="py-2">Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Total Hours</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    <tr>
                        <td class="py-2">2026-03-06</td>
                        <td>08:00 AM</td>
                        <td>05:00 PM</td>
                        <td>8.00</td>
                    </tr>

                </tbody>

            </table>

        </div>

    </main>

</div>

<?php require_once TEMP . '/footer.php'; ?>