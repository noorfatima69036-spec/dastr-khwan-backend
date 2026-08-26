<?php
require_once 'db_connect.php';

$conn->query("UPDATE waste_alerts SET is_active = 0");
echo json_encode(["success" => true, "message" => "Waste alert deactivate ho gaya"]);

$conn->close();
?>