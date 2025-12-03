<?php
session_start();
require_once 'config/database.php'; // Only include if we need database access
require_once 'includes/auth-functions.php'; // Only include if we need auth functions

$page_title = "Features - EventFlow Institutional";
$page_description = "See how EventFlow uses options, futures, and fixed income data to generate superior equity predictions.";

// Check if user is logged in and get their tier if available
$userLoggedIn = false;
$userTier = null;
$userEmail = null;

if(isset($_SESSION['user_id'])) {
    $userLoggedIn = true;
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT email, subscription_tier FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if($user) {
            $userTier = $user['subscription_tier'];
            $userEmail = $user['email'];
        }
    } catch(Exception $e) {
        // Silently fail - this is a public page
    }
}

// Define which features are available at each tier
$tierFeatures = [
    'standard' => [
        'title' => 'Standard',
        'color' => 'text-muted',
        'badge' => 'bg-secondary',
        'features' => [
            'Basic Event Calendar',
            'Delayed Signals (30 min)',
            'Options IV Analysis (10 stocks)',
            'Basic Sentiment Analysis',
            'Email Alerts',
            '1 Watchlist',
            'Community Access'
        ]
    ],
    'pro' => [
        'title' => 'Pro',
        'color' => 'text-nasdaq-blue',
        'badge' => 'bg-nasdaq-blue',
        'features' => [
            'Real-time Signal Feed',
            'Advanced Options Scanner',
            'Futures Correlation Analysis',
            'Fixed Income Alpha Signals',
            'SMS/Webhook Alerts',
            'Backtesting Suite',
            'API Access (10K/mo)',
            '5 Watchlists',
            'Priority Support'
        ]
    ],
    'premium' => [
        'title' => 'Premium',
        'color' => 'text-nasdaq-green',
        'badge' => 'bg-nasdaq-green',
        'features' => [
            'White-label Solutions',
            'Unlimited API Access',
            'Custom Model Training',
            'Dedicated Infrastructure',
            'Historical Data Exports',
            'Account Manager',
            'Custom Development',
            'Unlimited Watchlists',
            '24/7 Premium Support'
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Hero Section with User Context -->
    <section class="py-5" style="padding-top: 100px !important; background: linear-gradient(135deg, #000 0%, var(--nasdaq-dark-gray) 100%);">
        <div class="container">
            <?php if($userLoggedIn && $userTier): ?>
            <!-- Logged-in User Welcome -->
            <div class="alert alert-dark border-nasdaq-blue mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-person-check text-nasdaq-blue me-2"></i>
                        Welcome back, <?php echo htmlspecialchars($userEmail); ?>! 
                        You're currently on the <span class="badge <?php echo $tierFeatures[$userTier]['badge']; ?> ms-1"><?php echo ucfirst($userTier); ?></span> plan.
                    </div>
                    <div>
                        <?php if($userTier !== 'premium'): ?>
                        <a href="subscription/" class="btn btn-sm btn-outline-nasdaq-blue">
                            <i class="bi bi-arrow-up-circle me-1"></i> Upgrade Plan
                        </a>
                        <?php endif; ?>
                        <a href="dashboard/" class="btn btn-sm btn-nasdaq-blue ms-2">
                            <i class="bi bi-speedometer2 me-1"></i> Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">
                        Transform Derivatives Data into <span class="text-nasdaq-blue">Equity Alpha</span>
                    </h1>
                    <p class="lead mb-4">
                        Traditional stock analysis is reactive. Our platform analyzes options, futures, and fixed income markets 
                        to predict stock movements <strong>3-5 days before</strong> they happen.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#how-it-works" class="btn btn-nasdaq-blue btn-lg px-4">
                            <i class="bi bi-play-circle me-2"></i>See How It Works
                        </a>
                        <?php if(!$userLoggedIn): ?>
                        <a href="register.php" class="btn btn-outline-light btn-lg px-4">
                            <i class="bi bi-lightning-charge me-2"></i>Start Free Trial
                        </a>
                        <?php else: ?>
                        <a href="#pricing" class="btn btn-outline-light btn-lg px-4">
                            <i class="bi bi-credit-card me-2"></i>Compare Plans
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card bg-dark border-nasdaq-blue">
                        <div class="card-body">
                            <h5 class="card-title text-nasdaq-blue">Backtested Results</h5>
                            <div class="row text-center mt-4">
                                <div class="col-4">
                                    <div class="display-6 fw-bold text-nasdaq-green">+42%</div>
                                    <small>vs. S&P 500</small>
                                </div>
                                <div class="col-4">
                                    <div class="display-6 fw-bold text-nasdaq-green">68%</div>
                                    <small>Win Rate</small>
                                </div>
                                <div class="col-4">
                                    <div class="display-6 fw-bold text-nasdaq-green">2.4:1</div>
                                    <small>Risk/Reward</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-5 bg-dark" id="how-it-works">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">How Derivatives Predict Stocks</h2>
                <p class="lead text-muted">Smart money moves in derivatives first. We track those moves.</p>
            </div>
            
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card h-100 border-nasdaq-blue bg-black">
                        <div class="card-body text-center p-4">
                            <div class="mb-4">
                                <div class="icon-circle bg-nasdaq-blue">
                                    <i class="bi bi-activity text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h4 class="card-title mb-3">Options Intelligence</h4>
                            <p class="card-text">
                                Options traders price in earnings surprises, M&A rumors, and product launches 
                                <strong>days before</strong> stock traders react.
                            </p>
                            <div class="mt-4">
                                <h6 class="text-nasdaq-blue">Key Signals:</h6>
                                <ul class="list-unstyled text-start">
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Implied Volatility Surfaces</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Put/Call Ratio Extremes</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Skew & Smile Analysis</li>
                                    <li><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Unusual Options Activity</li>
                                </ul>
                            </div>
                            <?php if($userLoggedIn): ?>
                            <div class="mt-4">
                                <a href="dashboard/?module=options" class="btn btn-sm btn-outline-nasdaq-blue w-100">
                                    <?php if($userTier === 'standard'): ?>
                                    <i class="bi bi-lock me-1"></i> Upgrade for Options Intelligence
                                    <?php else: ?>
                                    <i class="bi bi-arrow-right me-1"></i> Open Options Module
                                    <?php endif; ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 border-nasdaq-green bg-black">
                        <div class="card-body text-center p-4">
                            <div class="mb-4">
                                <div class="icon-circle bg-nasdaq-green">
                                    <i class="bi bi-currency-exchange text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h4 class="card-title mb-3">Futures & FX Signals</h4>
                            <p class="card-text">
                                Futures term structure predicts <strong>sector rotations</strong> and <strong>market regimes</strong> 
                                that determine which stocks will outperform.
                            </p>
                            <div class="mt-4">
                                <h6 class="text-nasdaq-green">Key Signals:</h6>
                                <ul class="list-unstyled text-start">
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>VIX Futures Curve</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Commodity Term Structure</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Currency Futures Positioning</li>
                                    <li><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Index Futures Roll</li>
                                </ul>
                            </div>
                            <?php if($userLoggedIn): ?>
                            <div class="mt-4">
                                <a href="dashboard/?module=futures" class="btn btn-sm btn-outline-nasdaq-green w-100">
                                    <?php if($userTier === 'standard'): ?>
                                    <i class="bi bi-lock me-1"></i> Upgrade for Futures Analysis
                                    <?php else: ?>
                                    <i class="bi bi-arrow-right me-1"></i> Open Futures Module
                                    <?php endif; ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 border-nasdaq-light-blue bg-black">
                        <div class="card-body text-center p-4">
                            <div class="mb-4">
                                <div class="icon-circle bg-nasdaq-light-blue">
                                    <i class="bi bi-cash-coin text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h4 class="card-title mb-3">Fixed Income Alpha</h4>
                            <p class="card-text">
                                Credit spreads and yield curve shifts forecast <strong>market stress</strong> and <strong>risk appetite</strong> 
                                that drive equity valuations.
                            </p>
                            <div class="mt-4">
                                <h6 class="text-nasdaq-light-blue">Key Signals:</h6>
                                <ul class="list-unstyled text-start">
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Credit Spread Changes</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Yield Curve Steepening/Flattening</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>TED Spread Analysis</li>
                                    <li><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Inflation Expectations</li>
                                </ul>
                            </div>
                            <?php if($userLoggedIn): ?>
                            <div class="mt-4">
                                <a href="dashboard/?module=fixed_income" class="btn btn-sm btn-outline-nasdaq-light-blue w-100">
                                    <?php if($userTier !== 'premium'): ?>
                                    <i class="bi bi-lock me-1"></i> Premium Feature Only
                                    <?php else: ?>
                                    <i class="bi bi-arrow-right me-1"></i> Open Fixed Income Module
                                    <?php endif; ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing / Tier Comparison Section -->
    <section class="py-5 bg-black" id="pricing">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Choose Your Plan</h2>
                <p class="lead text-muted">Start with Standard, upgrade as you grow</p>
            </div>
            
            <div class="row g-4">
                <?php foreach($tierFeatures as $tierKey => $tierInfo): 
                    $isCurrentTier = ($userLoggedIn && $userTier === $tierKey);
                ?>
                <div class="col-md-4">
                    <div class="card h-100 bg-dark <?php echo $isCurrentTier ? 'border-nasdaq-blue shadow-lg' : 'border-secondary'; ?>">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <h4 class="card-title <?php echo $tierInfo['color']; ?>">
                                    <?php echo $tierInfo['title']; ?>
                                </h4>
                                <?php if($isCurrentTier): ?>
                                <span class="badge bg-nasdaq-blue">Current Plan</span>
                                <?php endif; ?>
                            </div>
                            
                            <ul class="list-unstyled mb-4">
                                <?php foreach($tierInfo['features'] as $feature): ?>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-nasdaq-green me-2"></i>
                                    <?php echo $feature; ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            
                            <div class="mt-auto">
                                <?php if(!$userLoggedIn): ?>
                                <a href="register.php?tier=<?php echo $tierKey; ?>" class="btn <?php echo $tierKey === 'pro' ? 'btn-nasdaq-blue' : 'btn-outline-light'; ?> w-100">
                                    <?php if($tierKey === 'standard'): ?>
                                    Start Free Trial
                                    <?php else: ?>
                                    Get Started
                                    <?php endif; ?>
                                </a>
                                <?php else: ?>
                                    <?php if($isCurrentTier): ?>
                                    <button class="btn btn-outline-light w-100" disabled>Current Plan</button>
                                    <?php else: ?>
                                    <a href="subscription/?upgrade_to=<?php echo $tierKey; ?>" class="btn <?php echo $tierKey === 'pro' ? 'btn-nasdaq-blue' : 'btn-outline-light'; ?> w-100">
                                        Upgrade to <?php echo $tierInfo['title']; ?>
                                    </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Platform Features -->
    <section class="py-5 bg-dark">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Platform Capabilities</h2>
                <p class="lead text-muted">Everything you need in one integrated dashboard</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <i class="bi bi-speedometer2 text-nasdaq-blue" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h4>Real-time Signal Dashboard</h4>
                            <p class="text-muted">
                                Live feed of equity signals generated from derivatives data. Filter by sector, 
                                confidence level, or time horizon.
                            </p>
                            <?php if($userLoggedIn && $userTier === 'standard'): ?>
                            <small class="text-warning"><i class="bi bi-clock me-1"></i> Standard: 30-min delayed signals</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <i class="bi bi-graph-up-arrow text-nasdaq-green" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h4>Backtesting Engine</h4>
                            <p class="text-muted">
                                Test our signals against historical data. See win rates, Sharpe ratios, and 
                                maximum drawdowns for any strategy.
                            </p>
                            <?php if($userLoggedIn && $userTier === 'standard'): ?>
                            <small class="text-warning"><i class="bi bi-lock me-1"></i> Available on Pro+</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <i class="bi bi-bell text-nasdaq-red" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h4>Smart Alerts</h4>
                            <p class="text-muted">
                                Get notified via email, SMS, or webhook when key derivatives signals trigger 
                                for your watchlist stocks.
                            </p>
                            <?php if($userLoggedIn && $userTier === 'standard'): ?>
                            <small class="text-warning"><i class="bi bi-envelope me-1"></i> Standard: Email only</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <i class="bi bi-diagram-3 text-nasdaq-light-blue" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h4>Cross-Asset Correlation Matrix</h4>
                            <p class="text-muted">
                                Visualize how options, futures, and fixed income data correlate with 
                                individual stocks and sectors.
                            </p>
                            <?php if($userLoggedIn && $userTier === 'standard'): ?>
                            <small class="text-warning"><i class="bi bi-lock me-1"></i> Available on Pro+</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <i class="bi bi-shield-check text-nasdaq-blue" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h4>Risk Management Tools</h4>
                            <p class="text-muted">
                                Calculate position sizes, stop losses, and hedge ratios based on 
                                derivatives-implied volatility and correlation.
                            </p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <i class="bi bi-code-slash text-nasdaq-green" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h4>API Access</h4>
                            <p class="text-muted">
                                Integrate our signals into your own trading systems, research platforms, 
                                or portfolio management tools.
                            </p>
                            <?php if($userLoggedIn): ?>
                                <?php if($userTier === 'standard'): ?>
                                <small class="text-warning"><i class="bi bi-lock me-1"></i> Available on Pro+</small>
                                <?php elseif($userTier === 'pro'): ?>
                                <small class="text-info"><i class="bi bi-infinity me-1"></i> Pro: 10,000 calls/month</small>
                                <?php else: ?>
                                <small class="text-success"><i class="bi bi-infinity me-1"></i> Premium: Unlimited</small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-black">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="display-5 fw-bold mb-4">
                        <?php if($userLoggedIn): ?>
                        Ready to Unlock More Features?
                        <?php else: ?>
                        Ready to See the Future of Stock Prediction?
                        <?php endif; ?>
                    </h2>
                    <p class="lead mb-4">
                        <?php if($userLoggedIn): ?>
                        Upgrade your plan to access advanced features and increase your API limits.
                        <?php else: ?>
                        Join hedge funds, proprietary trading firms, and sophisticated investors 
                        who use derivatives intelligence to stay ahead of the market.
                        <?php endif; ?>
                    </p>
                    <?php if(!$userLoggedIn): ?>
                    <a href="register.php" class="btn btn-nasdaq-blue btn-lg px-5">
                        <i class="bi bi-lightning-charge-fill me-2"></i>Start Your Free Trial
                    </a>
                    <p class="text-muted mt-3">No credit card required • 14-day full access</p>
                    <?php elseif($userTier !== 'premium'): ?>
                    <a href="subscription/" class="btn btn-nasdaq-green btn-lg px-5">
                        <i class="bi bi-arrow-up-circle-fill me-2"></i>Upgrade Your Plan
                    </a>
                    <p class="text-muted mt-3">Unlock all features and higher limits</p>
                    <?php else: ?>
                    <a href="dashboard/" class="btn btn-nasdaq-blue btn-lg px-5">
                        <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                    </a>
                    <p class="text-muted mt-3">You're on our highest tier. Thank you for being a premium customer!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>