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

function deleteInternScheduleByTemplateId($templateId)
{
    $conn     = connection();

    try {
        $sql = "DELETE FROM intern_schedules WHERE template_id = ?;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $templateId);
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
}