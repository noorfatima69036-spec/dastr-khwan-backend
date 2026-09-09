<?php
require_once '../config/db_connect.php';

$sql = "SELECT o.id, o.full_name, o.phone, o.address, o.total_amount, o.status,
               o.payment_method, o.payment_status, o.delivery_status, o.delivery_boy_id,
               db.name AS delivery_boy_name
        FROM orders o
        LEFT JOIN delivery_boys db ON o.delivery_boy_id = db.id
        WHERE o.status = 'ready'
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);

$orders = [];
while ($row = $result->fetch_assoc()) {
    $itemsStmt = $conn->prepare("SELECT item_name, quantity FROM order_items WHERE order_id = ?");
    $itemsStmt->bind_param("i", $row['id']);
    $itemsStmt->execute();
    $itemsResult = $itemsStmt->get_result();
    $items = [];
    while ($item = $itemsResult->fetch_assoc()) {
        $items[] = $item;
    }
    $row['items'] = $items;
    $orders[] = $row;
}

echo json_encode(["success" => true, "orders" => $orders]);
$conn->close();
?>