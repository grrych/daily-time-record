<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';

    // Basic validation
    if (empty($email) || empty($password)) {
        setFlash('error', 'Email and password are required.');
        redirect(BASE_URL . '/login.php');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlash('error', 'Invalid email address.');
        redirect(BASE_URL . '/login.php');
    }

    try {

        $intern = getInternByEmail($email);

        if (!password_verify($password, $intern['password_hash'])) {
            setFlash('error', 'Invalid credentials.');
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
