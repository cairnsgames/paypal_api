<?php
// /paypal/webhook.php

require 'vendor/autoload.php'; // Load Composer dependencies

use PayPal\Api\WebhookEvent;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

require_once 'config.php'; // Load PayPal API credentials
require_once 'settings.php'; // Load PayPal API settings

$clientId = getPropertyValue('b0181e17-e5c6-11ee-bb99-1a220d8ac2c9', 'paypal_clientid');
$secret = getPropertyValue('b0181e17-e5c6-11ee-bb99-1a220d8ac2c9', 'paypal_secret');

// PayPal API Context setup
$apiContext = new ApiContext(
    new OAuthTokenCredential(
        $clientId,     // Client ID
        $secret  // Client Secret
    )
);
$apiContext->setConfig([
    'mode' => 'sandbox', // Change to 'live' in production
]);

// Retrieve webhook payload
$body = file_get_contents('php://input');
$webhookEvent = new WebhookEvent();
$webhookEvent->fromJson($body);

$eventType = $webhookEvent->getEventType();
$resource = $webhookEvent->getResource();

// Function to handle successful payments
function success($paymentid, $webhookData = null) {
    $conn = getDbConnection();

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE payment_progress SET status = 'completed', webhook_data = ?, updated_at = NOW() WHERE payment_id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param('ss', $webhookData, $paymentid);

    if ($stmt->execute()) {
        // echo "Payment marked as completed.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

switch ($eventType) {
    case 'PAYMENT.SALE.COMPLETED':
        // Payment completed successfully
        $saleId = $resource['id'];
        // Call the success function with saleId and webhook data
        success($saleId, json_encode($resource));
        break;

    case 'PAYMENT.SALE.DENIED':
        // Payment denied
        break;

    // Handle other events as needed
    default:
        // Handle unknown event type
        break;
}

http_response_code(200); // Respond to PayPal with HTTP 200 OK
?>
