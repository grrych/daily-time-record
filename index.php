<?php
require_once __DIR__ . '/header.php';
requireLogin();

require_once SRC . '/config/connection.php';
require_once SRC . '/intern/intern.php';
require_once SRC . '/dtr/dtr.php';
require_once SRC . '/schedule-template/schedule-template.php';
require_once SRC . '/intern-schedule/intern-schedule.php';

require_once TEMP . '/header.php';

$timeZone   = new DateTimeZone('Asia/Manila');

function calculateWorkedSeconds($timeInStr, $timeOutStr, $schedule, $timeZone)
{
    if (empty($timeInStr) || empty($timeOutStr)) return 0;

    $timeIn  = new DateTime($timeInStr, $timeZone);
    $timeOut = new DateTime($timeOutStr, $timeZone);

    $seconds = $timeOut->getTimestamp() - $timeIn->getTimestamp();

    if ($seconds <= 0) return 0;

    // Apply schedule limits - only if schedule exists
    if (!empty($schedule) && !empty($schedule['start_time']) && !empty($schedule['end_time'])) {

        $startTime = new DateTime($timeIn->format('Y-m-d') . ' ' . $schedule['start_time'], $timeZone);
        $endTime   = new DateTime($timeIn->format('Y-m-d') . ' ' . $schedule['end_time'], $timeZone);

        // Get overlap between actual work time and scheduled work time
        $overlapStart = max($timeIn->getTimestamp(), $startTime->getTimestamp());
        $overlapEnd   = min($timeOut->getTimestamp(), $endTime->getTimestamp());

        if ($overlapEnd > $overlapStart) {
            $seconds = $overlapEnd - $overlapStart;
        } else {
            return 0; // No overlap with scheduled hours
        }
    }

    // FIXED BREAK LOGIC (overlap-based) - only if schedule exists and has break times
    if (!empty($schedule) && !empty($schedule['break_start']) && !empty($schedule['break_end'])) {

        $breakStart = new DateTime($timeIn->format('Y-m-d') . ' ' . $schedule['break_start'], $timeZone);
        $breakEnd   = new DateTime($timeIn->format('Y-m-d') . ' ' . $schedule['break_end'], $timeZone);

        // Get overlap between work time and break
        $overlapStart = max($timeIn->getTimestamp(), $breakStart->getTimestamp());
        $overlapEnd   = min($timeOut->getTimestamp(), $breakEnd->getTimestamp());

        if ($overlapEnd > $overlapStart) {
            $seconds -= ($overlapEnd - $overlapStart);
        }
    }

    return max(0, $seconds); // prevent negative
}

$totalRenderedSeconds = 0;

foreach (getAllDtrByInternId($_SESSION['intern_id'] ?? '') as $dtr) {
    if (!empty($dtr['time_in']) && !empty($dtr['time_out'])) {

        $scheduleTemplate = getScheduleTemplateByInternId($intern['intern_id'] ?? '');

        $totalRenderedSeconds += calculateWorkedSeconds(
            $dtr['time_in'],
            $dtr['time_out'],
            $scheduleTemplate,
            $timeZone
        );
    }
}

// Convert to hours + minutes
$totalRenderedHours   = floor($totalRenderedSeconds / 3600);
$totalRenderedMinutes = floor(($totalRenderedSeconds % 3600) / 60);

$requiredHours = $intern['required_hours'] ?? 500;

// Convert required hours to seconds
$requiredSeconds = $requiredHours * 3600;

// divide-by-zero
$progressPercent = ($requiredSeconds > 0)
    ? ($totalRenderedSeconds / $requiredSeconds) * 100
    : 0;

// Limit to 100%
$progressPercent = min(100, $progressPercent);

$dtrRecords = getDtrRecentLogByInternId($_SESSION['intern_id'] ?? '');

// Remaining seconds
$remainingSeconds = max(0, $requiredSeconds - $totalRenderedSeconds);

// Convert to hours + minutes
$remainingHours   = floor($remainingSeconds / 3600);
$remainingMinutes = floor(($remainingSeconds % 3600) / 60);

$workedDays = 0;

foreach (getAllDtrByInternId($_SESSION['intern_id'] ?? '') as $dtr) {
    if (!empty($dtr['time_in']) && !empty($dtr['time_out'])) {
        $workedDays++;
    }
}

$avgHoursPerDay = ($workedDays > 0)
    ? ($totalRenderedSeconds / 3600) / $workedDays
    : 0;

$remainingHoursDecimal = $remainingSeconds / 3600;

$estimatedDays = ($avgHoursPerDay > 0)
    ? ceil($remainingHoursDecimal / $avgHoursPerDay)
    : 0;

$estimatedFinishDate = null;

if ($estimatedDays > 0) {
    $today = new DateTime('now', $timeZone);
    $today->modify("+{$estimatedDays} days");
    $estimatedFinishDate = $today;
}
?>

