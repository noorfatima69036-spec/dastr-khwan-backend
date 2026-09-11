<?php
require_once '../config/db_connect.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$order_id = $data['order_id'] ?? null;
$user_id = $data['user_id'] ?? null;
$rating = $data['rating'] ?? null;
$comment = trim($data['comment'] ?? '');

if (!$order_id || !$user_id || !$rating) {
    echo json_encode(["success" => false, "message" => "Order ID, User ID, and rating are required."]);
    exit();
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(["success" => false, "message" => "Rating must be between 1 and 5."]);
    exit();
}

// Check karo pehle se review to nahi de chuka is order pe
$check = $conn->prepare("SELECT id FROM reviews WHERE order_id = ?");
$check->bind_param("i", $order_id);
$check->execute();
$existing = $check->get_result();

if ($existing->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "You have already reviewed this order."]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO reviews (order_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $order_id, $user_id, $rating, $comment);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Review submitted successfully, thank you!"]);
} else {
    echo json_encode(["success" => false, "message" => "Review could not be submitted."]);
}

$stmt->close();
$conn->close();
?>