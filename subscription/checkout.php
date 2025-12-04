<?php
session_start();
require_once '../config/database.php';
require_once '../config/stripe-config.php';
require_once '../includes/auth-functions.php';

requireLogin();

$plan = $_GET['plan'] ?? 'pro';
$billing = $_GET['billing'] ?? 'monthly'; // monthly or annual

// Validate plan
$validPlans = ['standard', 'pro', 'premium'];
if(!in_array($plan, $validPlans)) {
    header('Location: ../pricing.php');
    exit();
}

// Get user info
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get plan details
$stmt = $pdo->prepare("SELECT * FROM subscription_plans WHERE tier_name = ?");
$stmt->execute([$plan]);
$planDetails = $stmt->fetch();

if(!$planDetails) {
    die("Plan not found. Please contact support.");
}

// Calculate price
$price = $billing == 'annual' ? $planDetails['annual_price'] : $planDetails['monthly_price'];
$priceIdColumn = $billing == 'annual' ? 'stripe_annual_price_id' : 'stripe_monthly_price_id';
$priceId = $planDetails[$priceIdColumn];

if(empty($priceId)) {
    die("Price not configured for this plan. Please contact support.");
}

// Map tier name to display name
$display_plan = match($plan) {
    'standard' => 'Standard',
    'pro' => 'Pro',
    'premium' => 'Premium',
    default => ucfirst($plan)
};
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - AccuTrading Signals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container py-5" style="padding-top: 100px !important;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-dark border-nasdaq-blue">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Subscribe to <?php echo $display_plan; ?></h2>
                        
                        <div class="text-center mb-5">
                            <h3 class="display-4 fw-bold text-nasdaq-blue">$<?php echo number_format($price, 2); ?></h3>
                            <p class="text-muted"><?php echo $billing == 'annual' ? 'per year' : 'per month'; ?></p>
                            <a href="?plan=<?php echo $plan; ?>&billing=<?php echo $billing == 'monthly' ? 'annual' : 'monthly'; ?>" 
                               class="btn btn-outline-light btn-sm">
                                Switch to <?php echo $billing == 'monthly' ? 'annual' : 'monthly'; ?> billing
                            </a>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Plan Features</h5>
                                <ul class="list-unstyled">
                                    <?php
                                    $features = json_decode($planDetails['features'], true);
                                    foreach($features as $feature):
                                    ?>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle-fill text-nasdaq-green me-2"></i>
                                        <?php echo htmlspecialchars($feature); ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>Payment Details</h5>
                                <form id="payment-form">
                                    <div class="mb-3">
                                        <label for="card-holder-name" class="form-label">Cardholder Name</label>
                                        <input type="text" class="form-control" id="card-holder-name" 
                                               value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="card-element" class="form-label">Credit Card</label>
                                        <div id="card-element" class="form-control py-2"></div>
                                        <div id="card-errors" class="text-danger mt-2"></div>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="save-card" checked>
                                        <label class="form-check-label" for="save-card">
                                            Save card for future payments
                                        </label>
                                    </div>
                                    
                                    <div class="alert alert-info bg-black border-nasdaq-blue">
                                        <i class="bi bi-info-circle me-2"></i>
                                        You'll be charged <strong>$<?php echo number_format($price, 2); ?></strong> immediately.
                                        <?php echo $billing == 'annual' ? 'Renews annually.' : 'Renews monthly.'; ?>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button id="submit-button" class="btn btn-nasdaq-blue btn-lg">
                                            <span id="button-text">
                                                <i class="bi bi-credit-card me-2"></i>Pay $<?php echo number_format($price, 2); ?> Now
                                            </span>
                                            <span id="button-spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="text-center text-muted small">
                            <p>By subscribing, you agree to our <a href="../terms.php" class="text-nasdaq-blue">Terms of Service</a> and <a href="../privacy.php" class="text-nasdaq-blue">Privacy Policy</a>.</p>
                            <p>30-day money-back guarantee. Cancel anytime.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    const stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');
    const elements = stripe.elements();
    const cardElement = elements.create('card');
    cardElement.mount('#card-element');
    
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const buttonSpinner = document.getElementById('button-spinner');
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        submitButton.disabled = true;
        buttonText.classList.add('d-none');
        buttonSpinner.classList.remove('d-none');
        
        const { paymentMethod, error } = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
            billing_details: {
                name: document.getElementById('card-holder-name').value,
                email: '<?php echo $user["email"]; ?>'
            }
        });
        
        if (error) {
            document.getElementById('card-errors').textContent = error.message;
            submitButton.disabled = false;
            buttonText.classList.remove('d-none');
            buttonSpinner.classList.add('d-none');
        } else {
            // Send paymentMethod.id to your server
            fetch('process-subscription.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    payment_method_id: paymentMethod.id,
                    plan: '<?php echo $plan; ?>',
                    billing: '<?php echo $billing; ?>',
                    price_id: '<?php echo $priceId; ?>',
                    no_trial: true
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('card-errors').textContent = data.error;
                    submitButton.disabled = false;
                    buttonText.classList.remove('d-none');
                    buttonSpinner.classList.add('d-none');
                } else if (data.requires_action) {
                    // Handle 3D Secure authentication
                    stripe.confirmCardPayment(data.client_secret)
                        .then(result => {
                            if (result.error) {
                                document.getElementById('card-errors').textContent = result.error.message;
                                submitButton.disabled = false;
                                buttonText.classList.remove('d-none');
                                buttonSpinner.classList.add('d-none');
                            } else {
                                window.location.href = 'success.php?subscription_id=' + data.subscription_id;
                            }
                        });
                } else {
                    window.location.href = 'success.php?subscription_id=' + data.subscription_id;
                }
            })
            .catch(error => {
                document.getElementById('card-errors').textContent = 'Network error. Please try again.';
                submitButton.disabled = false;
                buttonText.classList.remove('d-none');
                buttonSpinner.classList.add('d-none');
            });
        }
    });
    </script>
</body>
</html>