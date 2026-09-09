<?php
// Calculates time-based delivery statistics for a specific delivery boy
require_once '../config/db_connect.php';

$delivery_boy_id = $_GET['delivery_boy_id'] ?? null;

if (!$delivery_boy_id) {
    echo json_encode(["success" => false, "message" => "Delivery Boy ID is required"]);
    exit();
}

$stats = [];

// Helper function to count delivered orders within a date range
function countDelivered($conn, $delivery_boy_id, $startDate, $endDate) {
    $stmt = $conn->prepare("SELECT COUNT(*) as c FROM orders WHERE delivery_boy_id = ? AND delivery_status = 'delivered' AND delivered_at BETWEEN ? AND ?");
    $stmt->bind_param("iss", $delivery_boy_id, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (int)$result['c'];
}

// Today
$stats['today'] = countDelivered($conn, $delivery_boy_id, date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59'));

// Yesterday
$yesterday = date('Y-m-d', strtotime('-1 day'));
$stats['yesterday'] = countDelivered($conn, $delivery_boy_id, $yesterday . ' 00:00:00', $yesterday . ' 23:59:59');

// This week (Monday to now)
$startOfWeek = date('Y-m-d 00:00:00', strtotime('monday this week'));
$stats['this_week'] = countDelivered($conn, $delivery_boy_id, $startOfWeek, date('Y-m-d 23:59:59'));

// Last week
$startOfLastWeek = date('Y-m-d 00:00:00', strtotime('monday last week'));
$endOfLastWeek = date('Y-m-d 23:59:59', strtotime('sunday last week'));
$stats['last_week'] = countDelivered($conn, $delivery_boy_id, $startOfLastWeek, $endOfLastWeek);

// This month
$startOfMonth = date('Y-m-01 00:00:00');
$stats['this_month'] = countDelivered($conn, $delivery_boy_id, $startOfMonth, date('Y-m-d 23:59:59'));

// Last month
$startOfLastMonth = date('Y-m-01 00:00:00', strtotime('first day of last month'));
$endOfLastMonth = date('Y-m-t 23:59:59', strtotime('last day of last month'));
$stats['last_month'] = countDelivered($conn, $delivery_boy_id, $startOfLastMonth, $endOfLastMonth);

// Total accepted (currently assigned to this boy, regardless of delivered or not)
$acceptedStmt = $conn->prepare("SELECT COUNT(*) as c FROM orders WHERE delivery_boy_id = ?");
$acceptedStmt->bind_param("i", $delivery_boy_id);
$acceptedStmt->execute();
$stats['total_accepted'] = (int)$acceptedStmt->get_result()->fetch_assoc()['c'];
$acceptedStmt->close();

// Total delivered (all-time)
$deliveredStmt = $conn->prepare("SELECT COUNT(*) as c FROM orders WHERE delivery_boy_id = ? AND delivery_status = 'delivered'");
$deliveredStmt->bind_param("i", $delivery_boy_id);
$deliveredStmt->execute();
$stats['total_delivered'] = (int)$deliveredStmt->get_result()->fetch_assoc()['c'];
$deliveredStmt->close();

echo json_encode(["success" => true, "stats" => $stats]);
$conn->close();
?>