<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$category = trim($data['category'] ?? '');
$name = trim($data['name'] ?? '');
$description = trim($data['description'] ?? '');
$price = $data['price'] ?? null;
$image_name = trim($data['image_name'] ?? '');

if (!$category || !$name || !$price) {
    echo json_encode(["success" => false, "message" => "Category, name aur price zaroori hain"]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO menu_items (category, name, description, price, image_name) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssds", $category, $name, $description, $price, $image_name);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Item successfully add ho gaya",
        "id" => $stmt->insert_id
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Item add nahi ho saka"]);
}

$stmt->close();
$conn->close();
?>