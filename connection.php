<?php

// Get database credentials from environment variables
$host = getenv('DB_HOST') ?: 'localhost';
$db = getenv('DB_NAME') ?: 'dlms';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';

// If running locally without environment variables, use defaults
if ($host === 'localhost' || $host === '') {
    $host = 'localhost:3306';
    $db = 'dlms';
    $user = 'root';
    $pass = '';
}

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

