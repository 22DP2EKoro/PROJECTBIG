<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/db.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['name'])) { echo json_encode(["error" => "Name required"]); exit; }

$stmt = $conn->prepare("SELECT id FROM objects WHERE name = ?");
$stmt->bind_param("s", $data['name']);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$row) { echo json_encode(["error" => "Place not found"]); exit; }
$id = $row['id'];

$s = $conn->prepare("DELETE FROM place WHERE objects_id = ?");
$s->bind_param("i", $id); $s->execute(); $s->close();

$s2 = $conn->prepare("DELETE FROM objects WHERE id = ?");
$s2->bind_param("i", $id); $s2->execute(); $s2->close();

echo json_encode(["success" => "Place deleted successfully"]);
$conn->close();
?>
