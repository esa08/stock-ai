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

require_once __DIR__ . '/../config/db.php';

$user_id = (int) $_SESSION['user']['id'];

// Soft delete: update deleted_at column for all user's conversations
$stmt = $mysqli->prepare(
  'UPDATE conversations 
   SET deleted_at = NOW() 
   WHERE user_id = ? AND deleted_at IS NULL'
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

$deleted_count = $stmt->affected_rows;
$stmt->close();

echo json_encode([
  'success' => true,
  'message' => 'All conversations deleted successfully.',
  'deleted_count' => $deleted_count
]);
