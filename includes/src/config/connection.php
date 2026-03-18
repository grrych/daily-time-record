<?php

function connection()
{
    $connection = null;

    try {
        $config = [
            'hostname'  => 'localhost',
            'port'      => 3306,
            'username'  => 'root',
            'password'  => '',
            'db_name'   => 'dtr_db'
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
