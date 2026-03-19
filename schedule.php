<?php
require_once __DIR__ . '/header.php';
requireLogin();

require_once SRC . '/config/connection.php';
require_once SRC . '/intern/intern.php';
require_once SRC . '/schedule/schedule.php';
require_once SRC . '/schedule/add-schedule.php';
require_once SRC . '/schedule/edit-schedule.php';

require_once TEMP . '/header.php';

flashMessage();

// ---------- FIXED generateTimeOptions ----------
if (!function_exists('generateTimeOptions')) {
    function generateTimeOptions($selected = null)
    {
        $start = strtotime('06:00');
        $end   = strtotime('22:00');

        // ✅ Normalize DB time (removes seconds like 08:00:00 → 08:00)
        if (!empty($selected)) {
            $selected = date('H:i', strtotime($selected));
        }

        for ($t = $start; $t <= $end; $t += 15 * 60) {
            $timeValue = date('H:i', $t);
            $timeText  = date('g:i A', $t);
            $selectedAttr = ($selected === $timeValue) ? 'selected' : '';

            echo "<option value='{$timeValue}' {$selectedAttr}>{$timeText}</option>";
        }
    }
}
?>

<div class="min-h-screen bg-gray-100 flex w-full">

    <?php require_once TEMP . '/sidebar.php'; ?>

    <main class="flex-1 px-8 pt-4 pb-14 max-h-screen overflow-y-auto mb-6">

        <?php require_once TEMP . '/navbar.php'; ?>

        <h1 class="text-2xl font-bold text-gray-800 mb-6">My Schedule</h1>

        <?php
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        ?>

        <!-- Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-6">
            <?php foreach ($daysOfWeek as $day): ?>
                <?php $schedule = getScheduleByDayOfWeek($day, $intern['intern_id']); ?>

                <div class="bg-white p-5 rounded-lg shadow">
                    <p class="text-sm text-gray-500"><?= $day ?></p>

                    <?php if (empty($schedule)): ?>
                        <p class="text-gray-400 text-sm mt-2">No schedule</p>
                    <?php else: ?>
                        <h3 class="text-lg font-semibold mt-1">
                            <?= date('h:i A', strtotime($schedule['start_time'])); ?>
                            -
                            <?= date('h:i A', strtotime($schedule['end_time'])); ?>
                        </h3>

                        <?php if (!empty($schedule['break_start']) && !empty($schedule['break_end'])): ?>
                            <p class="text-xs text-gray-500 mt-1">
                                Break:
                                <?= date('h:i A', strtotime($schedule['break_start'])); ?>
                                -
                                <?= date('h:i A', strtotime($schedule['break_end'])); ?>
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
                <thead class="border-b">
                    <tr>
                        <th class="py-2">Day</th>
                        <th>Start</th>
                        <th>Break</th>
                        <th>End</th>
                        <th>Total Hours</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    <?php foreach ($daysOfWeek as $day): ?>
                        <?php $schedule = getScheduleByDayOfWeek($day, $_SESSION['intern_id']); ?>

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

                        <tr>
                            <td class="py-2"><?= $day ?></td>
                            <td><?= !empty($schedule) ? date('g:i A', strtotime($schedule['start_time'])) : '<span class="text-gray-400 italic">Not set</span>'; ?></td>

                            <td>
                                <?php if (!empty($schedule) && $breakStart && $breakEnd): ?>
                                    <?= date('g:i A', strtotime($breakStart)); ?> - <?= date('g:i A', strtotime($breakEnd)); ?>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">—</span>
                                <?php endif; ?>
                            </td>

                            <td><?= !empty($schedule) ? date('g:i A', strtotime($schedule['end_time'])) : '<span class="text-gray-400 italic">Not set</span>'; ?></td>

                            <td>
                                <?= !empty($schedule)
                                    ? $totalHoursFormatted
                                    : '<span class="text-gray-400 italic">—</span>'; ?>
                            </td>

                            <td>
                                <button
                                    class="<?= empty($schedule) ? 'px-4 py-1 bg-blue-600 text-white rounded' : 'px-3 py-1 border border-blue-600 text-blue-600 rounded' ?>"
                                    data-modal-open="modal-<?= $day ?>">
                                    <?= empty($schedule) ? 'Set' : 'Edit' ?>
                                </button>
                            </td>
                        </tr>

                        <!-- MODAL -->
                        <div id="modal-<?= $day ?>" data-modal
                            class="fixed inset-0 hidden flex items-center justify-center bg-black/50 z-50">

                            <div data-modal-content
                                class="bg-white w-full max-w-md rounded-lg p-6 transform scale-90 opacity-0 transition-all duration-300">

                                <h2 class="text-lg font-semibold mb-4"><?= $day ?> Schedule</h2>

                                <form action="<?= BASE_URL ?>/schedule.php" method="POST" class="space-y-4">

                                    <input type="hidden" name="day" value="<?= $day ?>">
                                    <input type="hidden" name="<?= empty($schedule) ? 'set' : 'edit' ?>">

                                    <?php if (!empty($schedule)): ?>
                                        <input type="hidden" name="scheduleId" value="<?= $schedule['schedule_id'] ?>">
                                    <?php endif; ?>

                                    <!-- Start -->
                                    <div>
                                        <label class="text-sm">Start</label>
                                        <select name="startTime" class="w-full border rounded px-3 py-2">
                                            <option value="">-- Select Start --</option>
                                            <?php generateTimeOptions($schedule['start_time'] ?? null); ?>
                                        </select>
                                    </div>

                                    <!-- Break Start -->
                                    <div>
                                        <label class="text-sm">Break Start</label>
                                        <select name="breakStart" class="w-full border rounded px-3 py-2">
                                            <option value="">-- Select Break Start --</option>
                                            <?php generateTimeOptions($schedule['break_start'] ?? null); ?>
                                        </select>
                                    </div>

                                    <!-- Break End -->
                                    <div>
                                        <label class="text-sm">Break End</label>
                                        <select name="breakEnd" class="w-full border rounded px-3 py-2">
                                            <option value="">-- Select Break End --</option>
                                            <?php generateTimeOptions($schedule['break_end'] ?? null); ?>
                                        </select>
                                    </div>

                                    <!-- End -->
                                    <div>
                                        <label class="text-sm">End</label>
                                        <select name="endTime" class="w-full border rounded px-3 py-2">
                                            <option value="">-- Select End --</option>
                                            <?php generateTimeOptions($schedule['end_time'] ?? null); ?>
                                        </select>
                                    </div>

                                    <div class="flex justify-end gap-2 pt-4">
                                        <button type="button" data-modal-close
                                            class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                                    </div>

                                </form>

                            </div>
                        </div>

                    <?php endforeach; ?>

                </tbody>
            </table>

        </div>

    </main>
</div>

<?php require_once TEMP . '/footer.php'; ?>