<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$conn = new mysqli("localhost", "root", "", "user_auth");
if ($conn->connect_error) { echo json_encode(["error" => "DB connection failed"]); exit; }

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['name'], $data['description'], $data['address'], $data['avg_price'])) {
    echo json_encode(["error" => "Missing required fields"]); exit;
}

$stmt = $conn->prepare("INSERT INTO objects (name, description, address) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $data['name'], $data['description'], $data['address']);
if (!$stmt->execute()) { echo json_encode(["error" => "Insert failed: " . $stmt->error]); exit; }
$objects_id = $conn->insert_id;
$stmt->close();

$stmt2 = $conn->prepare("INSERT INTO restaurants (objects_id, avg_price) VALUES (?, ?)");
$stmt2->bind_param("id", $objects_id, $data['avg_price']);
if ($stmt2->execute()) {
    echo json_encode(["success" => true, "objects_id" => $objects_id]);
} else {
    echo json_encode(["error" => "Insert into restaurants failed: " . $stmt2->error]);
}
$stmt2->close();
$conn->close();
?>
