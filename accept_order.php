<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$order_id = $data['order_id'] ?? null;
$delivery_boy_id = $data['delivery_boy_id'] ?? null;

if (!$order_id || !$delivery_boy_id) {
    echo json_encode(["success" => false, "message" => "Order ID and Delivery Boy ID are required."]);
    exit();
}

// Pehle check karein ke ye order kisi aur ne accept to nahi kar liya (race condition se bachne ke liye)
$checkStmt = $conn->prepare("SELECT delivery_status FROM orders WHERE id = ?");
$checkStmt->bind_param("i", $order_id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$row = $result->fetch_assoc();

if ($row['delivery_status'] !== 'pending') {
    echo json_encode(["success" => false, "message" => "This order has already been accepted by another delivery boy."]);
    exit();
}

$stmt = $conn->prepare("UPDATE orders SET delivery_status = 'accepted', delivery_boy_id = ? WHERE id = ?");
$stmt->bind_param("ii", $delivery_boy_id, $order_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Order accepted."]);
} else {
    echo json_encode(["success" => false, "message" => "Could not be accepted"]);
}

$stmt->close();
$checkStmt->close();
$conn->close();
?>