<?php
function createIntern($studentId, $firstName, $middleName, $lastName, $email, $requiredHours)
{
    $conn     = connection();
    $insertId = null;

    try {
        $sql = "INSERT INTO interns(student_id, first_name, middle_name, last_name, email, required_hours)
                VALUES (?, ?, ?, ?, ?, ?);";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssi', $studentId, $firstName, $middleName, $lastName, $email, $requiredHours);
        $stmt->execute();
        $insertId = $conn->insert_id;
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
    return $insertId;
}

function getInternByEmail($email)
{
    $conn   = connection();
    $intern = [];

    try {
        $sql = "SELECT i.*, u.password_hash FROM interns i
                INNER JOIN users AS u ON i.intern_id = u.intern_id
                WHERE i.email = ? 
                ORDER BY i.intern_id DESC 
                LIMIT 1;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $intern = $result->fetch_assoc();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
    return $intern;
}

function getInternById($internId)
{
    $conn   = connection();
    $intern = [];

    try {
        $sql = "SELECT * FROM interns
                WHERE intern_id = ? 
                ORDER BY intern_id DESC 
                LIMIT 1;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $internId);
        $stmt->execute();
        $result = $stmt->get_result();
        $intern = $result->fetch_assoc();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
    return $intern;
}

function updateInternById(
    $studentId,
    $firstName,
    $middleName,
    $lastName,
    $email,
    $requiredHours,
    $internId
) {
    $conn     = connection();

    try {
        $sql = "UPDATE interns
                SET student_id = ?, 
                    first_name = ?, 
                    middle_name = ?, 
                    last_name = ?, 
                    email = ?, 
                    required_hours = ?
                WHERE intern_id = ?;";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'sssssii',
            $studentId,
            $firstName,
            $middleName,
            $lastName,
            $email,
            $requiredHours,
            $internId
        );
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
}
