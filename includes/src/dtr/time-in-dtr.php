<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['timeIn'])) {

    $now      = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $day      = $now->format('l');
    $workDate = $now->format('Y-m-d');
    $timeIn   = $now->format('H:i:s');

    try {

        // Check if schedule exists
        $scheduleExisting = getScheduleByDayOfWeek($day, $_SESSION['intern_id']);

        // NO schedule for today
        if (!$scheduleExisting) {
            setFlash('error', 'No schedule set for today. Please set your schedule first before time in.');
            redirect(BASE_URL . '/daily-time-record.php');
        }

        // Check if already timed in
        $drtExisting = getDtrByWorkDateInternId($workDate, $_SESSION['intern_id']);

        if ($drtExisting) {
            setFlash('error', 'You have already timed in today.');
            redirect(BASE_URL . '/daily-time-record.php');
        }

        // Create DTR
        createDtr($_SESSION['intern_id'], $workDate, $timeIn, NULL, NULL);

    } catch (Throwable $e) {
        errorLog($e);
        setFlash('error', 'Something went wrong. Please try again.');
        redirect(BASE_URL . '/daily-time-record.php');
    }

    setFlash('success', 'You have successfully timed in at ' . $now->format('g:i A') . '.');
    redirect(BASE_URL . '/daily-time-record.php');
}
?>