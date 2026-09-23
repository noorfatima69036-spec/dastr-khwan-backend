<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

require_once '../config/db_connect.php';

if (!isset($conn) && isset($db)) {
    $conn = $db;
}

$type = isset($_GET['type']) ? $_GET['type'] : '';
$data = [];

switch ($type) {
    case 'contact_messages':
        $result = $conn->query("SELECT id, name, email, message, created_at FROM contact_messages ORDER BY created_at DESC");
        break;

    case 'orders':
    case 'total_orders':
        $result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 20");
        break;

    case 'deliveries':
    case 'total_deliveries':
        $result = $conn->query("SELECT * FROM orders WHERE status='delivered' ORDER BY created_at DESC LIMIT 20");
        break;

    case 'bulk_orders':
        $result = $conn->query("SELECT * FROM bulk_orders ORDER BY created_at DESC LIMIT 20");
        break;

    default:
        $result = false;
        break;
}

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(["success" => true, "data" => $data]);
} else {
    echo json_encode(["success" => true, "data" => []]);
}
?>