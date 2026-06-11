<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$conn = new mysqli("localhost", "root", "", "user_auth");
if ($conn->connect_error) { echo json_encode(["error" => "DB connection failed"]); exit; }

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['name'])) { echo json_encode(["error" => "Name required"]); exit; }

$stmt = $conn->prepare(
    "SELECT o.id AS objects_id, o.name, o.description, o.address, p.facts
     FROM objects o JOIN place p ON p.objects_id = o.id WHERE o.name = ?"
);
$stmt->bind_param("s", $data['name']);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
echo $row ? json_encode($row) : json_encode(["error" => "Place not found"]);
$stmt->close();
$conn->close();
?>
