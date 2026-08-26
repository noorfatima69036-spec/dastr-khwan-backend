<?php
// add_bulk_order.php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$event_type = trim($data['event_type'] ?? '');
$guest_count = $data['guest_count'] ?? 0;
$event_date = $data['event_date'] ?? '';
$event_time = $data['event_time'] ?? '';
$menu_requirements = trim($data['menu_requirements'] ?? '');
$contact_number = trim($data['contact_number'] ?? '');

if (!$event_type || !$guest_count || !$event_date || !$event_time || !$contact_number) {
    echo json_encode(["success" => false, "message" => "Please fill all required fields"]);
    exit();
}

// Server-side bhi check karte hain ke event 1 din advance hai ya nahi
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

$stmt = $conn->prepare("INSERT INTO bulk_orders (event_type, guest_count, event_date, event_time, menu_requirements, contact_number) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sissss", $event_type, $guest_count, $event_date, $event_time, $menu_requirements, $contact_number);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Your bulk order request has been sent! We will confirm shortly."]);
} else {
    echo json_encode(["success" => false, "message" => "Something went wrong, please try again"]);
}

$stmt->close();
$conn->close();
?>
