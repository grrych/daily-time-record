<?php
require_once __DIR__ . '/../../header.php';
requireLogin();

require_once SRC . '/config/connection.php';
require_once SRC . '/intern/intern.php';
require_once SRC . '/dtr/dtr.php';
require_once SRC . '/schedule-template/schedule-template.php';

require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;

$timeZone = new DateTimeZone('Asia/Manila');

$internData       = getInternById($_SESSION['intern_id']);
$dtrRecords       = getAllDtrAscByInternId($internData['intern_id']);
$scheduleTemplate = getScheduleTemplateByInternId($internData['intern_id']);


/**
 * SAME FUNCTION FROM YOUR MAIN PAGE (IMPORTANT)
 */
function calculateWorkedSeconds($timeInStr, $timeOutStr, $schedule, $timeZone)
{
    if (empty($timeInStr) || empty($timeOutStr)) return 0;

    $timeIn  = new DateTime($timeInStr, $timeZone);
    $timeOut = new DateTime($timeOutStr, $timeZone);

    $seconds = $timeOut->getTimestamp() - $timeIn->getTimestamp();

    if ($seconds <= 0) return 0;

    // Apply schedule limit
    if (!empty($schedule['start_time']) && !empty($schedule['end_time'])) {

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

    // BREAK DEDUCTION (THIS IS WHY YOU WERE GETTING 9 HOURS)
    if (isset($schedule['break_start']) && isset($schedule['break_end'])) {

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

// START OUTPUT BUFFER
ob_start();
?>

<style>
    body {
        font-family: Arial, sans-serif;
    }

    h2 {
        text-align: center;
        margin-bottom: 5px;
    }

    .info {
        margin-bottom: 15px;
        font-size: 12px;
    }

    .info p {
        margin: 2px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid black;
        padding: 6px;
        font-size: 12px;
        text-align: center;
    }

    .header-line {
        margin: 10px 0;
        border-top: 1px solid black;
    }
</style>

<h2>Daily Time Record</h2>

<div class="info">
    <p><strong>Name:</strong> <?= e($internData['first_name'] ?? '') . ' '. e($internData['middle_name'] ?? '') .' ' . e($internData['last_name'] ?? ''); ?></p>
    <p><strong>Student Number:</strong> <?= e($internData['student_number'] ?? '—'); ?></p>
</div>

<table>
    <tr>
        <th>Date</th>
        <th>Time In</th>
        <th>Time Out</th>
        <th>Total Hours</th>
        <th>Minutes</th>
    </tr>

    <?php foreach ($dtrRecords as $dtr):
        $timeIn  = !empty($dtr['time_in']) ? new DateTime($dtr['time_in'], $timeZone) : null;
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
        } else {
            $hours = 0;
            $minutes = 0;
        }
    ?>
        <tr>
            <td><?= date('F j, Y', strtotime($dtr['work_date'])) ?></td>
            <td><?= $timeIn ? $timeIn->format('g:i A') : '—' ?></td>
            <td><?= $timeOut ? $timeOut->format('g:i A') : '—' ?></td>
            <td><?= $hours ?></td>
            <td><?= $minutes ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
$html = ob_get_clean();

// CLEAR BUFFER
if (ob_get_length()) ob_end_clean();

// GENERATE PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = "DTR_Report_" . date("Y-m-d_H-i-s") . ".pdf";

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');

echo $dompdf->output();
exit;
