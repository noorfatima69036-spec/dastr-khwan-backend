<?php
require_once '../config/db_connect.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$sql = "SELECT r.id, r.rating, r.comment, r.created_at, 
               o.id AS order_id, u.username AS customer_name
        FROM reviews r
        JOIN orders o ON r.order_id = o.id
        JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC";

$result = $conn->query($sql);

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode(["success" => true, "reviews" => $reviews]);
$conn->close();
?>