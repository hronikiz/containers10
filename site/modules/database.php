<?php

class Database {
    private $pdo;

    public function __construct(string $dsn, string $username, string $password) {
        $this->pdo = new PDO($dsn, $username, $password);
    }
}