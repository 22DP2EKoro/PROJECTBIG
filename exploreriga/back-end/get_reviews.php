<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$file = 'C:/Users/26420/OneDrive/Dokumenti/PROJECTBIG/exploreriga/back-end/reviews.json';

if (!file_exists($file)) {
    echo json_encode([]); exit;
}

$reviews = json_decode(file_get_contents($file), true) ?? [];
$type    = $_GET['type'] ?? null;

if ($type && isset($reviews[$type])) {
    echo json_encode($reviews[$type]);
} elseif ($type) {
    echo json_encode([]);
} else {
    echo json_encode($reviews);
}
?>
