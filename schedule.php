<?php
require_once __DIR__ . '/header.php';
requireLogin();

require_once SRC . '/config/connection.php';
require_once SRC . '/intern/intern.php';
require_once SRC . '/schedule-template/schedule-template.php';
require_once SRC . '/intern-schedule/intern-schedule.php';
require_once SRC . '/schedule-template/add-schedule-template.php';
require_once SRC . '/schedule-template/edit-schedule-template.php';

require_once TEMP . '/header.php';

flashMessage();
?>

<div class="min-h-screen bg-gray-100 flex w-full">

    <?php require_once TEMP . '/sidebar.php'; ?>

    <main class="flex-1 px-8 pt-4 pb-14 max-h-screen overflow-y-auto scrollbar-thin mb-6">

        <?php require_once TEMP . '/navbar.php'; ?>

        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">My Schedule</h1>

            <!-- Button to open modal -->
            <div class="mb-4 flex justify-end">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 cursor-pointer"
                    data-modal-open="scheduleModal">
                    Set Weekly Schedule
                </button>
            </div>
        </div>

        <?php $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; ?>

        <!-- Weekly Schedule Modal -->
        <div id="scheduleModal" data-modal class="fixed inset-0 hidden flex items-center justify-center bg-black/50 z-50">

            <div data-modal-content class="bg-white w-full max-w-lg rounded-lg transform scale-90 opacity-0 transition-all duration-200">

                <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold">Set Weekly Schedule</h2>
                </div>

                <form action="<?= BASE_URL ?>/schedule.php" method="POST">

                    <input type="hidden" name="set" value="">

                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto scrollbar-thin">
                        <!-- Days selection -->
                        <div>
                            <label class="text-sm font-medium">
                                Select Days to Apply
                                <span class="text-red-600">&ast;</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <?php foreach ($daysOfWeek as $day): ?>
                                    <label class="flex items-center gap-2 cursor-pointer border border-gray-300 px-4 py-3 rounded">
                                        <input class="cursor-pointer" type="checkbox" name="days[]" value="<?= $day; ?>">
                                        <span class="select-none">
                                            <?= $day . PHP_EOL; ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Start Time -->
                        <div>
                            <label class="text-sm font-medium">
                                Start Time
                                <span class="text-red-600">&ast;</span>
                            </label>
                            <input
                                type="time"
                                name="startTime" class="w-full border border-gray-300 rounded px-3 py-2"
                                value="08:00"
                                required>
                        </div>

                        <!-- Break Start -->
                        <div>
                            <label class="text-sm font-medium">
                                Break Start
                                <span class="text-red-600">&ast;</span>
                            </label>
                            <input
                                type="time"
                                name="breakStart"
                                class="w-full border border-gray-300 rounded px-3 py-2"
                                value="12:00"
                                required>
                        </div>

                        <!-- Break End -->
                        <div>
                            <label class="text-sm font-medium">
                                Break End
                                <span class="text-red-600">&ast;</span>
                            </label>
                            <input
                                type="time"
                                name="breakEnd"
                                class="w-full border border-gray-300 rounded px-3 py-2"
                                value="13:00"
                                required>
                        </div>

                        <!-- End Time -->
                        <div>
                            <label class="text-sm font-medium">
                                End Time
                                <span class="text-red-600">&ast;</span>
                            </label>
                            <input
                                type="time"
                                name="endTime"
                                class="w-full border border-gray-300 rounded px-3 py-2"
                                value="17:00"
                                required>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end gap-2 px-6 py-4">
                        <button type="button" data-modal-close class="px-4 py-2 bg-gray-200 rounded cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded cursor-pointer">
                            Apply Schedule
                        </button>
                    </div>

                </form>

            </div>
        </div>

        <!-- Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-6">
            <?php foreach ($daysOfWeek as $day): ?>
                <?php $schedule = getIScheduleTemplateDayOfWeekByDayInternId($day, $intern['intern_id']); ?>

                <div class="bg-white p-5 rounded-lg shadow">
                    <p class="text-sm text-gray-500"><?= $day ?></p>

                    <?php if (empty($schedule['day_of_week'])): ?>
                        <p class="text-gray-400 text-sm mt-2">No schedule</p>
                    <?php else: ?>
                        <h3 class="text-lg font-semibold mt-1">
                            <?= date('h:i A', strtotime($schedule['start_time'])); ?>
                            -
                            <?= date('h:i A', strtotime($schedule['end_time'])) . PHP_EOL; ?>
                        </h3>

                        <?php if (!empty($schedule['break_start']) && !empty($schedule['break_end'])): ?>
                            <p class="text-xs text-gray-500 mt-1">
                                Break:
                                <?= date('h:i A', strtotime($schedule['break_start'])); ?>
                                -
                                <?= date('h:i A', strtotime($schedule['break_end'])) . PHP_EOL; ?>
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow p-6">

            <h3 class="font-semibold mb-4">Weekly Schedule</h3>

            <table class="w-full text-sm text-left">
                <thead class="border-b border-gray-200">
                    <tr>
                        <th class="py-2">Day</th>
                        <th>Start</th>
                        <th>Break</th>
                        <th>End</th>
                        <th>Total Hours</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    <?php foreach ($daysOfWeek as $day): ?>
                        <?php $schedule = getIScheduleTemplateDayOfWeekByDayInternId($day, $intern['intern_id']); ?>

                        <?php
                        if (!empty($schedule)) {
                            $start      = $schedule['start_time'];
                            $end        = $schedule['end_time'];
                            $breakStart = $schedule['break_start'] ?? null;
                            $breakEnd   = $schedule['break_end'] ?? null;

                            $totalSeconds = strtotime($end) - strtotime($start);
                            if ($breakStart && $breakEnd) {
                                $totalSeconds -= (strtotime($breakEnd) - strtotime($breakStart));
                            }
                            $hours = floor($totalSeconds / 3600);
                            $minutes = floor(($totalSeconds % 3600) / 60);

                            $totalHoursFormatted = "{$hours} hr" . ($hours != 1 ? 's' : '') .
                                ($minutes > 0 ? " {$minutes} min" . ($minutes != 1 ? 's' : '') : '');
                        }
                        ?>

                        <tr class="border-b border-gray-200">
                            <td class="py-2">
                                <?= $day . PHP_EOL; ?>
                            </td>
                            <td>
                                <?= (!empty($schedule) ? date('g:i A', strtotime($schedule['start_time'])) : '<span class="text-gray-400 italic">Not set</span>') . PHP_EOL; ?>
                            </td>

                            <td>
                                <?php if (!empty($schedule) && $breakStart && $breakEnd): ?>
                                    <?= date('g:i A', strtotime($breakStart)); ?> - <?= date('g:i A', strtotime($breakEnd)) . PHP_EOL; ?>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">—</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= (!empty($schedule) ? date('g:i A', strtotime($schedule['end_time'])) : '<span class="text-gray-400 italic">Not set</span>') . PHP_EOL; ?>
                            </td>

                            <td>
                                <?= (!empty($schedule)
                                    ? $totalHoursFormatted
                                    : '<span class="text-gray-400 italic">—</span>') . PHP_EOL; ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>

        </div>

    </main>
</div>

<?php require_once TEMP . '/footer.php'; ?>