<?php
// 1. Prevent any stray database drivers warnings from breaking the JSON payload stream
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

include 'db.php'; // Your MS SQL connection file

$apiKey = "ASB2026SECRET";
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Empty or invalid JSON payload received."]);
    exit;
}

// 2. FIX: Convert all payload keys to UPPERCASE to prevent JS lowercase 'key' vs MS SQL uppercase parameter bugs
$data = array_change_key_case($data, CASE_UPPER);

// Check both uppercase 'KEY' (normalized) or incoming lowercase 'key'
$incomingKey = $data['KEY'] ?? '';
if ($incomingKey !== $apiKey) {
    echo json_encode(["success" => false, "message" => "Unauthorized API key signature initialization failure."]);
    exit;
}

$cm_code = $data['CM_CODE'] ?? '';
if (empty($cm_code)) {
    echo json_encode(["success" => false, "message" => "Customer Code parameter is required."]);
    exit;
}

// 3. Normalization parameters - ensures empty fields map to NULL instead of breaking MS SQL data types
$cm_name  = !empty($data['CM_NAME'])  ? trim($data['CM_NAME'])  : null;
$cm_add1  = !empty($data['CM_ADD1'])  ? trim($data['CM_ADD1'])  : null;
$cm_add2  = !empty($data['CM_ADD2'])  ? trim($data['CM_ADD2'])  : null;
$cm_add3  = !empty($data['CM_ADD3'])  ? trim($data['CM_ADD3'])  : null;
$cm_add4  = !empty($data['CM_ADD4'])  ? trim($data['CM_ADD4'])  : null;
$cm_nic   = !empty($data['CM_NIC'])   ? trim($data['CM_NIC'])   : null;
$cm_email = !empty($data['CM_EMAIL']) ? trim($data['CM_EMAIL']) : null;

// Convert empty strings to null values so MS SQL doesn't save default 1900-01-01 dates
$cm_dob   = !empty($data['CM_DOB'])   ? $data['CM_DOB']         : null;

if (empty($cm_name)) {
    echo json_encode(["success" => false, "message" => "Customer Name cannot be empty."]);
    exit;
}

// 4. FIX: Re-typed SQL query string from scratch to permanently purge hidden corrupt whitespace bytes (\xa0)
$sql = "UPDATE M_TBLCUSTOMERS SET 
        CM_NAME = ?, 
        CM_ADD1 = ?, 
        CM_ADD2 = ?, 
        CM_ADD3 = ?, 
        CM_ADD4 = ?, 
        CM_NIC = ?, 
        CM_DOB = ?, 
        CM_EMAIL = ?,
        MD_DATE = GETDATE(),
        MD_BY = 'SYSTEM_WEB'
        WHERE CM_CODE = ?";

$params = [
    $cm_name,
    $cm_add1,
    $cm_add2,
    $cm_add3,
    $cm_add4,
    $cm_nic,
    $cm_dob,
    $cm_email,
    $cm_code
];

// Verify database connection variable exists before executing query
if (!isset($conn) || !$conn) {
    echo json_encode(["success" => false, "message" => "Local MS SQL database connection handle resource missing or disconnected."]);
    exit;
}

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt) {
    $rowsAffected = sqlsrv_rows_affected($stmt);
    if ($rowsAffected === 0) {
        echo json_encode([
            "success" => false, 
            "message" => "Security authorization validated, but the Customer Code does not exist in the ERP records database."
        ]);
    } else {
        echo json_encode([
            "success" => true, 
            "message" => "Customer updated successfully"
        ]);
    }
} else {
    // 5. FIX: sqlsrv_errors() returns a native multidimensional array, which must be safely parsed inside JSON encoding channels
    echo json_encode([
        "success" => false, 
        "message" => "Update query rejected by local engine structural database rules.", 
        "debug_context" => sqlsrv_errors()
    ]);
}
?>