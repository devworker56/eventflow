<?php
// config/stripe-config.php
// Stripe Configuration using Environment Variables

// Load Composer autoloader for Stripe PHP library and dotenv
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Get Stripe keys from environment with validation
$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? '';
$stripePublishableKey = $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '';
$stripeWebhookSecret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '';

// Validate that required keys are present
if (empty($stripeSecretKey)) {
    throw new Exception('STRIPE_SECRET_KEY is not set in .env file');
}

if (empty($stripePublishableKey)) {
    throw new Exception('STRIPE_PUBLISHABLE_KEY is not set in .env file');
}

// Set Stripe API configuration
\Stripe\Stripe::setApiKey($stripeSecretKey);
\Stripe\Stripe::setApiVersion('2023-10-16'); // Use latest stable version

// Define constants for use throughout the application
define('STRIPE_PUBLISHABLE_KEY', $stripePublishableKey);
define('STRIPE_SECRET_KEY', $stripeSecretKey);
define('STRIPE_WEBHOOK_SECRET', $stripeWebhookSecret);

// Set timezone from environment or default
$timezone = $_ENV['APP_TIMEZONE'] ?? 'America/New_York';
date_default_timezone_set($timezone);

// Subscription plans mapping - Updated to match your database schema
$stripePlans = [
    'standard' => [
        'monthly' => 'price_standard_monthly',
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

/**
 * Create a Stripe customer
 * 
 * @param string $email Customer email
 * @param string $name Customer name
 * @param array $metadata Additional metadata
 * @return \Stripe\Customer|false
 */
function createStripeCustomer($email, $name, $metadata = []) {
    try {
        $customerData = [
            'email' => $email,
            'name' => $name,
            'metadata' => array_merge([
                'signup_date' => date('Y-m-d'),
                'platform' => 'EventFlow',
                'app_env' => $_ENV['APP_ENV'] ?? 'production'
            ], $metadata)
        ];
        
        return \Stripe\Customer::create($customerData);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        error_log("Stripe Customer Creation Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Create a subscription with trial period
 * 
 * @param string $customerId Stripe customer ID
 * @param string $priceId Stripe price ID
 * @param string|null $paymentMethodId Optional payment method ID
 * @param int $trialDays Trial period in days
 * @return \Stripe\Subscription|false
 */
function createSubscription($customerId, $priceId, $paymentMethodId = null, $trialDays = TRIAL_PERIOD_DAYS) {
    try {
        $subscriptionData = [
            'customer' => $customerId,
            'items' => [[
                'price' => $priceId,
            ]],
            'payment_behavior' => 'default_incomplete',
            'expand' => ['latest_invoice.payment_intent'],
            'trial_period_days' => $trialDays,
            'metadata' => [
                'app_env' => $_ENV['APP_ENV'] ?? 'production'
            ]
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

/**
 * Verify Stripe webhook signature
 * 
 * @return \Stripe\Event
 * @throws \UnexpectedValueException|\Stripe\Exception\SignatureVerificationException
 */
function verifyStripeWebhook() {
    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
    
    if (empty(STRIPE_WEBHOOK_SECRET)) {
        throw new Exception('STRIPE_WEBHOOK_SECRET is not configured');
    }
    
    return \Stripe\Webhook::constructEvent(
        $payload, $sig_header, STRIPE_WEBHOOK_SECRET
    );
}

/**
 * Get the correct Stripe price ID for a plan and billing period
 * 
 * @param string $plan Plan name (standard/pro/premium)
 * @param string $billing Billing period (monthly/annual)
 * @return string|null Price ID or null if not found
 */
function getStripePriceId($plan, $billing) {
    global $stripePlans;
    
    if (!isset($stripePlans[$plan])) {
        return null;
    }
    
    return $stripePlans[$plan][$billing] ?? null;
}

/**
 * Check if running in test mode
 * 
 * @return bool
 */
function isStripeTestMode() {
    return strpos(STRIPE_SECRET_KEY, 'sk_test_') === 0;
}

// Log Stripe mode for debugging
if ($_ENV['APP_DEBUG'] ?? false) {
    error_log('Stripe initialized in ' . (isStripeTestMode() ? 'TEST' : 'LIVE') . ' mode');
}
?>