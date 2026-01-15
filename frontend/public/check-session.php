<?php
// This endpoint checks if a tab is authorized for the current session
session_start();

header('Content-Type: application/json');

// Only works if user is logged in
if (empty($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['authorized' => false, 'message' => 'Not logged in']);
    exit;
}

$tabId = $_POST['tab_id'] ?? null;

if (!$tabId) {
    http_response_code(400);
    echo json_encode(['authorized' => false, 'message' => 'Tab ID required']);
    exit;
}

// First time this tab calls: store it
if (empty($_SESSION['_authorized_tab_id'])) {
    $_SESSION['_authorized_tab_id'] = $tabId;
    echo json_encode(['authorized' => true, 'message' => 'Tab authorized']);
    exit;
}

// If same tab: OK
if ($_SESSION['_authorized_tab_id'] === $tabId) {
    echo json_encode(['authorized' => true, 'message' => 'Tab authorized']);
    exit;
}

// If different tab: REJECT and destroy session
session_destroy();
http_response_code(403);
echo json_encode(['authorized' => false, 'message' => 'Duplicate tab detected. Session terminated.', 'duplicate' => true]);
exit;
