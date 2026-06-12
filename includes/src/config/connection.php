<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

function connection()
{
    $connection = null;

    try {
        $config = [
            'hostname'  => $_ENV['HOST_NAME'] ?? 'localhost',
            'port'      => $_ENV['PORT'] ?? 3306,
            'username'  => $_ENV['USERNAME'] ?? 'root',
            'password'  => $_ENV['PASSWORD'] ?? '',
            'db_name'   => $_ENV['DB_NAME'] ?? ''
        ];

        $connection = new mysqli(
            $config['hostname'],
            $config['username'],
            $config['password'],
            $config['db_name'],
            $config['port']
        );
    } catch (mysqli_sql_exception $e) {
        errorLog($e);
    }
    return $connection;
}