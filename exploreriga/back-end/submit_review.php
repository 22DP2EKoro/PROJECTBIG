<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['type'], $data['objectId'], $data['comment'], $data['rating'])) {
    echo json_encode(["error" => "Missing required fields."]); exit;
}

$type     = $data['type'];
$objectId = strval($data['objectId']);
$comment  = trim($data['comment']);
$rating   = intval($data['rating']);

if (!in_array($type, ['museum', 'restaurant', 'culture'])) {
    echo json_encode(["error" => "Invalid type."]); exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(["error" => "Rating must be between 1 and 5."]); exit;
}

// Saglabā JSON failā
$file    = 'C:/Users/26420/OneDrive/Dokumenti/PROJECTBIG/exploreriga/back-end/reviews.json';
$reviews = [];
if (file_exists($file)) {
    $reviews = json_decode(file_get_contents($file), true) ?? [];
}
$reviews[$type][$objectId][] = ['comment' => $comment, 'rating' => $rating];
file_put_contents($file, json_encode($reviews, JSON_PRETTY_PRINT), LOCK_EX);

// Saglabā datubāzē
$conn = new mysqli("localhost", "root", "", "user_auth");
if (!$conn->connect_error) {
    $conn->query("CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type VARCHAR(20) NOT NULL,
        object_id VARCHAR(50) NOT NULL,
        comment TEXT NOT NULL,
        rating TINYINT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $stmt = $conn->prepare("INSERT INTO reviews (type, object_id, comment, rating) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $type, $objectId, $comment, $rating);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

echo json_encode(["message" => "Review submitted successfully."]);
?>
