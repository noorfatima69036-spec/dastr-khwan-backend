<?php
// send_otp.php
// Note: Yeh OTP database mein save karta hai, lekin ASAL email nahi bhejta
// (real email ke liye PHPMailer + Gmail SMTP chahiye hota, jo FYP demo ke liye
// optional hai — abhi ke liye hum OTP screen par bhi dikha denge taake test ho sake)

require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');

if (!$email) {
    echo json_encode(["success" => false, "message" => "Please enter your email"]);
    exit();
}

// Check user exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "No account found with this email"]);
    exit();
}

// 6-digit random OTP generate karna
$otp = rand(100000, 999999);
$expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

$stmt = $conn->prepare("INSERT INTO otp_codes (email, otp_code, expires_at) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $otp, $expires_at);
$stmt->execute();

// Demo ke liye OTP response mein bhi bhej rahe hain (real app mein sirf email par jata)
echo json_encode([
    "success" => true,
    "message" => "OTP sent successfully",
    "demo_otp" => $otp  // ⚠️ Sirf testing ke liye — production mein yeh line hata dein
]);

$stmt->close();
$conn->close();
?>
