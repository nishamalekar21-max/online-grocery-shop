<?php

// Load eSewa merchant credentials
require_once __DIR__ . "/credentials.php";

$host = "localhost";
$username = "root";
$password = "";
$database = "grocery_shop";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>