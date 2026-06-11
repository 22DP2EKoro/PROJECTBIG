<?php
ini_set('display_errors', 0);
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

try {
    $conn = @new mysqli("localhost", "root", "", "user_auth");
    if (!$conn || $conn->connect_error) {
        echo json_encode(["error" => "DB connection failed: " . ($conn->connect_error ?? 'unknown')]); exit;
    }

    $conn->query("
      CREATE TABLE IF NOT EXISTS place_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        place_name VARCHAR(255) NOT NULL,
        category ENUM('museum', 'restaurant', 'culture') NOT NULL,
        description TEXT,
        address VARCHAR(255),
        website VARCHAR(255),
        opening_hours VARCHAR(255),
        image_url VARCHAR(500),
        contact_name VARCHAR(255),
        contact_email VARCHAR(255) NOT NULL,
        notes TEXT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      )
    ");

    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data || !isset($data['place_name'], $data['category'], $data['contact_email'])) {
        echo json_encode(["error" => "Missing required fields"]); exit;
    }

    $stmt = $conn->prepare("
      INSERT INTO place_requests (place_name, category, description, address, website, opening_hours, image_url, contact_name, contact_email, notes)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt) {
        echo json_encode(["error" => "Prepare failed: " . $conn->error]); exit;
    }

    $desc    = isset($data['description'])   ? $data['description']   : '';
    $addr    = isset($data['address'])       ? $data['address']       : '';
    $web     = isset($data['website'])       ? $data['website']       : '';
    $hours   = isset($data['opening_hours']) ? $data['opening_hours'] : '';
    $img     = isset($data['image_url'])     ? $data['image_url']     : '';
    $cname   = isset($data['contact_name'])  ? $data['contact_name']  : '';
    $notes   = isset($data['notes'])         ? $data['notes']         : '';

    $stmt->bind_param(
        "ssssssssss",
        $data['place_name'],
        $data['category'],
        $desc, $addr, $web, $hours, $img, $cname,
        $data['contact_email'],
        $notes
    );

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Insert failed: " . $stmt->error]);
    }
    $stmt->close();
    $conn->close();

} catch (Throwable $e) {
    echo json_encode(["error" => "Exception: " . $e->getMessage()]);
}
