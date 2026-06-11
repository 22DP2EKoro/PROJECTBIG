<?php
// 🟢 MUST BE AT THE VERY TOP - BEFORE ANY HTML OR OUTPUT
$allowed_origins = [
    'http://localhost:5173',
    'http://127.0.0.1:5173'
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
} else {
    header("Access-Control-Allow-Origin: *");  // You can restrict this in production
}

header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// ✅ Always handle OPTIONS preflight first
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "user_auth");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (
    !$data ||
    !isset($data['name'], $data['description'], $data['address'], $data['work_hours'])
) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields"]);
    exit();
}

// Insert into objects
$stmt1 = $conn->prepare("INSERT INTO objects (name, description, address) VALUES (?, ?, ?)");
$stmt1->bind_param("sss", $data['name'], $data['description'], $data['address']);

if (!$stmt1->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Insert into objects failed: " . $stmt1->error]);
    $stmt1->close();
    $conn->close();
    exit();
}

$objects_id = $conn->insert_id;
$stmt1->close();

// Insert into museums
$stmt2 = $conn->prepare("INSERT INTO museums (objects_id, work_hours) VALUES (?, ?)");
$stmt2->bind_param("is", $objects_id, $data['work_hours']);

if (!$stmt2->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Insert into museums failed: " . $stmt2->error]);
} else {
    echo json_encode(["success" => true, "objects_id" => $objects_id]);
}

$stmt2->close();
$conn->close();
?>
