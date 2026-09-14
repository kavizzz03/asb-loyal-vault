<?php
// Prevent internal database driver errors from injecting raw HTML into the output stream
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

include 'db.php'; // Your MS SQL connection file

$apiKey = "ASB2026SECRET";   

$key = $_GET['key'] ?? '';
$search = trim($_GET['search'] ?? '');

if ($key !== $apiKey) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized API key signature initialization failure."
    ]);
    exit;
}

if (empty($search)) {
    echo json_encode([
        "success" => false,
        "message" => "Search query criteria parameters required."
    ]);
    exit;
}

// Check database connection variable status explicitly
if (!isset($conn) || !$conn) {
    echo json_encode([
        "success" => false,
        "message" => "Local MS SQL database connection handle resource missing or disconnected."
    ]);
    exit;
}

// Customer Search
$sql = "
SELECT TOP 1
    CM_CODE,
    CM_OPENINGPOINTS,
    CM_TITLE,
    CM_NAME,
    CM_NIC,
    CM_MOBILE,
    CM_DOB,
    CM_EMAIL,
    CM_POINTS,
    CM_ADD1,
    CM_ADD2,
    CM_ADD3,
    CM_ADD4
FROM M_TBLCUSTOMERS
WHERE CM_CODE = ?
   OR CM_MOBILE = ?
   OR CM_NIC = ?
";

$params = [$search, $search, $search];
$stmt = sqlsrv_query($conn, $sql, $params);

if (!$stmt || !sqlsrv_has_rows($stmt)) {
    echo json_encode([
        "success" => false,
        "message" => "Customer record target not located within master database system indices."
    ]);
    exit;
}

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// Format DOB
if (isset($row['CM_DOB']) && $row['CM_DOB'] instanceof DateTime) {
    $row['CM_DOB'] = $row['CM_DOB']->format('Y-m-d');
} else {
    $row['CM_DOB'] = !empty($row['CM_DOB']) ? $row['CM_DOB'] : '';
}

// Clean Email
$row['CM_EMAIL'] = !empty($row['CM_EMAIL']) ? trim($row['CM_EMAIL']) : '';

// Loyalty Points Summary
$pointSql = "
SELECT
    ISNULL(SUM(POINT_ADDED), 0) AS POINTS_ADDED,
    ISNULL(SUM(POINT_DEDUCTED), 0) AS POINTS_DEDUCTED
FROM U_TBLLOYALTY_POINTS
WHERE POINT_MEMBER = ?
";

$pointStmt = sqlsrv_query($conn, $pointSql, [$row['CM_CODE']]);
$pointRow = sqlsrv_fetch_array($pointStmt, SQLSRV_FETCH_ASSOC);

$openingPoints = (float)($row['CM_OPENINGPOINTS'] ?? 0);
$transactionAdded = (float)($pointRow['POINTS_ADDED'] ?? 0);
$transactionDeducted = (float)($pointRow['POINTS_DEDUCTED'] ?? 0);

// Total Earned Points = Opening Points + Added Points
$row['POINTS_ADDED'] = $openingPoints + $transactionAdded;

$row['POINTS_DEDUCTED'] = $transactionDeducted;
$row['POINTS_AVAILABLE'] = (float)($row['CM_POINTS'] ?? 0);

// Optional: keep original values separately
$row['OPENING_POINTS'] = $openingPoints;
$row['TRANSACTION_POINTS_ADDED'] = $transactionAdded;

// Return clean response
echo json_encode([
    "success" => true,
    "customer" => $row
]);

exit;
?>