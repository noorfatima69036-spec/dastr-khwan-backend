<?php
// place_order.php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['user_id'] ?? null;
$order_type = $data['order_type'] ?? 'delivery';
$full_name = trim($data['full_name'] ?? '');
$phone = trim($data['phone'] ?? '');
$address = trim($data['address'] ?? '');
$special_instructions = trim($data['special_instructions'] ?? '');
$total_amount = $data['total_amount'] ?? 0;
$items = $data['items'] ?? [];
$payment_method = $data['payment_method'] ?? 'cod';
$transaction_id = trim($data['transaction_id'] ?? '');

if (!$user_id || !$full_name || !$phone || count($items) === 0) {
    echo json_encode(["success" => false, "message" => "Missing required order details"]);
    exit();
}

// Agar online payment hai to transaction ID zaroori hai
if ($payment_method === 'online' && !$transaction_id) {
    echo json_encode(["success" => false, "message" => "Transaction ID is mandatory for online payments."]);
    exit();
}

// Payment status decide karna
$payment_status = ($payment_method === 'online') ? 'pending_verification' : 'not_applicable';

// 1. Order ko 'orders' table mein save karna
$stmt = $conn->prepare("INSERT INTO orders (user_id, order_type, full_name, phone, address, special_instructions, total_amount, payment_method, transaction_id, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssssdsss", $user_id, $order_type, $full_name, $phone, $address, $special_instructions, $total_amount, $payment_method, $transaction_id, $payment_status);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Failed to place order"]);
    exit();
}

$order_id = $stmt->insert_id;

// 2. Har item ko 'order_items' table mein save karna (portion_type - full/half - bhi save ho raha hai)
$itemStmt = $conn->prepare("INSERT INTO order_items (order_id, item_name, quantity, price, portion_type) VALUES (?, ?, ?, ?, ?)");

foreach ($items as $item) {
    $itemName = $item['name'];
    $quantity = $item['quantity'];
    $price = $item['price'];
    $portionType = $item['portion_type'] ?? 'full'; // agar na bheja gaya ho to default 'full'
    $itemStmt->bind_param("isids", $order_id, $itemName, $quantity, $price, $portionType);
    $itemStmt->execute();
}

echo json_encode([
    "success" => true,
    "message" => "Order placed successfully!",
    "order_id" => $order_id
]);

$stmt->close();
$itemStmt->close();
$conn->close();
?>
