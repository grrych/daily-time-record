<?php
if (!empty($_SESSION['intern_id'])) {
    $intern = getInternById($_SESSION['intern_id']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OJT Daily Time Record</title>
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/main.css', false); ?>">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 overflow-hidden">