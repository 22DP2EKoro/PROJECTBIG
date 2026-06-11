<?php
// =========================
// CORS CONFIG (FIXED)
// =========================

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Return JSON always
header("Content-Type: application/json");

// =========================
// DATABASE CONNECTION
// =========================

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_auth";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["message" => "Database connection failed"]);
    exit;
}

// =========================
// INPUT DATA
// =========================

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(["message" => "Email or password not provided"]);
    exit;
}

$email = $data['email'];
$password = $data['password'];

// =========================
// CHECK USER
// =========================

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["message" => "User not found"]);
    exit;
}

$user = $result->fetch_assoc();

// =========================
// VERIFY PASSWORD
// =========================

if (!password_verify($password, $user['password'])) {
    echo json_encode(["message" => "Incorrect password"]);
    exit;
}

// =========================
// SUCCESS RESPONSE
// =========================

if ($user['is_admin'] == 1) {
    echo json_encode([
        "message" => "Admin login successful!",
        "role" => "admin",
        "id" => $user['id'],
        "email" => $user['email']
    ]);
} else {
    echo json_encode([
        "message" => "Login successful!",
        "role" => "user",
        "id" => $user['id'],
        "email" => $user['email']
    ]);
}

$stmt->close();
$conn->close();
?>