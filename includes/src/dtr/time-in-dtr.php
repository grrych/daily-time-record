<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['timeIn'])) {
    $now = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $workDate = $now->format('Y-m-d');
    $timeIn   = $now->format('H:i:s');

    try {
        $existing = getDtrByWorkDateInternId($workDate, $_SESSION['intern_id']);

        if ($existing) {
            setFlash('error', 'You have already timed in today.');
            redirect(BASE_URL . '/daily-time-record.php');
        }

        createDtr($_SESSION['intern_id'], $workDate, $timeIn, NULL, NULL);
    } catch (Throwable $e) {
        errorLog($e);
        setFlash('error', 'Something went wrong. Please try again.');
        redirect(BASE_URL . '/daily-time-record.php');
    }

    setFlash('success', 'You have successfully timed in at ' . $now->format('g:i A') . '.');
    redirect(BASE_URL . '/daily-time-record.php');
}
