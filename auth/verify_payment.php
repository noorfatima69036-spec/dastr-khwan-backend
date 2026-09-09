<?php
require_once '../config/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$order_id = $data['order_id'] ?? null;

if (!$order_id) {
    echo json_encode(["success" => false, "message" => "Payment verified successfully."]);
    exit();
}

$stmt = $conn->prepare("UPDATE orders SET payment_status = 'verified' WHERE id = ?");
$stmt->bind_param("i", $order_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Payment verified successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Verification failed."]);
}

$stmt->close();
$conn->close();
?>