<?php
require_once '../config/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$name = trim($data['name'] ?? '');
$phone = trim($data['phone'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if (!$name || !$phone || !$email || !$password) {
    echo json_encode(["success" => false, "message" => "All fields are mandatory"]);
    exit();
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO delivery_boys (name, phone, email, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $phone, $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "The delivery boy has been added."]);
} else {
    echo json_encode(["success" => false, "message" => "This email is already registered."]);
}

$stmt->close();
$conn->close();
?>