<?php
// signup.php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$username || !$email || !$password) {
    echo json_encode(["success" => false, "message" => "Please fill all fields"]);
    exit();
}

// Check if email already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "This email is already registered"]);
    exit();
}

// Password ko hash karna zaroori hai — plain text kabhi save nahi karte
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $hashed_password);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Account created successfully!"]);
} else {
    echo json_encode(["success" => false, "message" => "Signup failed, please try again"]);
}

$stmt->close();
$conn->close();
?>