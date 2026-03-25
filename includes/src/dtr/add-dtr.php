<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addEntry'])) {

    $workDate = trim($_POST['workDate'] ?? '');
    $timeIn   = trim($_POST['timeIn'] ?? '');
    $timeOut  = trim($_POST['timeOut'] ?? '');

    // Required fields
    if (empty($workDate) || empty($timeIn) || empty($timeOut)) {
        setFlash('error', 'All fields are required.');
        redirect(BASE_URL . '/daily-time-record.php');
    }

    // Validate date format (YYYY-MM-DD)
    $timezone = new DateTimeZone('Asia/Manila');

    // Prevent future date
    $today   = new DateTime('now', $timezone);
    $dateObj = DateTime::createFromFormat('Y-m-d', $workDate, $timezone);

    if ($dateObj > $today) {
        setFlash('error', 'Work date cannot be in the future.');
        redirect(BASE_URL . '/daily-time-record.php');
    }

    // Time format validation (HH:MM 24hr)
    $timePattern = '/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/';

    if (!preg_match($timePattern, $timeIn)) {
        setFlash('error', 'Invalid Time In format.');
        redirect(BASE_URL . '/daily-time-record.php');
    }

    if (!preg_match($timePattern, $timeOut)) {
        setFlash('error', 'Invalid Time Out format.');
        redirect(BASE_URL . '/daily-time-record.php');
    }

    // Convert to timestamps
    $timeInTimestamp  = strtotime($timeIn);
    $timeOutTimestamp = strtotime($timeOut);

    // Logical validation
    if ($timeOutTimestamp <= $timeInTimestamp) {
        setFlash('error', 'Time Out must be later than Time In.');
        redirect(BASE_URL . '/daily-time-record.php');
    }

    // Compute total hours
    $seconds = $timeOutTimestamp - $timeInTimestamp;
    $totalHours = round($seconds / 3600, 2); // decimal hours

    try {

        // Check duplicate record
        $dtrExisting = getDtrByWorkDateInternId($workDate, $_SESSION['intern_id']);

        if ($dtrExisting) {
            setFlash('error', 'You already have a record for this date.');
            redirect(BASE_URL . '/daily-time-record.php');
        }

        // Save with total hours
        createDtr(
            $_SESSION['intern_id'],
            $workDate,
            $timeIn,
            $timeOut,
            $totalHours
        );
    } catch (Throwable $e) {
        errorLog($e);
        setFlash('error', 'Something went wrong. Please try again.');
        redirect(BASE_URL . '/daily-time-record.php');
    }

    // Success message
    setFlash('success', "DTR saved! Total hours: {$totalHours} hrs.");
    redirect(BASE_URL . '/daily-time-record.php');
}
