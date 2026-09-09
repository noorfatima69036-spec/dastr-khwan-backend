<?php
// db_connect.php
// Yeh file database se connection banati hai — har API ise use karegi

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Browser pehle OPTIONS request bhejta hai (preflight) — usay turant OK bol dena hai
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$host = "localhost";
$dbname = "dastr_khwan";
$username = "root";   // XAMPP ka default username
$password = "";        // XAMPP ka default password (khali hota hai)

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}
?>
