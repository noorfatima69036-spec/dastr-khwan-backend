<?php
// get_menu_items.php
// Sab menu items database se laata hai, category ke hisaab se grouped
 
require_once 'db_connect.php';
 
$sql = "SELECT id, category, name, description, price_full, price_half, price_per_box, price_per_kg, image_name FROM menu_items WHERE is_available = 1 ORDER BY category, id";
$result = $conn->query($sql);
 
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
 
echo json_encode(["success" => true, "items" => $items]);
 
$conn->close();
?>