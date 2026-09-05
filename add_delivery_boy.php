<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$name = trim($data['name'] ?? '');
$phone = trim($data['phone'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if (!$name || !$phone || !$email || !$password) {
    echo json_encode(["success" => false, "message" => "Sab fields zaroori hain"]);
    exit();
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO delivery_boys (name, phone, email, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $phone, $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Delivery boy add ho gaya"]);
} else {
    echo json_encode(["success" => false, "message" => "Ye email pehle se registered hai"]);
}

$stmt->close();
$conn->close();
?>