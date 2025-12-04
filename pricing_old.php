<?php
session_start();
require_once 'config/database.php';

$pdo = getDBConnection();
$stmt = $pdo->query("SELECT * FROM subscription_plans ORDER BY monthly_price");
$plans = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing - AccuTrading Signals</title>
    <meta name="description" content="Simple, transparent pricing for derivatives intelligence. Start with a 14-day free trial.">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Hero Section -->
    <section class="py-5" style="padding-top: 100px !important; background: linear-gradient(135deg, #000 0%, var(--nasdaq-dark-gray) 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-4 fw-bold mb-4">
                        Simple, Transparent <span class="text-nasdaq-blue">Pricing</span>
                    </h1>
                    <p class="lead mb-4">
                        Start with a 14-day free trial. No credit card required. Cancel anytime.
                    </p>
                    <div class="d-inline-flex align-items-center bg-dark rounded-pill p-1 mb-4">
                        <button id="monthlyBtn" class="btn btn-nasdaq-blue rounded-pill px-4">Monthly</button>
                        <button id="annualBtn" class="btn btn-outline-light rounded-pill px-4">Annual (Save 17%)</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Cards -->
    <section class="py-5 bg-dark">
        <div class="container">
            <div class="row justify-content-center">
                <?php foreach($plans as $plan): 
                    // Map database tier names to display names
                    $display_name = match($plan['tier_name']) {
                        'explorer' => 'Standard',
                        'professional' => 'Pro',
                        'institutional' => 'Premium',
                        default => ucfirst($plan['tier_name'])
                    };
                    
                    // Determine styling
                    $is_popular = $plan['tier_name'] == 'professional'; // Pro is most popular
                    $border_class = $is_popular ? 'nasdaq-blue border-3' : 'secondary';
                    $btn_class = match($plan['tier_name']) {
                        'professional' => 'nasdaq-blue',      // Pro gets primary button
                        'institutional' => 'outline-nasdaq-blue', // Premium gets outline blue
                        default => 'outline-light'            // Standard gets basic outline
                    };
                ?>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 border-<?php echo $border_class; ?>">
                        <?php if($is_popular): ?>
                        <div class="card-header bg-nasdaq-blue text-center py-3">
                            <span class="badge bg-dark">MOST POPULAR</span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="card-body p-4">
                            <h3 class="card-title text-center mb-3"><?php echo $display_name; ?></h3>
                            
                            <div class="text-center mb-4">
                                <div class="monthly-price">
                                    <span class="display-4 fw-bold">$<?php echo $plan['monthly_price']; ?></span>
                                    <span class="text-muted">/month</span>
                                </div>
                                <div class="annual-price d-none">
                                    <span class="display-4 fw-bold">$<?php echo $plan['annual_price']; ?></span>
                                    <span class="text-muted">/year</span>
                                    <div class="text-success small">Save $<?php echo number_format(($plan['monthly_price'] * 12) - $plan['annual_price'], 2); ?></div>
                                </div>
                            </div>
                            
                            <ul class="list-unstyled mb-4">
                                <?php
                                $features = json_decode($plan['features'], true);
                                foreach($features as $feature):
                                ?>
                                <li class="mb-3">
                                    <i class="bi bi-check-circle-fill text-nasdaq-green me-2"></i>
                                    <?php echo htmlspecialchars($feature); ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            
                            <div class="text-center mt-auto">
                                <a href="subscription/checkout.php?plan=<?php echo $plan['tier_name']; ?>" 
                                   class="btn btn-<?php echo $btn_class; ?> w-100 py-3">
                                    Start Free Trial
                                </a>
                                <p class="text-muted small mt-2 mb-0">14-day free trial • No credit card required</p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5 bg-black">
        <div class="container">
            <h2 class="text-center display-5 fw-bold mb-5">Frequently Asked Questions</h2>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item bg-dark border-nasdaq-blue">
                            <h2 class="accordion-header">
                                <button class="accordion-button bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How does the free trial work?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>You get full access to the <strong>Standard tier</strong> features for 14 days. No credit card is required to start the trial. At the end of the trial, you can choose to upgrade to a paid plan or your account will be paused.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item bg-dark border-nasdaq-blue mt-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Can I cancel anytime?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Yes, you can cancel your subscription at any time. If you cancel, you'll continue to have access until the end of your current billing period.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item bg-dark border-nasdaq-blue mt-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    What payment methods do you accept?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>We accept all major credit cards (Visa, Mastercard, American Express) through our secure Stripe payment processor. We also support ACH transfers for <strong>Premium</strong> customers.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item bg-dark border-nasdaq-blue mt-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Do you offer refunds?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Yes, we offer a 30-day money-back guarantee. If you're not satisfied with our service, contact us within 30 days of your first payment for a full refund.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item bg-dark border-nasdaq-blue mt-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    Is there a limit on API calls?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Yes, each tier has a monthly API call limit: <strong>Standard (1,000)</strong>, <strong>Pro (10,000)</strong>, and <strong>Premium (100,000)</strong>. Additional API calls can be purchased if needed.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enterprise Section -->
    <section class="py-5 bg-dark">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="display-5 fw-bold mb-4">Need Enterprise Solutions?</h2>
                    <p class="lead mb-4">
                        For hedge funds, proprietary trading firms, and financial institutions requiring 
                        custom solutions, dedicated infrastructure, or white-label platforms.
                    </p>
                    <a href="contact.php" class="btn btn-outline-light btn-lg px-5">
                        <i class="bi bi-envelope me-2"></i>Contact Sales
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    
    <script>
    // Toggle monthly/annual pricing
    document.getElementById('monthlyBtn').addEventListener('click', function() {
        this.classList.add('btn-nasdaq-blue');
        this.classList.remove('btn-outline-light');
        document.getElementById('annualBtn').classList.remove('btn-nasdaq-blue');
        document.getElementById('annualBtn').classList.add('btn-outline-light');
        
        // Show monthly prices
        document.querySelectorAll('.monthly-price').forEach(el => el.classList.remove('d-none'));
        document.querySelectorAll('.annual-price').forEach(el => el.classList.add('d-none'));
    });
    
    document.getElementById('annualBtn').addEventListener('click', function() {
        this.classList.add('btn-nasdaq-blue');
        this.classList.remove('btn-outline-light');
        document.getElementById('monthlyBtn').classList.remove('btn-nasdaq-blue');
        document.getElementById('monthlyBtn').classList.add('btn-outline-light');
        
        // Show annual prices
        document.querySelectorAll('.monthly-price').forEach(el => el.classList.add('d-none'));
        document.querySelectorAll('.annual-price').forEach(el => el.classList.remove('d-none'));
    });
    </script>
</body>
</html>