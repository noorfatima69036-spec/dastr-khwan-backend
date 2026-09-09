<?php
require_once '../config/db_connect.php';

$sql = "SELECT id, day_name, main_item, extra_item FROM weekly_menu ORDER BY 
    FIELD(day_name, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
$result = $conn->query($sql);

$menu = [];
while ($row = $result->fetch_assoc()) {
    $menu[] = $row;
}

echo json_encode(["success" => true, "weekly_menu" => $menu]);

$conn->close();
?>