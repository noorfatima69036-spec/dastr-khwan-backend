<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/db_connect.php';

$stats = [];

$stats['total_orders'] = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];

$stats['total_bulk_orders'] = $conn->query("SELECT COUNT(*) as c FROM bulk_orders")->fetch_assoc()['c'];

$stats['total_memberships'] = $conn->query("SELECT COUNT(*) as c FROM memberships")->fetch_assoc()['c'];

$stats['total_delivered'] = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status = 'delivered'")->fetch_assoc()['c'];

$stats['total_delivery_boys'] = $conn->query("SELECT COUNT(*) as c FROM delivery_boys")->fetch_assoc()['c'];

$stats['total_revenue'] = $conn->query("SELECT SUM(total_amount) as s FROM orders WHERE status != 'cancelled'")->fetch_assoc()['s'] ?? 0;

echo json_encode(["success" => true, "stats" => $stats]);
$conn->close();
?>