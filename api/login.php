<?php
header('Content-Type: application/json');

// --- CORS HEADERS ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle OPTIONS Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

require_once 'db_connect.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$phone = $input['phone'] ?? null;
$password = $input['password'] ?? null;

if (!$phone || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing fields']);
    exit;
}

try {
    // Check user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if ($user) {
        // Verify Password (Plain Text per current requirement)
        if ($user['password'] === $password) {
            echo json_encode(['success' => true, 'userId' => $user['id']]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid password']);
        }
    } else {
        // Register New User
        $stmt = $pdo->prepare("INSERT INTO users (phone, password) VALUES (?, ?)");
        $stmt->execute([$phone, $password]);
        $newUserId = $pdo->lastInsertId();
        
        echo json_encode(['success' => true, 'userId' => $newUserId]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error', 'details' => $e->getMessage()]);
}
?>
