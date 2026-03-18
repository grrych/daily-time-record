<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId     = trim($_POST['studentId'] ?? '');
    $firstName     = trim($_POST['firstName'] ?? '');
    $middleName    = trim($_POST['middleName'] ?? '');
    $lastName      = trim($_POST['lastName'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $requiredHours = trim($_POST['requiredHours'] ?? '');

    $required = [
        $studentId,
        $firstName,
        $lastName,
        $email,
        $requiredHours,
    ];

    foreach ($required as $input) {
        if (empty($input)) {
            setFlash('error', 'Please fill in all required fields.');
            redirect(BASE_URL . '/profile.php?id=' . ($_SESSION['intern_id'] ?? ''));
        }
    }

    // Student ID validation
    if (!filter_var($studentId, FILTER_VALIDATE_INT) || strlen($studentId) > 12) {
        setFlash('error', 'Invalid Student ID.');
        redirect(BASE_URL . '/profile.php?id=' . ($_SESSION['intern_id'] ?? ''));
    }

    // First Name
    if (strlen($firstName) < 2 || strlen($firstName) > 100) {
        setFlash('error', 'First name must be between 2 and 100 characters.');
        redirect(BASE_URL . '/profile.php?id=' . ($_SESSION['intern_id'] ?? ''));
    }

    // Middle Name (optional)
    if ($middleName !== '' && (strlen($middleName) < 2 || strlen($middleName) > 100)) {
        setFlash('error', 'Middle name must be between 2 and 100 characters.');
        redirect(BASE_URL . '/profile.php?id=' . ($_SESSION['intern_id'] ?? ''));
    }

    // Last Name
    if (strlen($lastName) < 2 || strlen($lastName) > 100) {
        setFlash('error', 'Last name must be between 2 and 100 characters.');
        redirect(BASE_URL . '/profile.php?id=' . ($_SESSION['intern_id'] ?? ''));
    }

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 60) {
        setFlash('error', 'Invalid email address.');
        redirect(BASE_URL . '/profile.php?id=' . ($_SESSION['intern_id'] ?? ''));
    }

    // Required hours validation
    if (!filter_var($requiredHours, FILTER_VALIDATE_INT)) {
        setFlash('error', 'Required hours must be a number.');
        redirect(BASE_URL . '/profile.php?id=' . ($_SESSION['intern_id'] ?? ''));
    }

    try {
        // Update intern
        updateInternById(
            $studentId,
            $firstName,
            $middleName,
            $lastName,
            $email,
            $requiredHours,
            $_SESSION['intern_id'] ?? ''
        );
    } catch (Throwable $e) {
        errorLog($e);
        setFlash('error', 'Something went wrong. Please try again.');
        redirect(BASE_URL . '/profile.php?id=' . ($_SESSION['intern_id'] ?? ''));
    }

    // Success message
    setFlash('success', 'Profile updated successfully.');

    redirect(BASE_URL . '/profile.php');
}
