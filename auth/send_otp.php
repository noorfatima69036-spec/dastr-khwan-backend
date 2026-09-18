<?php
// send_otp.php - Actual Email sending via PHPMailer + Gmail SMTP

// 1. CORS Headers & OPTIONS Preflight Handling (Must be at the very top)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

// 2. PHPMailer Imports & Manual Includes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Composer / Manual Files Path
if (file_exists('../vendor/autoload.php')) {
    require '../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/PHPMailer-master/src/Exception.php')) {
    require __DIR__ . '/PHPMailer-master/src/Exception.php';
    require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
    require __DIR__ . '/PHPMailer-master/src/SMTP.php';
} else {
    require __DIR__ . '/PHPMailer/src/Exception.php';
    require __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require __DIR__ . '/PHPMailer/src/SMTP.php';
}

require_once '../config/db_connect.php';

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

// Database me OTP save karna
$stmt = $conn->prepare("INSERT INTO otp_codes (email, otp_code, expires_at) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $otp, $expires_at);
$stmt->execute();

// --- PHPMailer ke zariye Email Bhejna ---
$mail = new PHPMailer(true);

try {
    // Server Settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'noorfatima69036@gmail.com';        // Aapka Gmail Address
    $mail->Password   = 'fhdl tzna ybsj mprs';             // 16-digit App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Sender & Receiver
    $mail->setFrom('noorfatima69036@gmail.com', 'Dastr-Khwan');
    $mail->addAddress($email);

    // Email Body Content
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset OTP - Dastr-Khwan';
    $mail->Body    = "
        <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
            <h2 style='color: #8B0000;'>Dastr-Khwan Password Reset</h2>
            <p>Your OTP for resetting your password is:</p>
            <h1 style='color: #333; letter-spacing: 4px;'>$otp</h1>
            <p>This code will expire in <b>10 minutes</b>.</p>
        </div>
    ";

    $mail->send();

    // Response jab email successfully chali jaye
    echo json_encode([
        "success" => true,
        "message" => "OTP has been sent to your email address."
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Email could not be sent. Error: {$mail->ErrorInfo}"
    ]);
}

$stmt->close();
$conn->close();
?>