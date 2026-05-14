<?php
$DB_HOST = '127.0.0.1:3307';
$DB_USER = 'root';
$DB_PASS = '';  
$DB_NAME = 'internshipfinder';  // or internship_finder (use your actual DB)

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
