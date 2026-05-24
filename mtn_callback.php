<?php

require_once '../../includes/auth_check.php';

// ===============================
// 1. SESSION FIRST
// ===============================
session_start();

// ===============================
// 2. READ PAYLOAD SAFE
// ===============================
$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);

// Si payload invalide
if (!is_array($payload)) {
    http_response_code(400);
    exit("Invalid payload");
}

// ===============================
// 3. LOG CALLBACK
// ===============================
$logDir = __DIR__ . '/../../storage/logs';

if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

file_put_contents(
    $logDir . '/mtn_callback.log',
    "[" . date('Y-m-d H:i:s') . "]\n" . $raw . "\n\n",
    FILE_APPEND
);

// ===============================
// 4. EXTRACT DATA (SAFE)
// ===============================
$orderId = $payload['externalId']
    ?? $payload['MerchantReference']
    ?? null;

$transactionId = $payload['financialTransactionId']
    ?? $payload['TransactionID']
    ?? null;

$status = $payload['status']
    ?? $payload['ResultCode']
    ?? null;

// ===============================
// 5. STORE SESSION
// ===============================
$_SESSION['mtn_transaction_id'] = $transactionId;
$_SESSION['mtn_order_ref'] = $orderId;
$_SESSION['mtn_result_code'] = $status;

// ===============================
// 6. CHECK SUCCESS
// ===============================
$isSuccess =
    $status === 'SUCCESSFUL' ||
    $status === 'SUCCESS' ||
    $status === '0';

// ===============================
// 7. PREVENT MULTIPLE CALLBACK ISSUES
// ===============================
if (isset($_SESSION['mtn_processed']) && $_SESSION['mtn_processed'] === $transactionId) {
    exit("Already processed");
}

$_SESSION['mtn_processed'] = $transactionId;

// ===============================
// 8. REDIRECT
// ===============================
if ($isSuccess) {
    header('Location: success.php');
    exit;
}

header('Location: processing.php');
exit;