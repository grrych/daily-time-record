<?php
function createSchedule($internId, $dayOfWeek, $startTime, $endTime)
{
    $conn     = connection();

    try {
        $sql = "INSERT INTO schedules(intern_id, day_of_week, start_time, end_time)
                VALUES (?, ?, ?, ?);";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('isss', $internId, $dayOfWeek, $startTime, $endTime);
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
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

function updateScheduleByScheduleInternId($startTime, $endTime, $scheduleId, $internId)
{
    $conn     = connection();

    try {
        $sql = "UPDATE schedules SET start_time = ?, end_time = ?
                WHERE schedule_id = ? AND intern_id = ?;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssii', $startTime, $endTime, $scheduleId, $internId);
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
}