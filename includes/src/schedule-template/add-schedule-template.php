<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set'])) {
    $daysOfWeek = $_POST['days'] ?? [];
    $startTime  = trim($_POST['startTime'] ?? '');
    $breakStart = trim($_POST['breakStart'] ?? '');
    $breakEnd   = trim($_POST['breakEnd'] ?? '');
    $endTime    = trim($_POST['endTime'] ?? '');

    $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    // 1. Validate required fields
    if (empty($daysOfWeek)) {
        setFlash('error', 'Please select at least one day.');
        redirect(BASE_URL . '/schedule.php');
    }

    if (empty($startTime) || empty($breakStart) || empty($breakEnd) || empty($endTime)) {
        setFlash('error', 'All time fields are required.');
        redirect(BASE_URL . '/schedule.php');
    }

    $trimDaysOfWeek = [];

    // 2. Validate day
    foreach ($daysOfWeek as $day) {
        $day = trim($day);

        if (!in_array($day, $validDays)) {
            setFlash('error', 'Invalid day selected.');
            redirect(BASE_URL . '/schedule.php');
        }

        $trimDaysOfWeek[] = $day;
    }

    $trimDaysOfWeek = array_unique($trimDaysOfWeek);

    // 3. Validate time format (HH:MM 24-hour)
    $timePattern = '/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/';

    if (!preg_match($timePattern, $startTime)) {
        setFlash('error', 'Invalid start time format.');
        redirect(BASE_URL . '/schedule.php');
    }

    if (!preg_match($timePattern, $breakStart) || !preg_match($timePattern, $breakEnd)) {
        setFlash('error', 'Invalid break time format.');
        redirect(BASE_URL . '/schedule.php');
    }

    if (!preg_match($timePattern, $endTime)) {
        setFlash('error', 'Invalid end time format.');
        redirect(BASE_URL . '/schedule.php');
    }

    // 4. Convert to timestamps
    $startTimestamp      = strtotime($startTime);
    $breakStartTimestamp = strtotime($breakStart);
    $breakEndTimestamp   = strtotime($breakEnd);
    $endTimestamp        = strtotime($endTime);

    // 5. Validate start < end
    if ($endTimestamp <= $startTimestamp) {
        setFlash('error', 'End time must be after start time.');
        redirect(BASE_URL . '/schedule.php');
    }

    // 6. Validate break logic
    if ($breakStartTimestamp <= $startTimestamp) {
        setFlash('error', 'Break start must be after start time.');
        redirect(BASE_URL . '/schedule.php');
    }

    if ($breakEndTimestamp <= $breakStartTimestamp) {
        setFlash('error', 'Break end must be after break start.');
        redirect(BASE_URL . '/schedule.php');
    }

    if ($breakEndTimestamp >= $endTimestamp) {
        setFlash('error', 'Break end must be before end time.');
        redirect(BASE_URL . '/schedule.php');
    }

    // 7. Save schedule
    try {
        $isinternScheduleExist = getInternScheduleByInternId($_SESSION['intern_id']);

        if ($isinternScheduleExist) {
            setFlash('error', 'You already have a schedule set.');
            redirect(BASE_URL . '/schedule.php');
        }

        $insertId = createScheduleTemplate(
            $startTime,
            $breakStart,
            $breakEnd,
            $endTime,
            $_SESSION['intern_id']
        );

        foreach ($trimDaysOfWeek as $day) {
            createInternSchedule(
                $insertId,
                $day
            );
        }

        setFlash('success', 'Schedule set successfully!');
        redirect(BASE_URL . '/schedule.php');
    } catch (Throwable $e) {
        errorLog($e);
        setFlash('error', 'Something went wrong. Please try again.');
        redirect(BASE_URL . '/schedule.php');
    }
}
