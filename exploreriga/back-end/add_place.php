<?php
// CORS headers
$allowed_origins = ['http://localhost:5173', 'http://127.0.0.1:5173'];
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
} else {
    header("Access-Control-Allow-Origin: *");
}
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "", "user_auth");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "DB connect failed: " . $conn->connect_error]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

error_log("Received data: " . print_r($data, true));

if (!$data || !isset($data['name'], $data['description'], $data['address'])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
    exit();
}

$facts = $data['facts'] ?? "";

$stmt = $conn->prepare("INSERT INTO objects (name, description, address) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $data['name'], $data['description'], $data['address']);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Insert into objects failed: " . $stmt->error]);
    exit();
}
$object_id = $conn->insert_id;
$stmt->close();

$stmt2 = $conn->prepare("INSERT INTO place (objects_id, facts) VALUES (?, ?)");
$stmt2->bind_param("is", $object_id, $facts);
if ($stmt2->execute()) {
    echo json_encode(["success" => true, "objects_id" => $object_id]);
} else {
    error_log("Insert into places failed: " . $stmt2->error);
    http_response_code(500);
    echo json_encode(["error" => "Insert into places failed: " . $stmt2->error]);
}

$stmt2->close();
$conn->close();
