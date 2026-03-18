<?php

function createUser($internId, $passwordHash)
{
    $conn = connection();
    try {
        $sql = "INSERT INTO users(intern_id, password_hash)
                VALUES (?, ?);";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('is', $internId, $passwordHash);
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
}

function getUserByInternId($internId)
{
    $conn = connection();
    $user = [];

    try {
        $sql = "SELECT * FROM users WHERE intern_id = ? 
                ORDER BY user_id DESC
                LIMIT 1;";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $internId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
    return $user;
}
