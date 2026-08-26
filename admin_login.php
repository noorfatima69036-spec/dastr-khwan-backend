<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(["success" => false, "message" => "Please enter email and password"]);
    exit();
}

$stmt = $conn->prepare("SELECT id, username, email, password FROM admins WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "No admin account found with this email"]);
    exit();
}

$admin = $result->fetch_assoc();

if (!password_verify($password, $admin['password'])) {
    echo json_encode(["success" => false, "message" => "Incorrect password"]);
    exit();
}

echo json_encode([
    "success" => true,
    "message" => "Login successful",
    "admin" => [
        "id" => $admin['id'],
        "username" => $admin['username'],
        "email" => $admin['email']
    ]
]);

$stmt->close();
$conn->close();
?>