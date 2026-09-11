<?php
require_once '../config/db_connect.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$type = $_GET['type'] ?? null;

if (!$type) {
    echo json_encode(["success" => false, "message" => "Type is required"]);
    exit();
}

switch ($type) {
    case 'total_orders':
        $result = $conn->query("SELECT id, full_name, phone, address, total_amount, status, payment_method, payment_status, delivery_status, created_at FROM orders ORDER BY created_at DESC");
        break;

    case 'total_deliveries':
        $result = $conn->query("SELECT id, full_name, phone, address, total_amount, delivery_status, delivered_at FROM orders WHERE delivery_status = 'delivered' ORDER BY delivered_at DESC");
        break;

    case 'bulk_orders':
        $result = $conn->query("SELECT id, full_name, phone, total_amount, created_at, status FROM bulk_orders ORDER BY created_at DESC");
        break;

    case 'memberships':
        $result = $conn->query("SELECT id, full_name, phone, plan_type, created_at FROM memberships ORDER BY created_at DESC");
        break;

    case 'delivery_boys':
        $result = $conn->query("SELECT id, name, phone, email FROM delivery_boys ORDER BY id DESC");
        break;

    default:
        echo json_encode(["success" => false, "message" => "Invalid type"]);
        exit();
}

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode(["success" => true, "data" => $rows]);
$conn->close();
?>