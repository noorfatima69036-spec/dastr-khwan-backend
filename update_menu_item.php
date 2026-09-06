<?php
require_once 'db_connect.php';
 
$data = json_decode(file_get_contents("php://input"), true);
 
$id = $data['id'] ?? null;
$category = trim($data['category'] ?? '');
$name = trim($data['name'] ?? '');
$description = trim($data['description'] ?? '');
$price_full = $data['price_full'] ?? null;
$price_half = $data['price_half'] ?? null;
$price_per_box = $data['price_per_box'] ?? null;
$price_per_kg = $data['price_per_kg'] ?? null;
$image_name = trim($data['image_name'] ?? '');
 
if (!$id || !$category || !$name || !$price_full) {
    echo json_encode(["success" => false, "message" => "Zaroori fields missing hain"]);
    exit();
}
 
foreach (['price_half', 'price_per_box', 'price_per_kg'] as $field) {
    if ($$field === '' || $$field === 0 || $$field === '0') {
        $$field = null;
    }
}
 
$stmt = $conn->prepare("UPDATE menu_items SET category=?, name=?, description=?, price_full=?, price_half=?, price_per_box=?, price_per_kg=?, image_name=? WHERE id=?");
$stmt->bind_param("sssddddsi", $category, $name, $description, $price_full, $price_half, $price_per_box, $price_per_kg, $image_name, $id);
 
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Item successfully update ho gaya"]);
} else {
    echo json_encode(["success" => false, "message" => "Item update nahi ho saka"]);
}
 
$stmt->close();
$conn->close();
?>