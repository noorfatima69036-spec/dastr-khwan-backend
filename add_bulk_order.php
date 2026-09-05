<?php
// add_bulk_order.php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$event_type = trim($data['event_type'] ?? '');
$guest_count = $data['guest_count'] ?? 0;
$event_date = $data['event_date'] ?? '';
$event_time = $data['event_time'] ?? '';
$address = trim($data['address'] ?? '');
$menu_requirements = trim($data['menu_requirements'] ?? '');
$contact_number = trim($data['contact_number'] ?? '');
$items = $data['items'] ?? [];
$total_amount = $data['total_amount'] ?? 0;

if (!$event_type || !$guest_count || !$event_date || !$event_time || !$contact_number || !$address) {
    echo json_encode(["success" => false, "message" => "Please fill all required fields"]);
    exit();
}

if (count($items) === 0) {
    echo json_encode(["success" => false, "message" => "Please select at least one menu item"]);
    exit();
}

$today = new DateTime();
$today->setTime(0, 0, 0);
$eventDate = new DateTime($event_date);
$eventDate->setTime(0, 0, 0);
$diff = $today->diff($eventDate)->days;
$isPast = $eventDate < $today;

if ($isPast || $diff < 1) {
    echo json_encode(["success" => false, "message" => "Sorry, we can't take urgent basis orders. Please book at least 1 day in advance."]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO bulk_orders (event_type, guest_count, event_date, event_time, address, menu_requirements, contact_number, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sisssssd", $event_type, $guest_count, $event_date, $event_time, $address, $menu_requirements, $contact_number, $total_amount);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Something went wrong, please try again"]);
    exit();
}

$bulk_order_id = $stmt->insert_id;

$itemStmt = $conn->prepare("INSERT INTO bulk_order_items (bulk_order_id, item_name, category, quantity, price) VALUES (?, ?, ?, ?, ?)");
foreach ($items as $item) {
    $itemName = $item['name'];
    $category = $item['category'];
    $quantity = $item['quantity'];
    $price = $item['price'];
    $itemStmt->bind_param("issid", $bulk_order_id, $itemName, $category, $quantity, $price);
    $itemStmt->execute();
}

echo json_encode(["success" => true, "message" => "Your bulk order request has been sent! We will confirm shortly."]);

$stmt->close();
$itemStmt->close();
$conn->close();
?>