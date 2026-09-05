<?php
// Fetches the list of delivered orders for a specific delivery boy within a given time period
require_once 'db_connect.php';

$delivery_boy_id = $_GET['delivery_boy_id'] ?? null;
$period = $_GET['period'] ?? null;

if (!$delivery_boy_id || !$period) {
    echo json_encode(["success" => false, "message" => "delivery_boy_id and period are required"]);
    exit();
}

// Determine the date range based on the requested period
switch ($period) {
    case 'today':
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        break;
    case 'yesterday':
        $y = date('Y-m-d', strtotime('-1 day'));
        $start = $y . ' 00:00:00';
        $end = $y . ' 23:59:59';
        break;
    case 'this_week':
        $start = date('Y-m-d 00:00:00', strtotime('monday this week'));
        $end = date('Y-m-d 23:59:59');
        break;
    case 'last_week':
        $start = date('Y-m-d 00:00:00', strtotime('monday last week'));
        $end = date('Y-m-d 23:59:59', strtotime('sunday last week'));
        break;
    case 'this_month':
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:59');
        break;
    case 'last_month':
        $start = date('Y-m-01 00:00:00', strtotime('first day of last month'));
        $end = date('Y-m-t 23:59:59', strtotime('last day of last month'));
        break;
    case 'total_accepted':
        // Special case: all orders ever assigned to this boy, regardless of date
        $stmt = $conn->prepare("SELECT id, full_name, phone, address, total_amount, status, payment_method, payment_status, delivery_status, delivered_at FROM orders WHERE delivery_boy_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $delivery_boy_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = [];
        while ($row = $result->fetch_assoc()) $orders[] = $row;
        echo json_encode(["success" => true, "orders" => $orders]);
        exit();
    case 'total_delivered':
        $stmt = $conn->prepare("SELECT id, full_name, phone, address, total_amount, status, payment_method, payment_status, delivery_status, delivered_at FROM orders WHERE delivery_boy_id = ? AND delivery_status = 'delivered' ORDER BY delivered_at DESC");
        $stmt->bind_param("i", $delivery_boy_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = [];
        while ($row = $result->fetch_assoc()) $orders[] = $row;
        echo json_encode(["success" => true, "orders" => $orders]);
        exit();
    default:
        echo json_encode(["success" => false, "message" => "Invalid period"]);
        exit();
}

$stmt = $conn->prepare("SELECT id, full_name, phone, address, total_amount, status, payment_method, payment_status, delivery_status, delivered_at FROM orders WHERE delivery_boy_id = ? AND delivery_status = 'delivered' AND delivered_at BETWEEN ? AND ? ORDER BY delivered_at DESC");
$stmt->bind_param("iss", $delivery_boy_id, $start, $end);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode(["success" => true, "orders" => $orders]);
$stmt->close();
$conn->close();
?>