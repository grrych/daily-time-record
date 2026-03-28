<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentNumber = trim($_POST['studentNumber'] ?? '');
    $firstName     = trim($_POST['firstName'] ?? '');
    $middleName    = trim($_POST['middleName'] ?? '');
    $lastName      = trim($_POST['lastName'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $requiredHours = trim($_POST['requiredHours'] ?? '');

    $required = [
        $studentNumber,
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

    // Student Number validation
    if (strlen($studentNumber) < 5 || strlen($studentNumber) > 12) {
        setFlash('error', 'Student Number must be between 5 and 12 characters.');
        redirect(BASE_URL . '/profile.php?id=' . ($_SESSION['intern_id'] ?? ''));
    }

    // Allow only letters, numbers, dash (e.g. 2024-001)
    if (!preg_match('/^[A-Za-z0-9\-]+$/', $studentNumber)) {
        setFlash('error', 'Student Number can only contain letters, numbers, and dashes.');
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
            $studentNumber,
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
