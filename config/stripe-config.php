<?php
// Stripe Configuration
require_once __DIR__ . '/../vendor/autoload.php'; // Composer autoload

\Stripe\Stripe::setApiKey('your_stripe_secret_key_here');

// Subscription plans mapping
$stripePlans = [
    'explorer' => 'price_explorer_monthly',
    'professional' => 'price_professional_monthly', 
    'institutional' => 'price_institutional_monthly'
];

// Create Stripe customer
function createStripeCustomer($email, $name) {
    try {
        return \Stripe\Customer::create([
            'email' => $email,
            'name' => $name,
            'metadata' => [
                'signup_date' => date('Y-m-d')
            ]
        ]);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        error_log("Stripe Customer Creation Error: " . $e->getMessage());
        return false;
    }
}

// Create subscription
function createSubscription($customerId, $priceId, $trialDays = 14) {
    try {
        return \Stripe\Subscription::create([
            'customer' => $customerId,
            'items' => [[
                'price' => $priceId,
            ]],
            'payment_behavior' => 'default_incomplete',
            'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
            'expand' => ['latest_invoice.payment_intent'],
            'trial_period_days' => $trialDays
        ]);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        error_log("Stripe Subscription Error: " . $e->getMessage());
        return false;
    }
}

// Webhook handler
function handleStripeWebhook($payload, $sigHeader) {
    $endpoint_secret = 'your_webhook_secret';
    
    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sigHeader, $endpoint_secret
        );
    } catch(\UnexpectedValueException $e) {
        http_response_code(400);
        exit();
    } catch(\Stripe\Exception\SignatureVerificationException $e) {
        http_response_code(400);
        exit();
    }
    
    // Handle different event types
    switch ($event->type) {
        case 'customer.subscription.created':
        case 'customer.subscription.updated':
            handleSubscriptionUpdate($event->data->object);
            break;
        case 'customer.subscription.deleted':
            handleSubscriptionCancel($event->data->object);
            break;
        case 'invoice.payment_succeeded':
            handlePaymentSuccess($event->data->object);
            break;
        case 'invoice.payment_failed':
            handlePaymentFailed($event->data->object);
            break;
    }
    
    return true;
}
?>