<?php
ini_set('display_errors', 0);
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
try {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data || !isset($data['id'])) {
        echo json_encode(["error" => "Missing task id"]); exit;
    }

    $conn = @new mysqli("localhost", "root", "", "user_auth");
    if (!$conn || $conn->connect_error) {
        echo json_encode(["error" => "DB connection failed"]); exit;
    }

    if (isset($data['name']) && isset($data['status'])) {
        $stmt = $conn->prepare("UPDATE user_tasks SET name = ?, status = ? WHERE id = ?");
        if (!$stmt) { echo json_encode(["error" => "Prepare failed"]); exit; }
        $stmt->bind_param("ssi", $data['name'], $data['status'], $data['id']);
    } elseif (isset($data['name'])) {
        $stmt = $conn->prepare("UPDATE user_tasks SET name = ? WHERE id = ?");
        if (!$stmt) { echo json_encode(["error" => "Prepare failed"]); exit; }
        $stmt->bind_param("si", $data['name'], $data['id']);
    } elseif (isset($data['status'])) {
        $stmt = $conn->prepare("UPDATE user_tasks SET status = ? WHERE id = ?");
        if (!$stmt) { echo json_encode(["error" => "Prepare failed"]); exit; }
        $stmt->bind_param("si", $data['status'], $data['id']);
    } else {
        echo json_encode(["error" => "Nothing to update"]); exit;
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Update failed: " . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
} catch (Throwable $e) {
    echo json_encode(["error" => "Exception: " . $e->getMessage()]);
}
