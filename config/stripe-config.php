<?php
// config/stripe-config.php
// Stripe Configuration - SANDBOX ENVIRONMENT

// Load Composer dependencies if using Stripe PHP library
// require_once __DIR__ . '/../vendor/autoload.php';

// IMPORTANT: Use your sandbox/test keys here
// Get these from: https://dashboard.stripe.com/test/apikeys

// Test Publishable Key (for client-side)
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51SaM5cBSIo2mkGlffLpilJsMntrmLpjwkf3Eo5BvBtQIclatzbepy58CFmKjCpnQ3XSJLKf3G7Bt0U4GjSoAUGPL00larzYu8j');

// Test Secret Key (for server-side) - YOU NEED TO ROTATE THIS KEY!
define('STRIPE_SECRET_KEY', 'sk_test_51SaM5cBSIo2mkGlffLpilJsMntrmLpjwkf3Eo5BvBtQIclatzbepy58CFmKjCpnQ3XSJLKf3G7Bt0U4GjSoAUGPL00larzYu8j');

// Webhook secret for verifying webhook signatures
// Get from: Stripe Dashboard → Developers → Webhooks → Find your webhook → Reveal
define('STRIPE_WEBHOOK_SECRET', 'whsec_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

// Set timezone
date_default_timezone_set('America/New_York');

// Initialize Stripe with your secret key
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
\Stripe\Stripe::setApiVersion('2023-10-16'); // Use latest stable version

// Subscription plans mapping - Updated to match your database
$stripePlans = [
    'standard' => [
        'monthly' => 'price_standard_monthly',  // Create these in Stripe Dashboard
        'annual' => 'price_standard_annual'
    ],
    'pro' => [
        'monthly' => 'price_pro_monthly',
        'annual' => 'price_pro_annual'
    ],
    'premium' => [
        'monthly' => 'price_premium_monthly',
        'annual' => 'price_premium_annual'
    ]
];

// Trial period in days
define('TRIAL_PERIOD_DAYS', 14);

// Create Stripe customer
function createStripeCustomer($email, $name, $metadata = []) {
    try {
        $customerData = [
            'email' => $email,
            'name' => $name,
            'metadata' => array_merge([
                'signup_date' => date('Y-m-d'),
                'platform' => 'EventFlow'
            ], $metadata)
        ];
        
        return \Stripe\Customer::create($customerData);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        error_log("Stripe Customer Creation Error: " . $e->getMessage());
        return false;
    }
}

// Create subscription with trial
function createSubscription($customerId, $priceId, $paymentMethodId = null, $trialDays = TRIAL_PERIOD_DAYS) {
    try {
        $subscriptionData = [
            'customer' => $customerId,
            'items' => [[
                'price' => $priceId,
            ]],
            'payment_behavior' => 'default_incomplete',
            'expand' => ['latest_invoice.payment_intent'],
            'trial_period_days' => $trialDays
        ];
        
        // Attach payment method if provided
        if ($paymentMethodId) {
            $subscriptionData['default_payment_method'] = $paymentMethodId;
        }
        
        return \Stripe\Subscription::create($subscriptionData);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        error_log("Stripe Subscription Error: " . $e->getMessage());
        return false;
    }
}

// Verify webhook signature
function verifyStripeWebhook() {
    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
    
    try {
        return \Stripe\Webhook::constructEvent(
            $payload, $sig_header, STRIPE_WEBHOOK_SECRET
        );
    } catch(\UnexpectedValueException $e) {
        // Invalid payload
        http_response_code(400);
        exit();
    } catch(\Stripe\Exception\SignatureVerificationException $e) {
        // Invalid signature
        http_response_code(400);
        exit();
    }
}
?>