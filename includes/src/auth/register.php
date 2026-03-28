<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $studentNumber   = trim($_POST['studentNumber'] ?? '');
    $firstName       = trim($_POST['firstName'] ?? '');
    $middleName      = trim($_POST['middleName'] ?? '');
    $lastName        = trim($_POST['lastName'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $requiredHours   = trim($_POST['requiredHours'] ?? '');
    $password        = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    $required = [
        $studentNumber,
        $firstName,
        $lastName,
        $email,
        $requiredHours,
        $password,
        $confirmPassword
    ];

    foreach ($required as $input) {
        if (empty($input)) {
            setFlash('error', 'Please fill in all required fields.');
            redirect(BASE_URL . '/register.php');
        }
    }

    // Student Number validation
    if (strlen($studentNumber) < 5 || strlen($studentNumber) > 12) {
        setFlash('error', 'Student Number must be between 5 and 12 characters.');
        redirect(BASE_URL . '/register.php');
    }

    // Allow only letters, numbers, dash (e.g. 2024-001)
    if (!preg_match('/^[A-Za-z0-9\-]+$/', $studentNumber)) {
        setFlash('error', 'Student Number can only contain letters, numbers, and dashes.');
        redirect(BASE_URL . '/register.php');
    }

    // First Name
    if (strlen($firstName) < 2 || strlen($firstName) > 100) {
        setFlash('error', 'First name must be between 2 and 100 characters.');
        redirect(BASE_URL . '/register.php');
    }

    // Middle Name (optional)
    if ($middleName !== '' && (strlen($middleName) < 2 || strlen($middleName) > 100)) {
        setFlash('error', 'Middle name must be between 2 and 100 characters.');
        redirect(BASE_URL . '/register.php');
    }

    // Last Name
    if (strlen($lastName) < 2 || strlen($lastName) > 100) {
        setFlash('error', 'Last name must be between 2 and 100 characters.');
        redirect(BASE_URL . '/register.php');
    }

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 60) {
        setFlash('error', 'Invalid email address.');
        redirect(BASE_URL . '/register.php');
    }

    // Required hours validation
    if (!filter_var($requiredHours, FILTER_VALIDATE_INT)) {
        setFlash('error', 'Required hours must be a number.');
        redirect(BASE_URL . '/register.php');
    }

    // Password validation
    if (strlen($password) < 8 || strlen($password) > 60) {
        setFlash('error', 'Password must be between 8 and 60 characters.');
        redirect(BASE_URL . '/register.php');
    }

    if ($password !== $confirmPassword) {
        setFlash('error', 'Passwords do not match.');
        redirect(BASE_URL . '/register.php');
    }

    try {
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insert intern
        $insertId = createIntern(
            $studentNumber,
            $firstName,
            $middleName,
            $lastName,
            $email,
            $requiredHours
        );

        // Create user
        createUser($insertId, $passwordHash);
    } catch (Throwable $e) {
        errorLog($e);
        setFlash('error', 'Something went wrong. Please try again.');
        redirect(BASE_URL . '/register.php');
    }

    // Success message
    setFlash('success', 'Account created successfully! You can now login.');

    redirect(BASE_URL . '/register.php');
}
