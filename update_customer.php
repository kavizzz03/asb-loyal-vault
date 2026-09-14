<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Authenticate proxy session boundary
if (!isset($_SESSION['authenticated_user']) || $_SESSION['authenticated_user'] !== true) {
    echo json_encode(["success" => false, "message" => "Unauthorized proxy access session expired."]);
    exit;
}

require_once 'config.php';

// 2. Read incoming JSON payload from frontend
$rawInput = file_get_contents('php://input');
$payload = json_decode($rawInput, true);

if (!$payload || empty($payload['CM_CODE']) || empty($payload['CM_NAME'])) {
    echo json_encode(["success" => false, "message" => "Invalid structural payload data stream."]);
    exit;
}

// 3. Confirm target security domain match
if (trim($payload['CM_CODE']) !== trim($_SESSION['customer_profile']['CM_CODE'] ?? '')) {
    echo json_encode(["success" => false, "message" => "Security verification failure: Customer signature mismatch."]);
    exit;
}

// 4. Update the ERP Master Database Gateway via cURL
$apiResult = updateMasterCustomerData($payload);

// 5. Respond back downstream to JavaScript
echo json_encode($apiResult);
exit;
?>