<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$main_item = trim($data['main_item'] ?? '');
$extra_item = trim($data['extra_item'] ?? '');

if (!$id || !$main_item || !$extra_item) {
    echo json_encode(["success" => false, "message" => "Main item and extra item are mandatory."]);
    exit();
}

$stmt = $conn->prepare("UPDATE weekly_menu SET main_item = ?, extra_item = ? WHERE id = ?");
$stmt->bind_param("ssi", $main_item, $extra_item, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Menu updated successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Could not update"]);
}

$stmt->close();
$conn->close();
?>