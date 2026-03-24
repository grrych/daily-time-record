<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {

    $timeIn  = trim($_POST['timeIn'] ?? '');
    $timeOut = trim($_POST['timeOut'] ?? '');

    if (empty($timeIn) || empty($timeOut)) {
        setFlash('error', '');
        redirect(BASE_URL . '/daily-time-record.php');
    }

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
