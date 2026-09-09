<?php
require_once '../config/db_connect.php';

$sql = "SELECT id, name, phone, email FROM delivery_boys ORDER BY name";
$result = $conn->query($sql);

$boys = [];
while ($row = $result->fetch_assoc()) {
    $boys[] = $row;
}

echo json_encode(["success" => true, "delivery_boys" => $boys]);
$conn->close();
?>