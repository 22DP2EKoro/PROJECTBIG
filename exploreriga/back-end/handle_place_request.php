<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$conn = new mysqli("localhost", "root", "", "user_auth");
if ($conn->connect_error) { echo json_encode(["error" => "DB connection failed"]); exit; }

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['id'], $data['action'])) {
    echo json_encode(["error" => "Missing id or action"]); exit;
}

$allowed = ['approved', 'rejected'];
if (!in_array($data['action'], $allowed)) {
    echo json_encode(["error" => "Invalid action"]); exit;
}

$stmt = $conn->prepare("UPDATE place_requests SET status = ? WHERE id = ?");
$stmt->bind_param("si", $data['action'], $data['id']);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Update failed: " . $stmt->error]);
}
$stmt->close();
$conn->close();
?>
