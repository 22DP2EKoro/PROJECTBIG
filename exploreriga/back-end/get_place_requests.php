<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$conn = new mysqli("localhost", "root", "", "user_auth");
if ($conn->connect_error) { echo json_encode(["error" => "DB connection failed"]); exit; }

$result = $conn->query("SELECT * FROM place_requests ORDER BY submitted_at DESC");
if (!$result) { echo json_encode(["error" => "Query failed"]); exit; }

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode($rows);
$conn->close();
?>
