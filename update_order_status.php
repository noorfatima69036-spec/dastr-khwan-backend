<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$order_id = $data['order_id'] ?? null;
$status = $data['status'] ?? '';

$allowedStatuses = ['pending', 'preparing', 'cooking', 'packing', 'ready', 'delivered', 'cancelled'];

if (!$order_id || !in_array($status, $allowedStatuses)) {
    echo json_encode(["success" => false, "message" => "Invalid order ID or status"]);
    exit();
}

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Order status updated to: " . $status]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update status"]);
}

$stmt->close();
$conn->close();
?>