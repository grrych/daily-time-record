<?php

function createInternSchedule($internId, $templateId, $dayOfWeek)
{
    $conn     = connection();

    try {
        $sql = "INSERT INTO intern_schedules(intern_id, template_id, day_of_week)
                VALUES (?, ?, ?);";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iis', $internId, $templateId, $dayOfWeek);
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
