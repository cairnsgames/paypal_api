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
if (empty($body)) {
    http_response_code(400); // Bad Request
    die("No payload received.");
}

try {
    $webhookEvent = new WebhookEvent();
    $webhookEvent->fromJson($body);
} catch (Exception $e) {
    http_response_code(400); // Bad Request
    die("Invalid payload: " . $e->getMessage());
}

$eventType = $webhookEvent->getEventType();
$resource = $webhookEvent->getResource();

// Process the webhook event
switch ($eventType) {
    case 'PAYMENT.SALE.COMPLETED':
        // Payment completed successfully
        $saleId = $resource['id'];
        // Call the success function with saleId and webhook data
        success($saleId, json_encode($resource));
        break;

    case 'PAYMENT.SALE.DENIED':
        // Payment denied
        failed($saleId, json_encode($resource));
        break;

    // Handle other events as needed
    default:
        // Handle unknown event type
        break;
}

http_response_code(200); // Respond to PayPal with HTTP 200 OK
?>
