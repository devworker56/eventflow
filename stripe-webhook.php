<?php
// stripe-webhook.php
require_once 'config/database.php';
require_once 'config/stripe-config.php';

// Get webhook event
$payload = @file_get_contents('php://input');
$event = null;

try {
    // Verify webhook signature
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
    $event = \Stripe\Webhook::constructEvent(
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

// Handle the event
$pdo = getDBConnection();

switch ($event->type) {
    case 'customer.subscription.created':
    case 'customer.subscription.updated':
        $subscription = $event->data->object;
        handleSubscriptionUpdate($pdo, $subscription);
        break;
        
    case 'customer.subscription.deleted':
        $subscription = $event->data->object;
        handleSubscriptionCancel($pdo, $subscription);
        break;
        
    case 'invoice.payment_succeeded':
        $invoice = $event->data->object;
        handlePaymentSuccess($pdo, $invoice);
        break;
        
    case 'invoice.payment_failed':
        $invoice = $event->data->object;
        handlePaymentFailed($pdo, $invoice);
        break;
        
    case 'checkout.session.completed':
        // If using Stripe Checkout instead of Elements
        $session = $event->data->object;
        handleCheckoutComplete($pdo, $session);
        break;
}

http_response_code(200);

// Helper functions
function handleSubscriptionUpdate($pdo, $subscription) {
    $customerId = $subscription->customer;
    $status = $subscription->status;
    $trialEnd = $subscription->trial_end;
    $currentPeriodEnd = $subscription->current_period_end;
    
    // Get plan from metadata
    $plan = $subscription->metadata->plan ?? 'standard';
    
    $stmt = $pdo->prepare("
        UPDATE users 
        SET 
            subscription_tier = ?,
            subscription_status = ?,
            trial_ends_at = ?,
            current_period_end = ?,
            updated_at = NOW()
        WHERE stripe_customer_id = ?
    ");
    
    $trialEndsAt = $trialEnd ? date('Y-m-d H:i:s', $trialEnd) : null;
    $periodEndsAt = date('Y-m-d H:i:s', $currentPeriodEnd);
    
    $stmt->execute([$plan, $status, $trialEndsAt, $periodEndsAt, $customerId]);
}

function handleSubscriptionCancel($pdo, $subscription) {
    $customerId = $subscription->customer;
    
    $stmt = $pdo->prepare("
        UPDATE users 
        SET 
            subscription_status = 'canceled',
            current_period_end = NULL,
            updated_at = NOW()
        WHERE stripe_customer_id = ?
    ");
    $stmt->execute([$customerId]);
}

function handlePaymentSuccess($pdo, $invoice) {
    $paymentIntent = $invoice->payment_intent;
    $subscriptionId = $invoice->subscription;
    $amount = $invoice->amount_paid / 100; // Convert from cents
    
    // Get subscription to find user
    try {
        $subscription = \Stripe\Subscription::retrieve($subscriptionId);
        $customerId = $subscription->customer;
        
        // Find user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE stripe_customer_id = ?");
        $stmt->execute([$customerId]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Update payment record
            $stmt = $pdo->prepare("
                UPDATE payments 
                SET 
                    status = 'succeeded',
                    stripe_charge_id = ?,
                    updated_at = NOW()
                WHERE stripe_payment_intent_id = ?
            ");
            $stmt->execute([$invoice->charge, $paymentIntent]);
            
            // Update user subscription status
            $stmt = $pdo->prepare("
                UPDATE users 
                SET 
                    subscription_status = 'active',
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$user['id']]);
        }
    } catch (Exception $e) {
        error_log("Payment success handling error: " . $e->getMessage());
    }
}

function handlePaymentFailed($pdo, $invoice) {
    $paymentIntent = $invoice->payment_intent;
    
    // Update payment record
    $stmt = $pdo->prepare("
        UPDATE payments 
        SET 
            status = 'failed',
            updated_at = NOW()
        WHERE stripe_payment_intent_id = ?
    ");
    $stmt->execute([$paymentIntent]);
    
    // Find and notify user
    $stmt = $pdo->prepare("
        SELECT u.email 
        FROM payments p
        JOIN users u ON p.user_id = u.id
        WHERE p.stripe_payment_intent_id = ?
    ");
    $stmt->execute([$paymentIntent]);
    $payment = $stmt->fetch();
    
    if ($payment) {
        // Send email notification about payment failure
        // mail($payment['email'], 'Payment Failed - EventFlow', 'Your payment failed...');
    }
}
?>