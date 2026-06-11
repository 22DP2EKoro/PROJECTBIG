<?php
ini_set('display_errors', 0);
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
try {
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    if (!$user_id) { echo json_encode(["error" => "Missing user_id"]); exit; }

    $conn = @new mysqli("localhost", "root", "", "user_auth");
    if (!$conn || $conn->connect_error) {
        echo json_encode(["error" => "DB connection failed"]); exit;
    }

    $conn->query("CREATE TABLE IF NOT EXISTS user_tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(500) NOT NULL,
        status ENUM('to-do','in progress','finished') DEFAULT 'to-do',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $stmt = $conn->prepare("SELECT id, name, status FROM user_tasks WHERE user_id = ? ORDER BY created_at ASC");
    if (!$stmt) { echo json_encode(["error" => "Prepare failed: " . $conn->error]); exit; }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    echo json_encode(["tasks" => $tasks]);
    $stmt->close();
    $conn->close();
} catch (Throwable $e) {
    echo json_encode(["error" => "Exception: " . $e->getMessage()]);
}
