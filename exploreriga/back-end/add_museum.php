<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/db.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['name'], $data['description'], $data['address'], $data['work_hours'])) {
    echo json_encode(["error" => "Missing required fields"]); exit;
}

$stmt = $conn->prepare("INSERT INTO objects (name, description, address) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $data['name'], $data['description'], $data['address']);
if (!$stmt->execute()) { echo json_encode(["error" => "Insert failed: " . $stmt->error]); exit; }
$objects_id = $conn->insert_id;
$stmt->close();

$stmt2 = $conn->prepare("INSERT INTO museums (objects_id, work_hours) VALUES (?, ?)");
$stmt2->bind_param("is", $objects_id, $data['work_hours']);
if ($stmt2->execute()) {
    echo json_encode(["success" => true, "objects_id" => $objects_id]);
} else {
    echo json_encode(["error" => "Insert into museums failed: " . $stmt2->error]);
}
$stmt2->close();
$conn->close();
?>
