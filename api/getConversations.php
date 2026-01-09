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

require_once __DIR__ . '/../config/db.php';

$user_id = (int) $_SESSION['user']['id'];

$stmt = $mysqli->prepare(
  'SELECT id, user_id, title, created_at, updated_at, last_message_at
   FROM conversations
   WHERE user_id = ? AND deleted_at IS NULL
   ORDER BY COALESCE(last_message_at, updated_at, created_at) DESC'
);

if (!$stmt) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'message' => 'Server error.'
  ]);
  exit;
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$conversations = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();

echo json_encode([
  'success' => true,
  'data' => $conversations
]);