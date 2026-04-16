<?php
require_once __DIR__ . '/header.php';
requireLogin();

require_once SRC . '/config/connection.php';
require_once SRC . '/intern/intern.php';
require_once SRC . '/dtr/dtr.php';
require_once SRC . '/schedule-template/schedule-template.php';
require_once SRC . '/intern-schedule/intern-schedule.php';

require_once SRC . '/intern/edit-intern.php';

require_once TEMP . '/header.php';

flashMessage();

// ==========================
// COMPUTATION LOGIC (LIKE DTR PAGE)
// ==========================

$timeZone = new DateTimeZone('Asia/Manila');

$dtrRecords = getAllDtrByInternId($intern['intern_id'] ?? '');
$scheduleTemplate = getScheduleTemplateByInternId($intern['intern_id'] ?? '');

function calculateWorkedSeconds($timeInStr, $timeOutStr, $schedule, $timeZone)
{
    if (empty($timeInStr) || empty($timeOutStr)) return 0;

    $timeIn  = new DateTime($timeInStr, $timeZone);
    $timeOut = new DateTime($timeOutStr, $timeZone);

    $seconds = $timeOut->getTimestamp() - $timeIn->getTimestamp();
    if ($seconds <= 0) return 0;

    // Apply schedule limit
    if (!empty($schedule) && !empty($schedule['start_time']) && !empty($schedule['end_time'])) {

        $startTime = new DateTime($timeIn->format('Y-m-d') . ' ' . $schedule['start_time'], $timeZone);
        $endTime   = new DateTime($timeIn->format('Y-m-d') . ' ' . $schedule['end_time'], $timeZone);

        $overlapStart = max($timeIn->getTimestamp(), $startTime->getTimestamp());
        $overlapEnd   = min($timeOut->getTimestamp(), $endTime->getTimestamp());

        if ($overlapEnd > $overlapStart) {
            $seconds = $overlapEnd - $overlapStart;
        } else {
            return 0;
        }
    }

    // Deduct break
    if (!empty($schedule) && isset($schedule['break_start']) && isset($schedule['break_end'])) {

        $breakStart = new DateTime($timeIn->format('Y-m-d') . ' ' . $schedule['break_start'], $timeZone);
        $breakEnd   = new DateTime($timeIn->format('Y-m-d') . ' ' . $schedule['break_end'], $timeZone);

        $overlapStart = max($timeIn->getTimestamp(), $breakStart->getTimestamp());
        $overlapEnd   = min($timeOut->getTimestamp(), $breakEnd->getTimestamp());

        if ($overlapEnd > $overlapStart) {
            $seconds -= ($overlapEnd - $overlapStart);
        }
    }

    return max(0, $seconds);
}

// TOTAL HOURS
$totalSeconds = 0;

foreach ($dtrRecords as $record) {
    if (!empty($record['time_in']) && !empty($record['time_out'])) {
        $totalSeconds += calculateWorkedSeconds(
            $record['time_in'],
            $record['time_out'],
            $scheduleTemplate,
            $timeZone
        );
    }
}

// Convert completed
$completedHours   = floor($totalSeconds / 3600);
$completedMinutes = floor(($totalSeconds % 3600) / 60);

// Required & Remaining
$requiredHours = $intern['required_hours'] ?? 500;

$remainingSeconds = max(0, ($requiredHours * 3600) - $totalSeconds);
$remainingHours   = floor($remainingSeconds / 3600);
$remainingMinutes = floor(($remainingSeconds % 3600) / 60);
?>

