<?php
require_once '../config/db_connect.php';
 
$data = json_decode(file_get_contents("php://input"), true);
 
$category = trim($data['category'] ?? '');
$name = trim($data['name'] ?? '');
$description = trim($data['description'] ?? '');
$price_full = $data['price_full'] ?? null;
$price_half = $data['price_half'] ?? null;       // optional - normal menu ke liye
$price_per_box = $data['price_per_box'] ?? null; // optional - bulk order ke liye
$price_per_kg = $data['price_per_kg'] ?? null;   // optional - bulk order ke liye
$image_name = trim($data['image_name'] ?? '');
 
if (!$category || !$name || !$price_full) {
    echo json_encode(["success" => false, "message" => "Category, name aur Full price zaroori hain"]);
    exit();
}
 
// Empty string ya 0 aayein to unhe NULL treat karo (matlab ye option is dish pe applicable nahi)
foreach (['price_half', 'price_per_box', 'price_per_kg'] as $field) {
    if ($$field === '' || $$field === 0 || $$field === '0') {
        $$field = null;
    }
}
 
$stmt = $conn->prepare("INSERT INTO menu_items (category, name, description, price_full, price_half, price_per_box, price_per_kg, image_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssdddds", $category, $name, $description, $price_full, $price_half, $price_per_box, $price_per_kg, $image_name);
 
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