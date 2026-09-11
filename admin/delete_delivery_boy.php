<?php
// delete_delivery_boy.php
// Admin ek delivery boy ka record delete kar sakta hai (jab wo kaam chhod de)
 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
 
require_once '../config/db_connect.php';
 
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
 
if (!$id) {
    echo json_encode(["success" => false, "message" => "Delivery Boy ID is required."]);
    exit();
}
 
$stmt = $conn->prepare("DELETE FROM delivery_boys WHERE id = ?");
$stmt->bind_param("i", $id);
 
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "The delivery boy has been successfully deleted."]);
} else {
    echo json_encode(["success" => false, "message" => "Could not be deleted."]);
}
 
$stmt->close();
$conn->close();
?>