<div class="min-h-screen bg-gray-100 flex w-full">

    <?php require_once TEMP . '/sidebar.php'; ?>

    <!-- Content -->
    <main class="flex-1 px-8 pt-4 pb-14 max-h-screen overflow-y-auto scrollbar-thin mb-6">

        <?php require_once TEMP . '/navbar.php'; ?>

        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            Dashboard
        </h1>

        <div class="grid md:grid-cols-3 gap-6 mb-6">

            <!-- Student Number -->
            <div class="bg-white p-6 rounded-lg shadow flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Student Number</p>
                    <h2 class="text-lg font-semibold">
                        <?= e($intern['student_number'] ?? '') ?>
                    </h2>
                </div>
                <div class="bg-gray-100 p-3 rounded-full">
                    <!-- ID Card Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7h16M4 17h16M7 7v10M17 7v10" />
                    </svg>
                </div>
            </div>

            <!-- Required Hours -->
            <div class="bg-white p-6 rounded-lg shadow flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Required Hours</p>
                    <h2 class="text-lg font-semibold">
                        <?= e($intern['required_hours'] ?? '', false) ?> hrs
                    </h2>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <!-- Clock Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Completed Hours -->
            <div class="bg-white p-6 rounded-lg shadow flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Completed Hours</p>
                    <h2 class="text-lg font-semibold text-green-600">
                        <?= "{$totalRenderedHours} hr" . ($totalRenderedHours != 1 ? 's' : '') .
                            " {$totalRenderedMinutes} min" . ($totalRenderedMinutes != 1 ? 's' : '') ?>
                    </h2>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <!-- Check Circle -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2l4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Remaining Hours -->
            <div class="bg-white p-6 rounded-lg shadow flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Remaining Hours</p>
                    <h2 class="text-lg font-semibold text-red-600">
                        <?= "{$remainingHours} hr" . ($remainingHours != 1 ? 's' : '') .
                            " {$remainingMinutes} min" . ($remainingMinutes != 1 ? 's' : '') ?>
                    </h2>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <!-- Alert / Time Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Estimated Finish -->
            <div class="bg-white p-6 rounded-lg shadow flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Estimated Finish</p>
                    <h2 class="text-lg font-semibold text-blue-600">
                        <?php if ($remainingSeconds <= 0): ?>
                            Completed
                        <?php elseif ($estimatedFinishDate): ?>
                            <?= $estimatedFinishDate->format('M j, Y') ?>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </h2>
                </div>

                <div class="<?= $remainingSeconds <= 0 ? 'bg-green-100' : 'bg-blue-100' ?> p-3 rounded-full">
                    <?php if ($remainingSeconds <= 0): ?>
                        <!-- Check -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    <?php else: ?>
                        <!-- Calendar -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 
                           00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Progress -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">

            <h3 class="text-lg font-semibold mb-4 text-gray-800">
                Internship Progress
            </h3>

            <div class="flex justify-between text-sm mb-2">
                <span class="text-gray-500">Progress</span>
                <span class="font-medium"><?= round($progressPercent, 1) ?>%</span>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-blue-600 h-4 rounded-full" style="width: <?= $progressPercent ?>%;"></div>
            </div>

            <p class="text-sm text-gray-500 mt-2">
                <?= "{$totalRenderedHours} / {$requiredHours}" ?> hours completed
            </p>
        </div>

        <!-- Recent Logs -->
        <div class="bg-white rounded-lg shadow p-6">

            <h3 class="font-semibold mb-4">
                Recent Time Logs
            </h3>

            <table class="w-full text-sm text-left">

                <thead class="border-b border-gray-200">
                    <tr>
                        <th class="py-2">Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Total Hours</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    <?php if (empty($dtrRecords)): ?>
                        <tr>
                            <td colspan="4" class="py-4 text-center text-gray-500">
                                No records found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($dtrRecords as $dtr):

                            $timeIn  = !empty($dtr['time_in'])  ? new DateTime($dtr['time_in'], $timeZone) : null;
                            $timeOut = !empty($dtr['time_out']) ? new DateTime($dtr['time_out'], $timeZone) : null;

                            if ($timeIn && $timeOut) {

                                $seconds = calculateWorkedSeconds(
                                    $dtr['time_in'],
                                    $dtr['time_out'],
                                    $scheduleTemplate,
                                    $timeZone
                                );

                                $hours   = floor($seconds / 3600);
                                $minutes = floor(($seconds % 3600) / 60);

                                $totalHours = "{$hours} hr" . ($hours != 1 ? 's' : '') .
                                    " {$minutes} min" . ($minutes != 1 ? 's' : '');
                            } else {
                                $totalHours = '&mdash;';
                            }
                        ?>
                            <tr class="border-b border-gray-200">
                                <td class="py-2">
                                    <?= (!empty($dtr['work_date']) ? date('M j, Y', strtotime($dtr['work_date'])) : '&mdash;') . PHP_EOL; ?>
                                </td>
                                <td>
                                    <?= ($timeIn ? $timeIn->format('h:i A') : '&mdash;') . PHP_EOL; ?>
                                </td>
                                <td>
                                    <?= ($timeOut ? $timeOut->format('h:i A') : '&mdash;') . PHP_EOL; ?>
                                </td>
                                <td>
                                    <?= $totalHours . PHP_EOL; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

            </table>

        </div>

    </main>

</div>

<?php require_once TEMP . '/footer.php'; ?>