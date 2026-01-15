<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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

if (!isset($_GET['conversation_id']) || !is_numeric($_GET['conversation_id'])) {
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'message' => 'Invalid conversation_id.'
  ]);
  exit;
}

require_once __DIR__ . '/../config/db.php';

$user_id = (int) $_SESSION['user']['id'];
$conversation_id = (int) $_GET['conversation_id'];

// Verify that the conversation belongs to the current user
$stmt = $mysqli->prepare(
  'SELECT id FROM conversations 
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
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  http_response_code(404);
  echo json_encode([
    'success' => false,
    'message' => 'Conversation not found.'
  ]);
  $stmt->close();
  exit;
}
$stmt->close();

// Get messages for the conversation
$stmt = $mysqli->prepare(
  'SELECT id, role, content, created_at
   FROM messages
   WHERE conversation_id = ? AND deleted_at IS NULL
   ORDER BY created_at ASC'
);

if (!$stmt) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'message' => 'Server error.'
  ]);
  exit;
}

$stmt->bind_param('i', $conversation_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();

echo json_encode([
  'success' => true,
  'data' => $messages
]);
