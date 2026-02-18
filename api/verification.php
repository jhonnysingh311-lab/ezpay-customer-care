<?php
header('Content-Type: application/json');

// --- CORS HEADERS ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

require_once 'db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);

// Validation
if (empty($input['user_id']) || empty($input['full_name']) || empty($input['pin'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO verification (user_id, full_name, problem, security_pin, experience_level) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $input['user_id'],
        $input['full_name'],
        $input['problem'], // Make sure to handle potential NULL if not critical, or validate
        $input['pin'],
        $input['experience']
    ]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error', 'details' => $e->getMessage()]);
}
?>
