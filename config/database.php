<?php
declare(strict_types=1);

// Replace these placeholders with the exact credentials from your hosting control panel.
const DB_HOST = 'YOUR_DATABASE_HOST';
const DB_NAME = 'YOUR_DATABASE_NAME';
const DB_USER = 'YOUR_DATABASE_USERNAME';
const DB_PASS = 'YOUR_DATABASE_PASSWORD';

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}
