<?php
// test_connection.php
// Yeh file sirf TEST karne ke liye hai — confirm karti hai database connect ho raha hai ya nahi

require_once '../config/db_connect.php';

echo json_encode(["success" => true, "message" => "Database connection is working! 🎉"]);

$conn->close();
?>