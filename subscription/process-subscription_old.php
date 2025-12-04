<?php
// subscription/process-subscription.php
session_start();
require_once '../config/database.php';
require_once '../config/stripe-config.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get and validate JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['payment_method_id'], $input['plan'], $input['billing'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

$paymentMethodId = $input['payment_method_id'];
$plan = $input['plan'];
$billing = $input['billing'];
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

try {
    $pdo = getDBConnection();
    
    // Get user info
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception('User not found');
    }
    
    // Get plan details
    $stmt = $pdo->prepare("SELECT * FROM subscription_plans WHERE tier_name = ?");
    $stmt->execute([$plan]);
    $planDetails = $stmt->fetch();
    
    if (!$planDetails) {
        throw new Exception('Invalid plan selected');
    }
    
    // Get the correct Stripe price ID
    $priceColumn = $billing == 'annual' ? 'stripe_annual_price_id' : 'stripe_monthly_price_id';
    $priceId = $planDetails[$priceColumn];
    
    if (empty($priceId)) {
        throw new Exception('Price ID not configured for this plan');
    }
    
    // Check if user already has a Stripe customer ID
    $stripeCustomerId = $user['stripe_customer_id'];
    
    if (empty($stripeCustomerId)) {
        // Create new Stripe customer
        $customer = \Stripe\Customer::create([
            'email' => $user['email'],
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'payment_method' => $paymentMethodId,
            'invoice_settings' => [
                'default_payment_method' => $paymentMethodId
            ]
        ]);
        $stripeCustomerId = $customer->id;
        
        // Save Stripe customer ID to database
        $stmt = $pdo->prepare("UPDATE users SET stripe_customer_id = ? WHERE id = ?");
        $stmt->execute([$stripeCustomerId, $userId]);
    } else {
        // Attach payment method to existing customer
        $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
        $paymentMethod->attach(['customer' => $stripeCustomerId]);
        
        // Set as default payment method
        \Stripe\Customer::update($stripeCustomerId, [
            'invoice_settings' => [
                'default_payment_method' => $paymentMethodId
            ]
        ]);
    }
    
    // Create subscription
    $subscription = \Stripe\Subscription::create([
        'customer' => $stripeCustomerId,
        'items' => [[
            'price' => $priceId,
        ]],
        'payment_behavior' => 'default_incomplete',
        'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
        'expand' => ['latest_invoice.payment_intent'],
        'trial_period_days' => TRIAL_PERIOD_DAYS,
        'metadata' => [
            'user_id' => $userId,
            'plan' => $plan,
            'billing' => $billing
        ]
    ]);
    
    // Update user subscription info in database
    $trialEndsAt = date('Y-m-d H:i:s', $subscription->trial_end);
    $currentPeriodEnd = date('Y-m-d H:i:s', $subscription->current_period_end);
    
    $stmt = $pdo->prepare("
        UPDATE users 
        SET 
            subscription_tier = ?,
            subscription_status = 'trialing',
            stripe_subscription_id = ?,
            trial_ends_at = ?,
            current_period_end = ?,
            updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([
        $plan,
        $subscription->id,
        $trialEndsAt,
        $currentPeriodEnd,
        $userId
    ]);
    
    // Record payment attempt
    $stmt = $pdo->prepare("
        INSERT INTO payments (
            user_id, 
            stripe_payment_intent_id, 
            amount,
            currency,
            status,
            plan_tier,
            billing_period,
            period_start,
            period_end,
            metadata
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $userId,
        $subscription->latest_invoice->payment_intent->id,
        $planDetails['monthly_price'],
        'USD',
        'pending',
        $plan,
        $billing,
        date('Y-m-d H:i:s'),
        $currentPeriodEnd,
        json_encode(['subscription_id' => $subscription->id])
    ]);
    
    // Return the client secret for 3D Secure if needed
    $response = [
        'subscription_id' => $subscription->id,
        'client_secret' => $subscription->latest_invoice->payment_intent->client_secret,
        'requires_action' => $subscription->latest_invoice->payment_intent->status === 'requires_action',
        'subscription_status' => $subscription->status
    ];
    
    // Update session with new tier
    $_SESSION['user_tier'] = $plan;
    
    echo json_encode($response);
    
} catch (\Stripe\Exception\CardException $e) {
    // Card was declined
    $error = $e->getError();
    http_response_code(402);
    echo json_encode([
        'error' => 'Payment failed: ' . ($error->message ?? 'Card declined'),
        'code' => $error->code ?? 'card_declined'
    ]);
} catch (\Stripe\Exception\RateLimitException $e) {
    // Too many requests
    http_response_code(429);
    echo json_encode(['error' => 'Too many requests. Please try again later.']);
} catch (\Stripe\Exception\InvalidRequestException $e) {
    // Invalid parameters
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request: ' . $e->getMessage()]);
} catch (\Stripe\Exception\AuthenticationException $e) {
    // Authentication with Stripe failed
    http_response_code(500);
    echo json_encode(['error' => 'Payment system error. Please contact support.']);
} catch (\Stripe\Exception\ApiConnectionException $e) {
    // Network communication failed
    http_response_code(503);
    echo json_encode(['error' => 'Network error. Please try again.']);
} catch (\Stripe\Exception\ApiErrorException $e) {
    // Generic Stripe error
    http_response_code(500);
    echo json_encode(['error' => 'Payment processing error: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Application error
    http_response_code(500);
    echo json_encode(['error' => 'Application error: ' . $e->getMessage()]);
}
?>