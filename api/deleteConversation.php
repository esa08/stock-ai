<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode([
    'success' => false,
    'message' => 'Method not allowed.'
  ]);
  exit;
}

if (!isset($_SESSION['user']['id'])) {
  http_response_code(401);
  echo json_encode([
    'success' => false,
    'message' => 'Unauthorized.'
  ]);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['conversation_id']) || !is_numeric($input['conversation_id'])) {
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'message' => 'Invalid conversation_id.'
  ]);
  exit;
}

require_once __DIR__ . '/../config/db.php';

$user_id = (int) $_SESSION['user']['id'];
$conversation_id = (int) $input['conversation_id'];

// Soft delete: update deleted_at column
$stmt = $mysqli->prepare(
  'UPDATE conversations 
   SET deleted_at = NOW() 
   WHERE id = ? AND user_id = ? AND deleted_at IS NULL'
);

if (!$stmt) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'message' => 'Server error.'
  ]);
  exit;
}

$stmt->bind_param('ii', $conversation_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows === 0) {
  $stmt->close();
  http_response_code(404);
  echo json_encode([
    'success' => false,
    'message' => 'Conversation not found or already deleted.'
  ]);
  exit;
}

$stmt->close();

echo json_encode([
  'success' => true,
  'message' => 'Conversation deleted successfully.'
]);
