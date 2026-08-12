<?php

declare(strict_types=1);

/**
 * Database configuration.
 * Change these values to match your local MySQL setup.
 */
const DB_HOST = 'localhost';
const DB_USER = 'bluebird_user';
const DB_PASS = 'password';
const DB_NAME = 'bluebirdhotel';

/**
 * Returns a shared mysqli connection (created once per request).
 */
function db(): mysqli
{
    static $conn = null;

    if ($conn === null) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $conn->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e) {
            http_response_code(500);
            exit('Database connection failed. Please check config.php settings.');
        }
    }

    return $conn;
}

// The connection is created lazily on first use. No code relies on a
// global $conn handle anymore, so nothing is assigned here.