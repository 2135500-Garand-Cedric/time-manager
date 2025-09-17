<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db_conn.php'; // Adjust path if needed

// Simple token/password authentication
$token = $_GET['token'] ?? '';
$expected_token = getenv('API_SECRET');

if ($token !== $expected_token) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Fetch all records with activity name
$sql = "SELECT r.id, a.name AS activity_name, r.start_time, r.end_time, r.duration_seconds
        FROM records r
        JOIN activities a ON r.activity_id = a.id
        ORDER BY r.start_time ASC";

$res = $mysqli->query($sql);

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

// Return JSON
echo json_encode($data, JSON_PRETTY_PRINT);
