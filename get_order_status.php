<?php
require_once 'db_connect.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo json_encode(["success" => false, "message" => "Order ID is mandatory."]);
    exit();
}

$stmt = $conn->prepare("SELECT status, payment_method, payment_status FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Order not received."]);
    exit();
}

$row = $result->fetch_assoc();
echo json_encode([
    "success" => true, 
    "status" => $row['status'],
    "payment_method" => $row['payment_method'],
    "payment_status" => $row['payment_status']
]);

$stmt->close();
$conn->close();
?>