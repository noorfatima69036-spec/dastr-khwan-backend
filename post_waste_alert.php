<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$message = trim($data['message'] ?? '');
$phone = trim($data['phone'] ?? '');

if (!$message || !$phone) {
    echo json_encode(["success" => false, "message" => "Message aur phone number zaroori hain"]);
    exit();
}

// Pehle sab purane alerts deactivate kar dein
$conn->query("UPDATE waste_alerts SET is_active = 0");

// Naya alert add karke activate karein
$stmt = $conn->prepare("INSERT INTO waste_alerts (message, phone, is_active) VALUES (?, ?, 1)");
$stmt->bind_param("ss", $message, $phone);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Waste alert activate ho gaya"]);
} else {
    echo json_encode(["success" => false, "message" => "Alert post nahi ho saka"]);
}

$stmt->close();
$conn->close();
?>