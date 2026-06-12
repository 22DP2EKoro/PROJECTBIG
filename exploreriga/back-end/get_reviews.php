<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/db.php';

$type = $_GET['type'] ?? null;

if ($type && !in_array($type, ['museum', 'restaurant', 'culture'])) {
    echo json_encode([]); exit;
}

if ($type) {
    $stmt = $conn->prepare("SELECT object_id, comment, rating FROM reviews WHERE type = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[$row['object_id']][] = ['comment' => $row['comment'], 'rating' => (int)$row['rating']];
    }
    echo json_encode($reviews);
} else {
    $result = $conn->query("SELECT type, object_id, comment, rating FROM reviews ORDER BY created_at DESC");
    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[$row['type']][$row['object_id']][] = ['comment' => $row['comment'], 'rating' => (int)$row['rating']];
    }
    echo json_encode($reviews);
}

$conn->close();
?>
