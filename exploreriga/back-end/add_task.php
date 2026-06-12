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
    if (!$data || !isset($data['user_id'], $data['name'])) {
        echo json_encode(["error" => "Missing fields"]); exit;
    }

    require_once __DIR__ . '/db.php';

    $conn->query("CREATE TABLE IF NOT EXISTS user_tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(500) NOT NULL,
        status ENUM('to-do','in progress','finished') DEFAULT 'to-do',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $stmt = $conn->prepare("INSERT INTO user_tasks (user_id, name, status) VALUES (?, ?, 'to-do')");
    if (!$stmt) { echo json_encode(["error" => "Prepare failed: " . $conn->error]); exit; }
    $stmt->bind_param("is", $data['user_id'], $data['name']);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Insert failed: " . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
} catch (Throwable $e) {
    echo json_encode(["error" => "Exception: " . $e->getMessage()]);
}
