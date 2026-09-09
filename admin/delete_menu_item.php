<?php
require_once '../config/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(["success" => false, "message" => "Item ID zaroori hai"]);
    exit();
}

$stmt = $conn->prepare("DELETE FROM menu_items WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Item delete ho gaya"]);
} else {
    echo json_encode(["success" => false, "message" => "Item delete nahi ho saka"]);
}

$stmt->close();
$conn->close();
?>