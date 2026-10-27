<?php
declare(strict_types=1);

// Replace these placeholders with the exact credentials from your hosting control panel.
const DB_HOST = 'sql209.infinityfree.com';
const DB_NAME = 'if0_43284159_sis_database';
const DB_USER = 'if0_43284159';
const DB_PASS = 'qCMed3Z4pLV';

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
