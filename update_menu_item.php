<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$category = trim($data['category'] ?? '');
$name = trim($data['name'] ?? '');
$description = trim($data['description'] ?? '');
$price = $data['price'] ?? null;
$image_name = trim($data['image_name'] ?? '');

if (!$id || !$category || !$name || !$price) {
    echo json_encode(["success" => false, "message" => "Zaroori fields missing hain"]);
    exit();
}

$stmt = $conn->prepare("UPDATE menu_items SET category=?, name=?, description=?, price=?, image_name=? WHERE id=?");
$stmt->bind_param("sssdsi", $category, $name, $description, $price, $image_name, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Item successfully update ho gaya"]);
} else {
    echo json_encode(["success" => false, "message" => "Item update nahi ho saka"]);
}

$stmt->close();
$conn->close();
?>