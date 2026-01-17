<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Load environment variables
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/db.php';

// Disable output buffering for streaming
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', false);
ini_set('implicit_flush', true);
ob_implicit_flush(true);

// Clear any existing output buffers
while (ob_get_level() > 0) {
  ob_end_clean();
}

// Set headers for SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');

// Helper function to send SSE event
function sendSSE($event, $data) {
  echo "event: $event\n";
  echo "data: " . json_encode($data) . "\n\n";
  flush();
}

// Helper function to send error and exit
function sendError($message, $code = 400) {
  sendSSE('error', ['message' => $message, 'code' => $code]);
  exit;
}

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  sendError('Method not allowed.', 405);
}

if (!isset($_SESSION['user']['id'])) {
  sendError('Unauthorized.', 401);
}

// Parse input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['message']) || trim($input['message']) === '') {
  sendError('Message is required.', 400);
}

$user_id = (int) $_SESSION['user']['id'];
$message = trim($input['message']);
$conversation_id = isset($input['conversation_id']) ? (int) $input['conversation_id'] : null;

// Get API key
$apiKey = env('GEMINI_API_KEY');
if (!$apiKey) {
  sendError('API key not configured.', 500);
}

// Function to call Gemini API (non-streaming, for title generation)
function callGeminiSync($apiKey, $prompt) {
  $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$apiKey";
  
  $payload = [
    'contents' => [
      [
        'parts' => [
          ['text' => $prompt]
        ]
      ]
    ],
    'generationConfig' => [
      'maxOutputTokens' => 50,
      'temperature' => 0.7
    ]
  ];

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($httpCode !== 200) {
    return null;
  }

  $data = json_decode($response, true);
  
  if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
    return trim($data['candidates'][0]['content']['parts'][0]['text']);
  }
  
  return null;
}

// Function to get conversation history
function getConversationHistory($mysqli, $conversation_id) {
  $stmt = $mysqli->prepare(
    'SELECT role, content FROM messages 
     WHERE conversation_id = ? AND deleted_at IS NULL 
     ORDER BY created_at ASC 
     LIMIT 20'
  );
  $stmt->bind_param('i', $conversation_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $history = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
  return $history;
}

// Create new conversation if needed
if (!$conversation_id) {
  // Generate title from the first message
  $titlePrompt = "Generate a very short title (max 5 words) for a conversation that starts with this message. Only respond with the title, nothing else:\n\n$message";
  $title = callGeminiSync($apiKey, $titlePrompt);
  
  if (!$title) {
    $title = substr($message, 0, 50) . (strlen($message) > 50 ? '...' : '');
  }

  // Insert new conversation
  $stmt = $mysqli->prepare(
    'INSERT INTO conversations (user_id, title, last_message_at) VALUES (?, ?, NOW())'
  );
  $stmt->bind_param('is', $user_id, $title);
  
  if (!$stmt->execute()) {
    sendError('Failed to create conversation.', 500);
  }
  
  $conversation_id = $mysqli->insert_id;
  $stmt->close();

  sendSSE('conversation_created', [
    'conversation_id' => $conversation_id,
    'title' => $title
  ]);
} else {
  // Verify conversation belongs to user
  $stmt = $mysqli->prepare(
    'SELECT id FROM conversations WHERE id = ? AND user_id = ? AND deleted_at IS NULL'
  );
  $stmt->bind_param('ii', $conversation_id, $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows === 0) {
    sendError('Conversation not found.', 404);
  }
  $stmt->close();
}

// Save user message
$stmt = $mysqli->prepare(
  'INSERT INTO messages (conversation_id, role, content) VALUES (?, "user", ?)'
);
$stmt->bind_param('is', $conversation_id, $message);

if (!$stmt->execute()) {
  sendError('Failed to save message.', 500);
}
$stmt->close();

// Update conversation last_message_at
$stmt = $mysqli->prepare('UPDATE conversations SET last_message_at = NOW() WHERE id = ?');
$stmt->bind_param('i', $conversation_id);
$stmt->execute();
$stmt->close();

sendSSE('message_saved', ['role' => 'user', 'content' => $message]);

// Build conversation history for context
$history = getConversationHistory($mysqli, $conversation_id);
$contents = [];

// System instruction for financial chatbot
$systemPrompt = "You are a helpful financial assistant chatbot. You help users with financial planning, budgeting, investment advice, and money management questions. Be concise but informative. If you don't know something, say so honestly.";

foreach ($history as $msg) {
  $contents[] = [
    'role' => $msg['role'] === 'user' ? 'user' : 'model',
    'parts' => [['text' => $msg['content']]]
  ];
}

// Streaming call to Gemini API
$streamUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:streamGenerateContent?alt=sse&key=$apiKey";

$payload = [
  'contents' => $contents,
  'systemInstruction' => [
    'parts' => [['text' => $systemPrompt]]
  ],
  'generationConfig' => [
    'maxOutputTokens' => 2048,
    'temperature' => 0.8
  ]
];

$ch = curl_init($streamUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);

$fullResponse = '';

// Process streaming response
curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$fullResponse) {
  $lines = explode("\n", $data);
  
  foreach ($lines as $line) {
    $line = trim($line);
    
    if (empty($line) || $line === 'data: [DONE]') {
      continue;
    }
    
    if (strpos($line, 'data: ') === 0) {
      $jsonStr = substr($line, 6);
      $json = json_decode($jsonStr, true);
      
      if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
        $text = $json['candidates'][0]['content']['parts'][0]['text'];
        $fullResponse .= $text;
        sendSSE('chunk', ['text' => $text]);
      }
    }
  }
  
  return strlen($data);
});

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 && empty($fullResponse)) {
  sendError('Failed to get AI response.', 500);
}

// Save assistant response
if (!empty($fullResponse)) {
  $stmt = $mysqli->prepare(
    'INSERT INTO messages (conversation_id, role, content) VALUES (?, "assistant", ?)'
  );
  $stmt->bind_param('is', $conversation_id, $fullResponse);
  $stmt->execute();
  $stmt->close();

  // Update conversation last_message_at
  $stmt = $mysqli->prepare('UPDATE conversations SET last_message_at = NOW() WHERE id = ?');
  $stmt->bind_param('i', $conversation_id);
  $stmt->execute();
  $stmt->close();
}

sendSSE('done', [
  'conversation_id' => $conversation_id,
  'full_response' => $fullResponse
]);
