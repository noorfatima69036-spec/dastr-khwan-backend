<?php
require_once '../config/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(["success" => false, "message" => "Email and password are required."]);
    exit();
}

$stmt = $conn->prepare("SELECT id, name, email, password FROM delivery_boys WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Account not found"]);
    exit();
}

$boy = $result->fetch_assoc();

if (!password_verify($password, $boy['password'])) {
    echo json_encode(["success" => false, "message" => "Incorrect password"]);
    exit();
}

echo json_encode([
    "success" => true,
    "message" => "Login successful",
    "delivery_boy" => [
        "id" => $boy['id'],
        "name" => $boy['name'],
        "email" => $boy['email']
    ]
]);

$stmt->close();
$conn->close();
?>