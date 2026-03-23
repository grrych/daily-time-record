<?php
require_once __DIR__ . '/header.php';
requireLogin();

require_once SRC . '/config/connection.php';
require_once SRC . '/intern/intern.php';
require_once SRC . '/dtr/dtr.php';
require_once SRC . '/schedule/schedule.php';

require_once SRC . '/dtr/time-in-dtr.php';
require_once SRC . '/dtr/time-out-dtr.php';

require_once TEMP . '/header.php';

flashMessage();
?>

<div class="min-h-screen bg-gray-100 flex w-full">

    <?php require_once TEMP . '/sidebar.php'; ?>

    <!-- Content -->
    <main class="flex-1 px-8 pt-4 pb-14 max-h-screen overflow-y-auto mb-6">

        <?php require_once TEMP . '/navbar.php'; ?>

        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            Daily Time Record
        </h1>

        <!-- Time In / Time Out -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">

            <?php
            $timeZone   = new DateTimeZone('Asia/Manila');
            $now        = new DateTime('now', $timeZone);
            $workDate   = $now->format('Y-m-d');
            $dayName    = $now->format('l');

            // Week Start (Monday) and Week End (Saturday)
            $monday     = clone $now;
            $monday->modify('Monday this week');
            $weekStart  = $monday->format('Y-m-d');

            $saturday   = clone $monday;
            $saturday->modify('+5 days');
            $weekEnd    = $saturday->format('Y-m-d');

            // Get today's DTR and schedule
            $todayRecord   = getDtrByWorkDateInternId($workDate, $intern['intern_id'] ?? '');
            $todaySchedule = getScheduleByDayOfWeek($dayName, $intern['intern_id']);

            // Get DTRs for the current week
            $dtrThisWeek = getDtrThisWeek($intern['intern_id'] ?? '', $weekStart, $weekEnd);

            // Get all DTR records
            $dtrRecords  = getAllDtrByInternId($intern['intern_id']);

            // Calculate Hours Today
            $hoursTodaySeconds = 0;

            if (!empty($todayRecord['time_in']) && !empty($todayRecord['time_out'])) {

                $timeIn  = new DateTime($todayRecord['time_in'], $timeZone);
                $timeOut = new DateTime($todayRecord['time_out'], $timeZone);

                // total worked seconds
                $hoursTodaySeconds = $timeOut->getTimestamp() - $timeIn->getTimestamp();

                // subtract start time and end time
                if (!empty($todaySchedule['start_time']) && !empty($todaySchedule['end_time'])) {
                    $startTime = new DateTime($todaySchedule['start_time'], $timeZone);
                    $endTime   = new DateTime($todaySchedule['end_time'], $timeZone);

                    if ($timeIn->getTimestamp() <= $startTime->getTimestamp()) {
                        $hoursTodaySeconds -= ($startTime->getTimestamp() - $timeIn->getTimestamp());
                    }

                    if ($timeOut->getTimestamp() >= $endTime->getTimestamp()) {
                        $hoursTodaySeconds -= ($timeOut->getTimestamp() - $endTime->getTimestamp());
                    }
                }

                // subtract break
                if (!empty($todaySchedule['break_start']) && !empty($todaySchedule['break_end'])) {
                    $breakStart = new DateTime($todaySchedule['break_start'], $timeZone);
                    $breakEnd   = new DateTime($todaySchedule['break_end'], $timeZone);

                    if (
                        $breakStart->getTimestamp() <= $timeOut->getTimestamp() &&
                        $breakEnd->getTimestamp() >= $timeOut->getTimestamp()
                    ) {
                        $hoursTodaySeconds -= ($timeOut->getTimestamp() - $breakStart->getTimestamp());
                    }

                    if (
                        $breakStart->getTimestamp() >= $timeIn->getTimestamp() &&
                        $breakEnd->getTimestamp() <= $timeOut->getTimestamp()
                    ) {
                        $hoursTodaySeconds -= ($breakEnd->getTimestamp() - $breakStart->getTimestamp());
                    }
                }
            }

            // convert to hours + minutes
            $hoursTodayPart   = floor($hoursTodaySeconds / 3600);
            $minutesTodayPart = floor(($hoursTodaySeconds % 3600) / 60);

            // Calculate Hours This Week
            $totalWeekSeconds = 0;

            foreach ($dtrThisWeek as $record) {

                if (empty($record['time_in']) || empty($record['time_out'])) continue;

                $timeIn  = new DateTime($record['time_in'], $timeZone);
                $timeOut = new DateTime($record['time_out'], $timeZone);

                $seconds = $timeOut->getTimestamp() - $timeIn->getTimestamp();

                // get schedule for that day
                $dayName = date('l', strtotime($record['work_date']));
                $schedule = getScheduleByDayOfWeek($dayName, $intern['intern_id']);

                if (!empty($schedule['start_time']) && !empty($schedule['end_time'])) {
                    $startTime = new DateTime($schedule['start_time'], $timeZone);
                    $endTime   = new DateTime($schedule['end_time'], $timeZone);

                    if ($timeIn->getTimestamp() <= $startTime->getTimestamp()) {
                        $seconds -= ($startTime->getTimestamp() - $timeIn->getTimestamp());
                    }

                    if ($timeOut->getTimestamp() >= $endTime->getTimestamp()) {
                        $seconds -= ($timeOut->getTimestamp() - $endTime->getTimestamp());
                    }
                }

                if (!empty($schedule['break_start']) && !empty($schedule['break_end'])) {
                    $breakStart = new DateTime($schedule['break_start'], $timeZone);
                    $breakEnd   = new DateTime($schedule['break_end'], $timeZone);

                    if (
                        $breakStart->getTimestamp() <= $timeOut->getTimestamp() &&
                        $breakEnd->getTimestamp() >= $timeOut->getTimestamp()
                    ) {
                        $seconds -= ($timeOut->getTimestamp() - $breakStart->getTimestamp());
                    }

                    if (
                        $breakStart->getTimestamp() >= $timeIn->getTimestamp() &&
                        $breakEnd->getTimestamp() <= $timeOut->getTimestamp()
                    ) {
                        $seconds -= ($breakEnd->getTimestamp() - $breakStart->getTimestamp());
                    }
                }

                $totalWeekSeconds += $seconds;
            }

            // convert
            $hoursWeekPart   = floor($totalWeekSeconds / 3600);
            $minutesWeekPart = floor(($totalWeekSeconds % 3600) / 60);

            // Total Hours Rendered (example: from DB or calculation)
            $totalRenderedSeconds = 0;

            foreach ($dtrRecords as $record) {

                if (empty($record['time_in']) || empty($record['time_out'])) continue;

                $timeIn  = new DateTime($record['time_in'], $timeZone);
                $timeOut = new DateTime($record['time_out'], $timeZone);

                $seconds = $timeOut->getTimestamp() - $timeIn->getTimestamp();

                // subtract break
                $dayName = date('l', strtotime($record['work_date']));
                $schedule = getScheduleByDayOfWeek($dayName, $intern['intern_id']);

                // subtract start time and end time
                if (!empty($schedule['start_time']) && !empty($schedule['end_time'])) {
                    $startTime = new DateTime($schedule['start_time'], $timeZone);
                    $endTime   = new DateTime($schedule['end_time'], $timeZone);

                    if ($timeIn->getTimestamp() <= $startTime->getTimestamp()) {
                        $seconds -= ($startTime->getTimestamp() - $timeIn->getTimestamp());
                    }

                    if ($timeOut->getTimestamp() >= $endTime->getTimestamp()) {
                        $seconds -= ($timeOut->getTimestamp() - $endTime->getTimestamp());
                    }
                }

                if (!empty($schedule['break_start']) && !empty($schedule['break_end'])) {
                    $breakStart = new DateTime($schedule['break_start'], $timeZone);
                    $breakEnd   = new DateTime($schedule['break_end'], $timeZone);

                    if (
                        $breakStart->getTimestamp() <= $timeOut->getTimestamp() &&
                        $breakEnd->getTimestamp() >= $timeOut->getTimestamp()
                    ) {
                        $seconds -= ($timeOut->getTimestamp() - $breakStart->getTimestamp());
                    }

                    if (
                        $breakStart->getTimestamp() >= $timeIn->getTimestamp() &&
                        $breakEnd->getTimestamp() <= $timeOut->getTimestamp()
                    ) {
                        $seconds -= ($breakEnd->getTimestamp() - $breakStart->getTimestamp());
                    }
                }

                $totalRenderedSeconds += $seconds;
            }

            // convert
            $totalRenderedHours   = floor($totalRenderedSeconds / 3600);
            $totalRenderedMinutes = floor(($totalRenderedSeconds % 3600) / 60);

            $totalRequired = $intern['required_hours'] ?? 500;
            ?>

            <h3 class="font-semibold mb-4 text-gray-800">Today's Attendance</h3>

            <div class="grid md:grid-cols-3 gap-6 items-center">

                <!-- Time In -->
                <div class="bg-gray-50 p-4 rounded text-center">
                    <p class="text-sm text-gray-500 mb-1">Time In</p>
                    <p class="text-xl font-semibold text-gray-800">
                        <?= !empty($todayRecord['time_in']) ? date('h:i A', strtotime($todayRecord['time_in'])) : '–'; ?>
                    </p>
                </div>

                <!-- Time Out -->
                <div class="bg-gray-50 p-4 rounded text-center">
                    <p class="text-sm text-gray-500 mb-1">Time Out</p>
                    <p class="text-xl font-semibold text-gray-800">
                        <?= !empty($todayRecord['time_out']) ? date('h:i A', strtotime($todayRecord['time_out'])) : '–'; ?>
                    </p>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col gap-3">

                    <!-- Time Buttons -->
                    <div class="flex gap-3">
                        <?php if (empty($todayRecord)): ?>
                            <!-- No record yet -->
                            <form action="<?= htmlspecialchars(BASE_URL . '/daily-time-record.php'); ?>" method="post" class="flex-1">
                                <input type="hidden" name="timeIn" value="">
                                <button class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
                                    Time In
                                </button>
                            </form>
                            <button class="flex-1 bg-gray-200 text-gray-400 py-2 rounded cursor-not-allowed">
                                Time Out
                            </button>

                        <?php elseif (empty($todayRecord['time_out'])): ?>
                            <!-- Timed in, not timed out -->
                            <button disabled class="flex-1 bg-gray-200 text-gray-400 py-2 rounded cursor-not-allowed">
                                Time In
                            </button>
                            <form action="<?= htmlspecialchars(BASE_URL . '/daily-time-record.php'); ?>" method="post" class="flex-1">
                                <input type="hidden" name="timeOut" value="">
                                <button class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">
                                    Time Out
                                </button>
                            </form>

                        <?php else: ?>
                            <!-- Already timed out -->
                            <button disabled class="flex-1 bg-gray-200 text-gray-400 py-2 rounded cursor-not-allowed">
                                Time In
                            </button>
                            <button disabled class="flex-1 bg-gray-200 text-gray-400 py-2 rounded cursor-not-allowed">
                                Time Out
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Divider -->
                    <div class="flex items-center gap-3 my-1">
                        <div class="flex-1 h-px bg-gray-300"></div>
                        <span class="text-xs text-gray-500 font-medium">OR</span>
                        <div class="flex-1 h-px bg-gray-300"></div>
                    </div>

                    <!-- Manual Entry Button -->
                    <button
                        class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700"
                        data-modal-open="manualEntryModal"
                        data-fill-work_date="<?= $workDate ?>"
                        data-fill-time_in="<?= $todayRecord['time_in'] ?? '' ?>"
                        data-fill-time_out="<?= $todayRecord['time_out'] ?? '' ?>">
                        Add Manual Entry
                    </button>

                </div>

            </div>

        </div>

        <!-- Reusable Modal for Manual Entry -->
        <div
            id="manualEntryModal"
            data-modal
            class="fixed flex inset-0 z-50 hidden items-center justify-center bg-black/50 transition-opacity duration-300">

            <div
                data-modal-content
                class="bg-white w-full max-w-md rounded-lg shadow-lg transform scale-90 opacity-0 transition-all duration-300">

                <!-- Header -->
                <div class="flex justify-between items-center border-b px-6 py-4">
                    <h2 class="text-lg font-semibold">Manual DTR Entry</h2>
                    <button data-modal-close class="text-gray-500 hover:text-gray-700 text-xl">&times;</button>
                </div>

                <!-- Body -->
                <form action="<?= htmlspecialchars(BASE_URL . '/daily-time-record.php') ?>" method="POST" class="p-6 space-y-4">

                    <input type="hidden" name="intern_id" value="<?= $intern_id ?>">

                    <!-- Date -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Date</label>
                        <input type="date" name="work_date" id="work_date" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                    </div>

                    <!-- Time In -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Time In</label>
                        <input type="time" name="time_in" id="time_in" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                    </div>

                    <!-- Time Out -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Time Out</label>
                        <input type="time" name="time_out" id="time_out" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" data-modal-close class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Save
                        </button>
                    </div>

                </form>

            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-6">

            <!-- Hours Today -->
            <div class="bg-white p-5 rounded-lg shadow">
                <p class="text-sm text-gray-500">Hours Today</p>
                <h3 class="text-xl font-semibold mt-1">
                    <?php
                    echo "{$hoursTodayPart} hr" . ($hoursTodayPart != 1 ? 's' : '') .
                        " {$minutesTodayPart} min" . ($minutesTodayPart != 1 ? 's' : '');
                    ?>
                </h3>
            </div>

            <!-- Hours This Week -->
            <div class="bg-white p-5 rounded-lg shadow">
                <p class="text-sm text-gray-500">Hours This Week</p>
                <h3 class="text-xl font-semibold mt-1">
                    <?php
                    echo "{$hoursWeekPart} hr" . ($hoursWeekPart != 1 ? 's' : '') .
                        " {$minutesWeekPart} min" . ($minutesWeekPart != 1 ? 's' : '');
                    ?>
                </h3>
            </div>

            <!-- Total Hours Rendered -->
            <div class="bg-white p-5 rounded-lg shadow">
                <p class="text-sm text-gray-500">Total Hours Rendered</p>
                <h3 class="text-xl font-semibold mt-1">
                    <?= "{$totalRenderedHours} hr" . ($totalRenderedHours != 1 ? 's' : '') .
                        " {$totalRenderedMinutes} min" . ($totalRenderedMinutes != 1 ? 's' : '') .
                        " / {$totalRequired} hrs"; ?>
                </h3>
            </div>

        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold">Attendance History</h3>
                <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                    Export
                </button>
            </div>

            <div class="overflow-y-auto max-h-96">
                <table class="w-full text-sm text-left divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10 border-b">
                        <tr>
                            <th class="py-2 px-4">Date</th>
                            <th class="px-4">Day</th>
                            <th class="px-4">Time In</th>
                            <th class="px-4">Time Out</th>
                            <th class="px-4">Total Hours</th>
                            <th class="px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dtrRecords)): ?>
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">
                                    No DTR records found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dtrRecords as $dtr):
                                $timeIn  = !empty($dtr['time_in'])  ? new DateTime($dtr['time_in'], $timeZone) : null;
                                $timeOut = !empty($dtr['time_out']) ? new DateTime($dtr['time_out'], $timeZone) : null;

                                // Calculate total hours in HH hrs MM mins (ignore seconds)
                                if ($timeIn && $timeOut) {

                                    $seconds = $timeOut->getTimestamp() - $timeIn->getTimestamp();

                                    $dayName = date('l', strtotime($dtr['work_date']));
                                    $schedule = getScheduleByDayOfWeek($dayName, $intern['intern_id']);

                                    // subtract start time and end time
                                    if (!empty($schedule['start_time']) && !empty($schedule['end_time'])) {
                                        $startTime = new DateTime($schedule['start_time'], $timeZone);
                                        $endTime   = new DateTime($schedule['end_time'], $timeZone);

                                        if ($timeIn->getTimestamp() <= $startTime->getTimestamp()) {
                                            $seconds -= ($startTime->getTimestamp() - $timeIn->getTimestamp());
                                        }

                                        if ($timeOut->getTimestamp() >= $endTime->getTimestamp()) {
                                            $seconds -= ($timeOut->getTimestamp() - $endTime->getTimestamp());
                                        }
                                    }

                                    if (!empty($schedule['break_start']) && !empty($schedule['break_end'])) {
                                        $breakStart = new DateTime($schedule['break_start'], $timeZone);
                                        $breakEnd   = new DateTime($schedule['break_end'], $timeZone);

                                        if (
                                            $breakStart->getTimestamp() <= $timeOut->getTimestamp() &&
                                            $breakEnd->getTimestamp() >= $timeOut->getTimestamp()
                                        ) {
                                            $seconds -= ($timeOut->getTimestamp() - $breakStart->getTimestamp());
                                        }

                                        if (
                                            $breakStart->getTimestamp() >= $timeIn->getTimestamp() &&
                                            $breakEnd->getTimestamp() <= $timeOut->getTimestamp()
                                        ) {
                                            $seconds -= ($breakEnd->getTimestamp() - $breakStart->getTimestamp());
                                        }
                                    }

                                    $hours   = floor($seconds / 3600);
                                    $minutes = floor(($seconds % 3600) / 60);

                                    $totalHours = "{$hours} hr" . ($hours != 1 ? 's' : '') .
                                        " {$minutes} min" . ($minutes != 1 ? 's' : '');
                                } else {
                                    $totalHours = '—';
                                }

                                $status = ($timeIn && $timeOut) ? 'Complete' : (! $timeIn && ! $timeOut ? 'Absent' : 'Ongoing');
                                $statusClass = match ($status) {
                                    'Complete' => 'bg-green-100 text-green-700',
                                    'Ongoing'  => 'bg-yellow-100 text-yellow-700',
                                    default    => 'bg-red-100 text-red-700',
                                };
                            ?>
                                <tr>
                                    <td class="py-2 px-4"><?= !empty($dtr['work_date']) ? date('M j, Y', strtotime($dtr['work_date'])) : '—'; ?></td>
                                    <td class="px-4"><?= !empty($dtr['work_date']) ? date('l', strtotime($dtr['work_date'])) : '—'; ?></td>
                                    <td class="px-4"><?= $timeIn ? $timeIn->format('h:i A') : '—'; ?></td>
                                    <td class="px-4"><?= $timeOut ? $timeOut->format('h:i A') : '—'; ?></td>
                                    <td class="px-4"><?= $totalHours; ?></td>
                                    <td class="px-4">
                                        <span class="px-2 py-1 text-xs rounded <?= $statusClass ?>">
                                            <?= $status ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</div>

<?php require_once TEMP . '/footer.php'; ?>