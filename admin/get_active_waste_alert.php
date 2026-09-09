<?php
require_once '../config/db_connect.php';

$sql = "SELECT message, phone FROM waste_alerts WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["success" => true, "active" => true, "message" => $row['message'], "phone" => $row['phone']]);
} else {
    echo json_encode(["success" => true, "active" => false]);
}

$conn->close();
?>