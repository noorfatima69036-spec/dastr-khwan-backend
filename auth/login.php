<?php
// login.php
require_once '../config/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(["success" => false, "message" => "Please enter email and password"]);
    exit();
}

$stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "No account found with this email"]);
    exit();
}

$user = $result->fetch_assoc();

// Hashed password ko verify karna
if (!password_verify($password, $user['password'])) {
    echo json_encode(["success" => false, "message" => "Incorrect password"]);
    exit();
}

echo json_encode([
    "success" => true,
    "message" => "Login successful",
    "user" => [
        "id" => $user['id'],
        "username" => $user['username'],
        "email" => $user['email']
    ]
]);

$stmt->close();
$conn->close();
?>