<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

require_once '../config/db_connect.php';

if (!isset($conn) && isset($db)) {
    $conn = $db;
}

$stats = [];

// Total Orders
$res = $conn->query("SELECT COUNT(*) as c FROM orders");
$stats['total_orders'] = $res ? $res->fetch_assoc()['c'] : 0;

// Total Deliveries
$res = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='delivered'");
$stats['total_deliveries'] = $res ? $res->fetch_assoc()['c'] : 0;

// Bulk Orders
$res = $conn->query("SELECT COUNT(*) as c FROM bulk_orders");
$stats['bulk_orders'] = $res ? $res->fetch_assoc()['c'] : 0;

// Membership Offers
$res = $conn->query("SELECT COUNT(*) as c FROM memberships");
$stats['membership_offers'] = $res ? $res->fetch_assoc()['c'] : 0;

// Delivery Partners
$res = $conn->query("SELECT COUNT(*) as c FROM delivery_partners");
$stats['delivery_partners'] = $res ? $res->fetch_assoc()['c'] : 0;

// Contact Messages Count
$res = $conn->query("SELECT COUNT(*) as c FROM contact_messages");
$stats['total_messages'] = $res ? $res->fetch_assoc()['c'] : 0;
$stats['contact_messages'] = $stats['total_messages']; // Fallback key

// Total Revenue
$res = $conn->query("SELECT SUM(total_amount) as total FROM orders");
$row = $res ? $res->fetch_assoc() : null;
$stats['total_revenue'] = $row && $row['total'] ? $row['total'] : 0;

echo json_encode(["success" => true, "data" => $stats]);
?>