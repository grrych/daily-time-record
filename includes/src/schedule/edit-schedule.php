<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $scheduleId = trim($_POST['scheduleId'] ?? '');
    $startTime  = trim($_POST['startTime'] ?? '');
    $endTime    = trim($_POST['endTime'] ?? '');

    $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    // 1. Validate required fields
    if (empty($scheduleId) || empty($startTime) || empty($endTime)) {
        setFlash('error', 'All fields are required.');
        redirect(BASE_URL . '/schedule.php');
    }

    // 3. Validate start time
    if (
        !preg_match('/^(0[6-9]|1[0-2]):[0-5][0-9]$/', $startTime) &&
        !preg_match('/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $startTime)
    ) {
        setFlash('error', 'Invalid start time format.');
        redirect(BASE_URL . '/schedule.php');
    }

    // 4. Validate end time format and minimum value
    if (
        !preg_match('/^(0[6-9]|1[0-2]):[0-5][0-9]$/', $endTime) &&
        !preg_match('/^(1[3-9]|2[0-3]):[0-5][0-9]$/', $endTime)
    ) {
        setFlash('error', 'Invalid end time format. End time must be 13:00 or later.');
        redirect(BASE_URL . '/schedule.php');
    }

    // 5. Validate end time is after start time
    $startTimestamp = strtotime($startTime);
    $endTimestamp   = strtotime($endTime);

    if ($endTimestamp <= $startTimestamp) {
        setFlash('error', 'End time must be after start time.');
        redirect(BASE_URL . '/schedule.php');
    }

    // 6. Save schedule
    try {
        updateScheduleByScheduleInternId($startTime, $endTime, $scheduleId, $_SESSION['intern_id']);
        setFlash('success', 'Schedule updated successfully!');
        redirect(BASE_URL . '/schedule.php');
    } catch (Throwable $e) {
        errorLog($e);
        setFlash('error', 'Something went wrong. Please try again.');
        redirect(BASE_URL . '/schedule.php');
    }
}
