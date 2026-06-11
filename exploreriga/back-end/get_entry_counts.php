<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$conn = new mysqli("localhost", "root", "", "user_auth");
if ($conn->connect_error) { echo json_encode(["error" => "DB connection failed"]); exit; }

$counts = [];
$queries = [
    'museum'     => 'SELECT COUNT(*) AS cnt FROM museums',
    'restaurant' => 'SELECT COUNT(*) AS cnt FROM restaurants',
    'place'      => 'SELECT COUNT(*) AS cnt FROM place',
    'user'       => 'SELECT COUNT(*) AS cnt FROM users',
    'review'     => 'SELECT COUNT(*) AS cnt FROM reviews',
];

foreach ($queries as $key => $sql) {
    $result = $conn->query($sql);
    $counts[$key] = $result ? (int)$result->fetch_assoc()['cnt'] : 0;
}

echo json_encode($counts);
$conn->close();
?>
