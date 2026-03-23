<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['timeOut'])) {

    $now      = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $workDate = $now->format('Y-m-d');
    $timeOut  = '17:10:00'; // $now->format('H:i:s');

    try {

        $existing = getDtrByWorkDateInternId($workDate, $_SESSION['intern_id']);

        // No Time In found
        if (!$existing) {
            setFlash('error', 'You must time in before timing out.');
            redirect(BASE_URL . '/daily-time-record.php');
        }

        // Already timed out
        if (!empty($existing['time_out'])) {
            setFlash('error', 'You have already timed out today.');
            redirect(BASE_URL . '/daily-time-record.php');
        }

        // Convert times to DateTime
        $timeIn    = new DateTime($existing['time_in']);
        $timeOutDT = new DateTime($timeOut);

        // Calculate interval in seconds
        $interval      = $timeIn->diff($timeOutDT);
        $totalSeconds  = ($interval->days * 86400) + ($interval->h * 3600) + ($interval->i * 60) + $interval->s;

        // Minimum required seconds (3 minutes)
        $minSeconds = 180;

        if ($totalSeconds < $minSeconds) {
            $remaining = $minSeconds - $totalSeconds;

            // Convert to human readable
            if ($remaining >= 60) {
                $minutes = floor($remaining / 60);
                $seconds = $remaining % 60;
                $timeMsg = $minutes . ' minute' . ($minutes > 1 ? 's' : '');
                if ($seconds > 0) {
                    $timeMsg .= ' ' . $seconds . ' second' . ($seconds > 1 ? 's' : '');
                }
            } else {
                $timeMsg = $remaining . ' second' . ($remaining > 1 ? 's' : '');
            }

            setFlash('error', 'You cannot time out yet. Please wait ' . $timeMsg . ' before timing out.');
            redirect(BASE_URL . '/daily-time-record.php');
        }

        // Convert to decimal hours
        $totalHours = ($interval->days * 24) + $interval->h + ($interval->i / 60) + ($interval->s / 3600);
        $totalHours = round($totalHours, 2);

        updateDtrTodayByWorkDateInternId(
            $timeOut,
            $totalHours,
            $workDate,
            $_SESSION['intern_id']
        );
    } catch (Throwable $e) {
        errorLog($e);
        setFlash('error', 'Something went wrong. Please try again.');
        redirect(BASE_URL . '/daily-time-record.php');
    }

    setFlash(
        'success',
        'You have successfully timed out at ' . $now->format('g:i A') .
            '. Total hours worked today: ' . $totalHours . ' hrs.'
    );

    redirect(BASE_URL . '/daily-time-record.php');
}
