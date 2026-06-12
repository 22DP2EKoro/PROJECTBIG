<?php
// ✅ CORS HEADERS — MUST be at the very top of the file
$allowed_origins = [
    'http://localhost:5173',
    'http://127.0.0.1:5173',
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
} else {
    header("Access-Control-Allow-Origin: *"); // Optional fallback
}
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/db.php';

// ✅ Parse incoming JSON
$data = json_decode(file_get_contents("php://input"), true);
if (
    !$data ||
    !isset($data['name'], $data['description'], $data['address'], $data['avg_price'])
) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
    exit();
}

// ✅ Insert into `objects`
$stmt = $conn->prepare("INSERT INTO objects (name, description, address) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $data['name'], $data['description'], $data['address']);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Insert into objects failed: " . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit();
}

$object_id = $conn->insert_id;
$stmt->close();

// ✅ Insert into `restaurants`
$stmt2 = $conn->prepare("INSERT INTO restaurants (objects_id, avg_price) VALUES (?, ?)");
$stmt2->bind_param("id", $object_id, $data['avg_price']);

if ($stmt2->execute()) {
    echo json_encode(["success" => true, "objects_id" => $object_id]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Insert into restaurants failed: " . $stmt2->error]);
}

$stmt2->close();
$conn->close();
?>
