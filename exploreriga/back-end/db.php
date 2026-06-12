<?php
$db_host = 'localhost';
$db_user = 'u690305643_root';
$db_pass = 'Meitene.0104';
$db_name = 'user_auth';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    header("Content-Type: application/json");
    echo json_encode(["error" => "DB connection failed"]);
    exit;
}
