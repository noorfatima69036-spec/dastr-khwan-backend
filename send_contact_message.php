<?php
// send_contact_message.php
// Customer ke Contact Us form se aaya message database mein save karta hai

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once 'config/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$message = trim($data['message'] ?? '');

if (!$name || !$email || !$message) {
    echo json_encode(["success" => false, "message" => "Name, email, and message are all required."]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $message);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Your message has been sent. We will contact you soon!"]);
} else {
    echo json_encode(["success" => false, "message" => "There was a problem sending the message, please try again."]);
}

$stmt->close();
$conn->close();
?>