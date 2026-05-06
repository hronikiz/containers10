<?php

require_once __DIR__ . '/modules/Database.php';
require_once __DIR__ . '/config.php';

$dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['database']};charset=utf8";

$db = new Database(
    $dsn,
    $config['db']['username'],
    $config['db']['password']
);

echo "Connected to database successfully!";