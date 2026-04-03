<?php

function createInternSchedule($templateId, $dayOfWeek)
{
    $conn     = connection();

    try {
        $sql = "INSERT INTO intern_schedules(template_id, day_of_week)
                VALUES (?, ?);";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('is', $templateId, $dayOfWeek);
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
}

function getInternScheduleByInternId($internId)
{
    $conn           = connection();
    $internSchedule = [];

    try {
        $sql = "SELECT * FROM intern_schedules
                WHERE intern_id = ?;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $internId);
        $stmt->execute();
        $result = $stmt->get_result();
        $internSchedule = $result->fetch_assoc();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
    return $internSchedule;
}

function getInternScheduleDayOfWeekByDayInternId($internId)
{
    $conn           = connection();
    $internSchedule = [];

    try {
        $sql = "SELECT 
                    ins.*, 
                    sct.start_time,
                    sct.break_start,
                    sct.break_end,
                    sct.end_time

                FROM intern_schedules AS ins
                LEFT JOIN schedule_templates AS sct ON ins.template_id = sct.template_id
                WHERE ins.intern_id = ?
                ORDER BY ins.schedule_id DESC
                LIMIT 1;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $internId);
        $stmt->execute();
        $result = $stmt->get_result();
        $internSchedule = $result->fetch_assoc();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
    return $internSchedule;
}
