<?php
function createDtr($internId, $workDate, $timeIn, $timeOut, $totalHours)
{
    $conn     = connection();

    try {
        $sql = "INSERT INTO dtr_records(intern_id, work_date, time_in, time_out, total_hours)
                VALUES (?, ?, ?, ?, ?);";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('issss', $internId, $workDate, $timeIn, $timeOut, $totalHours);
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
}

function getAllDtrByInternId($internId)
{
    $conn = connection();
    $dtr  = [];

    try {
        $sql = "SELECT * FROM dtr_records
                WHERE intern_id = ?
                ORDER BY work_date ASC;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $internId);
        $stmt->execute();
        $result = $stmt->get_result();
        $dtr = $result->fetch_all(MYSQLI_ASSOC);
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
    return $dtr;
}

function getDtrByWorkDateInternId($workDate, $internId)
{
    $conn = connection();
    $dtr  = [];

    try {
        $sql = "SELECT * FROM dtr_records
                WHERE work_date = ? AND intern_id = ?
                ORDER BY dtr_id DESC 
                LIMIT 1;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $workDate, $internId);
        $stmt->execute();
        $result = $stmt->get_result();
        $dtr = $result->fetch_assoc();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
    return $dtr;
}

function getDtrThisWeek($internId, $weekStart, $weekEnd)
{
    $conn = connection();
    $dtr = [];
    try {
        $sql = "SELECT * FROM dtr_records
                WHERE intern_id = ?
                AND work_date BETWEEN ? AND ?
                ORDER BY work_date ASC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iss', $internId, $weekStart, $weekEnd);
        $stmt->execute();
        $result = $stmt->get_result();

        $dtr = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close(); // clean up
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }

    return $dtr;
}

function updateDtrTodayByWorkDateInternId($timeOut, $totalHours, $workDate, $internId)
{
    $conn     = connection();

    try {
        $sql = "UPDATE dtr_records 
                SET time_out = ?, 
                    total_hours = ?
                WHERE work_date = ? AND intern_id = ?;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssi', $timeOut, $totalHours, $workDate, $internId);
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
}
