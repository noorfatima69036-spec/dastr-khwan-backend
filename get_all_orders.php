<?php
require_once 'db_connect.php';

$sql = "SELECT o.id, o.user_id, o.order_type, o.full_name, o.phone, o.address, 
               o.special_instructions, o.total_amount, o.status, o.created_at,
               o.payment_method, o.transaction_id, o.payment_status
        FROM orders o
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
$orders = [];
while ($row = $result->fetch_assoc()) {
    $itemsStmt = $conn->prepare("SELECT item_name, quantity, price FROM order_items WHERE order_id = ?");
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