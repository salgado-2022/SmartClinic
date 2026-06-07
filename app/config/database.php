<?php
/**
 * Database Configuration
 * 
 * Reads connection parameters from environment variables
 * and returns a PDO connection instance.
 */

function getDBConnection(): PDO
{
    $host = getenv('DB_HOST') ?: 'db';
    $port = getenv('DB_PORT') ?: '3306';
    $dbname = getenv('DB_NAME') ?: 'smartclinic';
    $user = getenv('DB_USER') ?: 'smartclinic_user';
    $password = getenv('DB_PASSWORD') ?: 'smartclinic_pass';

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    return new PDO($dsn, $user, $password, $options);
}
