<?php
function createScheduleTemplate($startTime, $breakStart, $breakEnd, $endTime)
{
    $conn     = connection();
    $insertId = null;

    try {
        $sql = "INSERT INTO schedule_templates(start_time, break_start, break_end, end_time)
                VALUES (?, ?, ?, ?);";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $startTime, $breakStart, $breakEnd, $endTime);
        $stmt->execute();

        $insertId = $conn->insert_id;
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
    return $insertId;
}

function getScheduleByDayOfWeek($dayOfWeek, $internId)
{
    $conn     = connection();
    $schedule = [];

    try {
        $sql = "SELECT * FROM schedules
                WHERE day_of_week = ? AND intern_id = ?
                ORDER BY schedule_id DESC 
                LIMIT 1;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $dayOfWeek, $internId);
        $stmt->execute();
        $result = $stmt->get_result();
        $schedule = $result->fetch_assoc();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
    return $schedule;
}

function updateScheduleByScheduleInternId($startTime, $breakStart, $breakEnd, $endTime, $scheduleId, $internId)
{
    $conn     = connection();

    try {
        $sql = "UPDATE schedules SET start_time = ?, break_start = ?, break_end = ?, end_time = ?
                WHERE schedule_id = ? AND intern_id = ?;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssii', $startTime, $breakStart, $breakEnd, $endTime, $scheduleId, $internId);
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
}
