<?php
// Fetches all bulk order requests along with their selected items, for the admin panel
require_once '../config/db_connect.php';

$sql = "SELECT b.id, b.event_type, b.guest_count, b.event_date, b.event_time, 
               b.address, b.menu_requirements, b.contact_number, b.status, 
               b.total_amount, b.created_at
        FROM bulk_orders b
        ORDER BY b.created_at DESC";
$result = $conn->query($sql);

$orders = [];
while ($row = $result->fetch_assoc()) {
    // Fetch the individual dishes selected for this bulk order
    $itemsStmt = $conn->prepare("SELECT item_name, category, quantity, price FROM bulk_order_items WHERE bulk_order_id = ?");
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

echo json_encode(["success" => true, "bulk_orders" => $orders]);
$conn->close();
?>