<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$conn = new mysqli("localhost", "root", "", "user_auth");
if ($conn->connect_error) { echo json_encode(["error" => "DB connection failed"]); exit; }

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['objects_id'], $data['name'], $data['description'], $data['address'])) {
    echo json_encode(["error" => "Missing required fields"]); exit;
}

$stmt = $conn->prepare("UPDATE objects SET name=?, description=?, address=? WHERE id=?");
$stmt->bind_param("sssi", $data['name'], $data['description'], $data['address'], $data['objects_id']);
if (!$stmt->execute()) { echo json_encode(["error" => "Update objects failed: " . $stmt->error]); exit; }
$stmt->close();

$work_hours = $data['work_hours'] ?? '';
$stmt2 = $conn->prepare("UPDATE museums SET work_hours=? WHERE objects_id=?");
$stmt2->bind_param("si", $work_hours, $data['objects_id']);
if ($stmt2->execute()) {
    echo json_encode(["success" => "Museum updated successfully"]);
} else {
    echo json_encode(["error" => "Update museums failed: " . $stmt2->error]);
}
$stmt2->close();
$conn->close();
?>
