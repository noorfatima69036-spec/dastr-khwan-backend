<?php
// Marks an order as delivered and records the exact delivery timestamp
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$order_id = $data['order_id'] ?? null;
$delivery_boy_id = $data['delivery_boy_id'] ?? null;

if (!$order_id || !$delivery_boy_id) {
    echo json_encode(["success" => false, "message" => "Order ID and Delivery Boy ID are mandatory"]);
    exit();
}

$stmt = $conn->prepare("UPDATE orders SET status = 'delivered', delivery_status = 'delivered', delivered_at = NOW() WHERE id = ? AND delivery_boy_id = ?");
$stmt->bind_param("ii", $order_id, $delivery_boy_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "The order has been marked as delivered"]);
} else {
    echo json_encode(["success" => false, "message" => "Could not be updated"]);
}

$stmt->close();
$conn->close();
?>