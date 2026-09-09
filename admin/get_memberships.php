<?php
// Fetches all office membership requests for the admin panel
require_once '../config/db_connect.php';

$sql = "SELECT id, office_name, office_address, delivery_time, meal_preference, 
               contact_number, plan_type, created_at
        FROM memberships
        ORDER BY created_at DESC";
$result = $conn->query($sql);

$memberships = [];
while ($row = $result->fetch_assoc()) {
    $memberships[] = $row;
}

echo json_encode(["success" => true, "memberships" => $memberships]);
$conn->close();
?>