<div class="min-h-screen bg-gray-100 flex w-full">

    <?php require_once TEMP . '/sidebar.php'; ?>

    <main class="flex-1 px-8 pt-4 pb-14 max-h-screen overflow-y-auto scrollbar-thin mb-6">

        <?php require_once TEMP . '/navbar.php'; ?>

        <?php if (empty($_GET['id'])): ?>

            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                Profile
            </h1>

            <!-- Profile Header -->
            <div class="bg-white rounded-lg shadow p-6 mb-6 flex items-center justify-between">

                <div class="flex items-center gap-6">

                    <div class="w-14 h-14 rounded-full bg-blue-600 flex items-center justify-center text-white text-lg font-bold">
                        <?= e($initials); ?>
                    </div>

                    <div>
                        <h2 class="text-md font-bold text-gray-800">
                            <?= e($intern['first_name'] ?? ''); ?>
                            <?= e($intern['middle_name'] ?? ''); ?>
                            <?= e($intern['last_name'] ?? ''); ?>
                        </h2>

                        <p class="text-sm text-gray-500">
                            Student Number: <?= e($intern['student_number'] ?? ''); ?>
                        </p>

                        <p class="text-sm text-gray-500">
                            <?= e($intern['email'] ?? ''); ?>
                        </p>
                    </div>

                </div>

                <a href="<?= e(BASE_URL . '/profile.php?id=' . ($intern['intern_id'] ?? '')); ?>"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Profile
                </a>

            </div>

            <div class="grid md:grid-cols-2 gap-6">

                <!-- Personal Info -->
                <div class="bg-white rounded-lg shadow p-6">

                    <h3 class="text-lg font-semibold mb-4 text-gray-800">
                        Personal Information
                    </h3>

                    <div class="space-y-4 text-sm">

                        <div class="flex justify-between">
                            <span class="text-gray-500">First Name</span>
                            <span class="font-medium text-gray-800"><?= e($intern['first_name'] ?? ''); ?></span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Middle Name</span>
                            <span class="font-medium text-gray-800"><?= e($intern['middle_name'] ?? ''); ?></span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Last Name</span>
                            <span class="font-medium text-gray-800"><?= e($intern['last_name'] ?? ''); ?></span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Email</span>
                            <span class="font-medium text-gray-800"><?= e($intern['email'] ?? ''); ?></span>
                        </div>

                    </div>

                </div>

                <!-- Internship Info (YOUR ORIGINAL FORMAT - hours and minutes separately) -->
                <div class="bg-white rounded-lg shadow p-6">

                    <h3 class="text-lg font-semibold mb-4 text-gray-800">
                        Internship Information
                    </h3>

                    <div class="space-y-4 text-sm">

                        <div class="flex justify-between">
                            <span class="text-gray-500">Required Hours</span>
                            <span class="font-medium text-gray-800">
                                <?= "{$requiredHours} hrs"; ?>
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Completed Hours</span>
                            <span class="font-medium text-green-600">
                                <?= "{$completedHours} hr" . ($completedHours != 1 ? 's' : '') .
                                    " {$completedMinutes} min"; ?>
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Remaining Hours</span>
                            <span class="font-medium text-red-600">
                                <?= "{$remainingHours} hr" . ($remainingHours != 1 ? 's' : '') .
                                    " {$remainingMinutes} min"; ?>
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Intern Since</span>
                            <span class="font-medium text-gray-800">
                                <?= date('F d, Y', strtotime($intern['created_at'] ?? 'now')); ?>
                            </span>
                        </div>

                    </div>

                </div>

            </div>

        <?php else: ?>

            <!-- EDIT MODE -->
            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                Edit Profile
            </h1>

            <div class="bg-white rounded-lg shadow p-6 max-w-screen">

                <form action="<?= e(BASE_URL . '/profile.php?id=' . ($intern['intern_id'] ?? '')); ?>" method="POST" class="space-y-6">

                    <!-- Profile Header -->
                    <div class="flex items-center gap-4 mb-4">

                        <div class="w-14 h-14 rounded-full bg-blue-600 flex items-center justify-center text-lg text-white font-bold">
                            <?= e($initials); ?>
                        </div>

                        <div>
                            <p class="text-md font-bold text-gray-800">
                                <?= e($intern['first_name'] ?? ''); ?>
                                <?= e($intern['middle_name'] ?? ''); ?>
                                <?= e($intern['last_name'] ?? ''); ?>
                            </p>
                            <p class="text-sm text-gray-500">Update your personal information</p>
                        </div>

                    </div>

                    <!-- Student Number -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Student Number</label>
                        <input type="text" name="studentNumber" value="<?= e($intern['student_number'] ?? '') ?>"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-gray-600">
                    </div>

                    <!-- Name Fields -->
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">First Name</label>
                            <input type="text" name="firstName" value="<?= e($intern['first_name'] ?? '') ?>"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Middle Name</label>
                            <input type="text" name="middleName" value="<?= e($intern['middle_name'] ?? '') ?>"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Last Name</label>
                            <input type="text" name="lastName" value="<?= e($intern['last_name'] ?? '') ?>"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Email</label>
                        <input type="email" name="email" value="<?= e($intern['email'] ?? '') ?>"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                    </div>

                    <!-- Required Hours -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Required Hours</label>
                        <input type="text" name="requiredHours" value="<?= e($intern['required_hours'] ?? '') ?>"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-gray-600">
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 pt-4">
                        <a href="<?= e(BASE_URL . '/profile.php'); ?>"
                            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 cursor-pointer">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 cursor-pointer">
                            Save Changes
                        </button>
                    </div>

                </form>

            </div>

        <?php endif; ?>

    </main>

</div>

<?php require_once TEMP . '/footer.php'; ?>