<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/db.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['objects_id'], $data['name'], $data['description'], $data['address'])) {
    echo json_encode(["error" => "Missing required fields"]); exit;
}

$stmt = $conn->prepare("UPDATE objects SET name=?, description=?, address=? WHERE id=?");
$stmt->bind_param("sssi", $data['name'], $data['description'], $data['address'], $data['objects_id']);
if (!$stmt->execute()) { echo json_encode(["error" => "Update objects failed: " . $stmt->error]); exit; }
$stmt->close();

$facts = $data['facts'] ?? '';
$stmt2 = $conn->prepare("UPDATE place SET facts=? WHERE objects_id=?");
$stmt2->bind_param("si", $facts, $data['objects_id']);
if ($stmt2->execute()) {
    echo json_encode(["success" => "Place updated successfully"]);
} else {
    echo json_encode(["error" => "Update place failed: " . $stmt2->error]);
}
$stmt2->close();
$conn->close();
?>
