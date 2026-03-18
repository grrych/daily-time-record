<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';

    $errors = [
        'empty'   => 'Email and password are required.',
        'invalid' => 'Invalid credentials.'
    ];

    // Basic validation
    if (empty($email) || empty($password)) {
        setFlash('error', $errors['empty']);
        redirect(BASE_URL . '/login.php');
    }

    if (strlen($email) > 60 || strlen($password) > 60) {
        setFlash('error', $errors['invalid']);
        redirect(BASE_URL . '/login.php');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlash('error', $errors['invalid']);
        redirect(BASE_URL . '/login.php');
    }

    try {

        $intern = getInternByEmail($email);

        if (!password_verify($password, $intern['password_hash'])) {
            setFlash('error', $errors['invalid']);
            redirect(BASE_URL . '/login.php');
        }

        $_SESSION['intern_id'] = $intern['intern_id'];

        redirect(BASE_URL . '/index.php');
    } catch (Throwable $e) {

        errorLog($e);
        setFlash('error', 'Something went wrong. Please try again.');
        redirect(BASE_URL . '/login.php');
    }
}
