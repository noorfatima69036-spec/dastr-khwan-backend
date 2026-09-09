<?php
// add_membership.php
require_once '../config/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$office_name = trim($data['office_name'] ?? '');
$office_address = trim($data['office_address'] ?? '');
$plan_type = $data['plan_type'] ?? '';
$delivery_time = $data['delivery_time'] ?? '';
$meal_preference = trim($data['meal_preference'] ?? '');
$contact_number = trim($data['contact_number'] ?? '');

if (!$office_name || !$office_address || !$plan_type || !$delivery_time || !$contact_number) {
    echo json_encode(["success" => false, "message" => "Please fill all required fields"]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO memberships (office_name, office_address, plan_type, delivery_time, meal_preference, contact_number) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $office_name, $office_address, $plan_type, $delivery_time, $meal_preference, $contact_number);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Your membership request has been submitted! We will contact you soon."]);
} else {
    echo json_encode(["success" => false, "message" => "Something went wrong, please try again"]);
}

$stmt->close();
$conn->close();
?>
