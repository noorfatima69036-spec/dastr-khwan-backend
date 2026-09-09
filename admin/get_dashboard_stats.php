<?php
// Fetches summary statistics for the admin dashboard overview page
require_once '../config/db_connect.php';

$stats = [];

// Count total orders placed
$stats['total_orders'] = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];

// Count total bulk order requests
$stats['total_bulk_orders'] = $conn->query("SELECT COUNT(*) as c FROM bulk_orders")->fetch_assoc()['c'];

// Count total membership requests
$stats['total_memberships'] = $conn->query("SELECT COUNT(*) as c FROM memberships")->fetch_assoc()['c'];

// Count orders that have been delivered
$stats['total_delivered'] = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status = 'delivered'")->fetch_assoc()['c'];

// Count registered delivery boys
$stats['total_delivery_boys'] = $conn->query("SELECT COUNT(*) as c FROM delivery_boys")->fetch_assoc()['c'];

// Sum total revenue from non-cancelled orders
$stats['total_revenue'] = $conn->query("SELECT SUM(total_amount) as s FROM orders WHERE status != 'cancelled'")->fetch_assoc()['s'] ?? 0;

echo json_encode(["success" => true, "stats" => $stats]);
$conn->close();
?>