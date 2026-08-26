<?php
// reset_password.php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$otp = trim($data['otp'] ?? '');
$new_password = $data['new_password'] ?? '';

if (!$email || !$otp || !$new_password) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit();
}

// Sabse recent OTP nikalna is email ke liye
$stmt = $conn->prepare("SELECT id, otp_code, expires_at FROM otp_codes WHERE email = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "No OTP found, please request a new one"]);
    exit();
}

$row = $result->fetch_assoc();

if ($row['otp_code'] !== $otp) {
    echo json_encode(["success" => false, "message" => "Incorrect OTP"]);
    exit();
}

if (strtotime($row['expires_at']) < time()) {
    echo json_encode(["success" => false, "message" => "OTP has expired, please request a new one"]);
    exit();
}

// OTP sahi hai — password update karo
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$update->bind_param("ss", $hashed_password, $email);

if ($update->execute()) {
    echo json_encode(["success" => true, "message" => "Password reset successfully! You can now login."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to reset password"]);
}

$stmt->close();
$update->close();
$conn->close();
?>
