<?php
function createScheduleTemplate($startTime, $breakStart, $breakEnd, $endTime, $internId)
{
    $conn     = connection();
    $insertId = null;

    try {
        $sql = "INSERT INTO schedule_templates(start_time, break_start, break_end, end_time, intern_id)
                VALUES (?, ?, ?, ?, ?);";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $startTime, $breakStart, $breakEnd, $endTime, $internId);
        $stmt->execute();

        $insertId = $conn->insert_id;
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
    return $insertId;
}

function getScheduleByDayOfWeek($dayOfWeek, $internId)
{
    $conn             = connection();
    $scheduleTemplate = [];

    try {
        $sql = "SELECT * FROM schedule_templates
                WHERE day_of_week = ? AND intern_id = ?
                ORDER BY schedule_id DESC 
                LIMIT 1;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $dayOfWeek, $internId);
        $stmt->execute();
        $result = $stmt->get_result();
        $scheduleTemplate = $result->fetch_assoc();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
    return $scheduleTemplate;
}

function getScheduleTemplateByInternId($internId)
{
    $conn             = connection();
    $scheduleTemplate = [];

    try {
        $sql = "SELECT * FROM schedule_templates
                WHERE intern_id = ?
                ORDER BY template_id DESC 
                LIMIT 1;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $internId);
        $stmt->execute();
        $result = $stmt->get_result();
        $scheduleTemplate = $result->fetch_assoc();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
    return $scheduleTemplate;
}

function getScheduleTemplateDayOfWeekByDayInternId($day, $internId)
{
    $conn             = connection();
    $scheduleTemplate = [];

    try {
        $sql = "SELECT 
                    sct.*,
                    ins.day_of_week
                FROM schedule_templates AS sct
                LEFT JOIN intern_schedules AS ins ON sct.template_id = ins.template_id
                WHERE ins.day_of_week = ? AND sct.intern_id = ?
                ORDER BY sct.template_id DESC
                LIMIT 1;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $day, $internId);
        $stmt->execute();
        $result = $stmt->get_result();
        $scheduleTemplate = $result->fetch_assoc();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
    return $scheduleTemplate;
}

function updateScheduleTemplateByInternId($startTime, $breakStart, $breakEnd, $endTime, $internId)
{
    $conn     = connection();

    try {
        $sql = "UPDATE schedule_templates SET start_time = ?, break_start = ?, break_end = ?, end_time = ?
                WHERE intern_id = ?;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $startTime, $breakStart, $breakEnd, $endTime, $internId);
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
}