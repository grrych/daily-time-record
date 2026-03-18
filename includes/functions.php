<?php
function errorLog($error)
{
    if ($error instanceof Throwable) {
        $message = "[" . date('Y-m-d H:i:s') . "] "
            . "Exception: " . $error->getMessage()
            . " in " . $error->getFile()
            . " on line " . $error->getLine() . PHP_EOL;
    } else {
        $message = "[" . date('Y-m-d H:i:s') . "] Error: " . $error . PHP_EOL;
    }

    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $logFile = $logDir . '/error.log';
    file_put_contents($logFile, $message, FILE_APPEND);
}

function redirect($path)
{
    header("Location: " . $path);
    exit;
}

function sessionStart()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Set a flash message
 */
function setFlash($type, $message)
{
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Display flash message
 */
function flashMessage()
{
    if (!empty($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_message']['type'];
        $message = htmlspecialchars($_SESSION['flash_message']['message']);

        $colors = [
            'success' => 'bg-green-500',
            'error' => 'bg-red-500',
            'warning' => 'bg-yellow-500',
            'info' => 'bg-blue-500'
        ];

        $color = $colors[$type] ?? 'bg-gray-500';

        echo '
        <div id="flash-message"
            class="fixed top-5 right-5 z-40 ' . $color . ' text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-4 transition-opacity duration-500">

            <span>' . $message . '</span>

            <button onclick="closeFlash()" class="font-bold">✕</button>
        </div>
        ';

        unset($_SESSION['flash_message']);
    }
}

function requireLogin()
{
    if (!isset($_SESSION['intern_id']) || empty($_SESSION['intern_id'])) {
        redirect(BASE_URL . '/login.php?redirect_to=' . urlencode(BASE_URL . '/login.php?auth=1'));
        exit();
    }
}
