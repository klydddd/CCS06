<?php
// ── Database connection ───────────────────────────────────────
// Default XAMPP credentials — change $pass if yours differs.
$host   = 'localhost';
$dbname = 'portfolio_db';
$user   = 'root';
$pass   = '';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    exit('Database connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
