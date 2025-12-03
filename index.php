<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AccuTrading Signals - AI-Powered Event Intelligence</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link href="assets/css/custom.css" rel="stylesheet">
    
    <!-- NASDAQ Color Theme -->
    <style>
        :root {
            --nasdaq-blue: #0091DA;
            --nasdaq-dark-blue: #0056A4;
            --nasdaq-light-blue: #00B2FF;
            --nasdaq-green: #00D18C;
            --nasdaq-red: #FF4D4D;
            --nasdaq-gray: #2D2D2D;
            --nasdaq-light-gray: #F5F5F5;
            --nasdaq-dark-gray: #1A1A1A;
        }
        
        .bg-nasdaq-blue { background-color: var(--nasdaq-blue) !important; }
        .text-nasdaq-blue { color: var(--nasdaq-blue) !important; }
        .btn-nasdaq-blue { 
            background-color: var(--nasdaq-blue);
            border-color: var(--nasdaq-blue);
            color: white;
        }
        .btn-nasdaq-blue:hover {
            background-color: var(--nasdaq-dark-blue);
            border-color: var(--nasdaq-dark-blue);
        }
        .btn-outline-nasdaq-blue {
            border-color: var(--nasdaq-blue);
            color: var(--nasdaq-blue);
        }
        .btn-outline-nasdaq-blue:hover {
            background-color: var(--nasdaq-blue);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Fixed Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top border-bottom border-nasdaq-blue">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/images/logo.png" alt="EventFlow" height="40" class="me-2">
                <span class="fw-bold text-nasdaq-blue">AccuTrading</span>
                <span class="text-light ms-1">Signals</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="features.php">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pricing.php">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['user_email']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="dashboard/"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="dashboard/profile.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-nasdaq-blue ms-2" href="register.php">Start Free Trial</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Half-height Hero Banner -->
<section class="hero-banner position-relative" style="height: 50vh; min-height: 400px;">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">
                    Decode <span class="text-nasdaq-blue">Derivatives Intelligence</span> for Superior Equity Predictions
                </h1>
                <p class="lead mb-4">
                    AccuTradingSignals is an intelligence engine that decodes market events to provide a predictive trading edge. We combine rigorous quantitative finance—analyzing metrics like options volatility surfaces and futures term structures—with advanced machine learning models that interpret news sentiment and social narratives. This allows us to identify triggers, model cross-asset reactions, and forecast their ripple effects into equity markets.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="register.php" class="btn btn-nasdaq-blue btn-lg px-4">
                        <i class="bi bi-lightning-charge-fill me-2"></i>Start Free Trial
                    </a>
                    <a href="#how-it-works" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-play-circle me-2"></i>See How It Works
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block">
                <div class="card bg-dark border-nasdaq-blue">
                    <div class="card-body">
                        <h5 class="card-title text-nasdaq-blue">Live Equity Signal</h5>
                        <div class="signal-preview">
                            <div class="d-flex align-items-center mb-3">
                                <div class="symbol-icon bg-nasdaq-blue rounded-circle p-2 me-3">
                                    <i class="bi bi-graph-up text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">AAPL</h6>
                                    <small class="text-muted">Earnings in 2 days</small>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge bg-success">+82% Confidence</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Derivatives Signal:</small>
                                <p class="mb-1">Options IV suggests 8.2% move priced in</p>
                                <small class="text-muted">Our AI predicts: <strong class="text-success">+5.3%</strong></p>
                            </div>
                            <button class="btn btn-sm btn-outline-nasdaq-blue w-100">
                                View Full Analysis
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Animated background elements -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="z-index: -1; overflow: hidden;">
        <div class="floating-element" style="position: absolute; top: 20%; left: 10%; width: 100px; height: 100px; border: 2px solid var(--nasdaq-blue); opacity: 0.1; border-radius: 50%;"></div>
        <div class="floating-element" style="position: absolute; bottom: 30%; right: 15%; width: 150px; height: 150px; border: 2px solid var(--nasdaq-green); opacity: 0.1; border-radius: 50%;"></div>
    </div>
</section>

    <!-- Features Section -->
    <section class="py-5 bg-dark">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Cross-Asset Event Intelligence</h2>
                <p class="lead text-muted">Integrated analysis across 7 asset classes</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card h-100 border-nasdaq-blue bg-black">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-graph-up text-nasdaq-blue" style="font-size: 2rem;"></i>
                            </div>
                            <h5 class="card-title">Equities & ETFs</h5>
                            <p class="card-text text-muted">Direct exposure analysis with sector rotation signals</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card h-100 border-nasdaq-blue bg-black">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-activity text-nasdaq-green" style="font-size: 2rem;"></i>
                            </div>
                            <h5 class="card-title">Options Markets</h5>
                            <p class="card-text text-muted">Volatility surface forecasting and skew analysis</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card h-100 border-nasdaq-blue bg-black">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-currency-exchange text-nasdaq-red" style="font-size: 2rem;"></i>
                            </div>
                            <h5 class="card-title">FX & Futures</h5>
                            <p class="card-text text-muted">Term structure and physical market impacts</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card h-100 border-nasdaq-blue bg-black">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-cash-coin text-nasdaq-light-blue" style="font-size: 2rem;"></i>
                            </div>
                            <h5 class="card-title">Fixed Income</h5>
                            <p class="card-text text-muted">Rate sensitivity and credit spread implications</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard Preview -->
    <section class="py-5 bg-black">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="display-5 fw-bold mb-4">Professional Dashboard</h2>
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-check-circle-fill text-nasdaq-green me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h5 class="mb-0">Real-time Signal Feed</h5>
                                <p class="text-muted mb-0">Live updates from Modal AI engine</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-check-circle-fill text-nasdaq-green me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h5 class="mb-0">Cross-Asset Correlation Matrix</h5>
                                <p class="text-muted mb-0">Visualize connections across markets</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-check-circle-fill text-nasdaq-green me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h5 class="mb-0">Portfolio Impact Analysis</h5>
                                <p class="text-muted mb-0">See how events affect your positions</p>
                            </div>
                        </div>
                    </div>
                    <a href="dashboard/" class="btn btn-nasdaq-blue btn-lg">
                        <i class="bi bi-arrow-right-circle me-2"></i>View Sample Dashboard
                    </a>
                </div>
                <div class="col-lg-6">
                    <div class="card bg-dark border-nasdaq-blue">
                        <div class="card-body p-0">
                            <div class="dashboard-preview">
                                <!-- Mock dashboard preview -->
                                <div class="p-3 border-bottom border-nasdaq-blue d-flex justify-content-between">
                                    <span class="text-nasdaq-blue">Live Signals</span>
                                    <span class="badge bg-nasdaq-blue">Real-time</span>
                                </div>
                                <div class="p-3">
                                    <div class="signal-item mb-3">
                                        <div class="d-flex justify-content-between">
                                            <strong class="text-light">FED Rate Decision</strong>
                                            <span class="text-nasdaq-green">+82% Confidence</span>
                                        </div>
                                        <small class="text-muted">Impact: Equities ↓, USD ↑, Bonds ↑</small>
                                    </div>
                                    <div class="signal-item mb-3">
                                        <div class="d-flex justify-content-between">
                                            <strong class="text-light">AAPL Earnings</strong>
                                            <span class="text-nasdaq-red">-67% Confidence</span>
                                        </div>
                                        <small class="text-muted">Options IV suggests 8.2% move priced</small>
                                    </div>
                                    <div class="signal-item">
                                        <div class="d-flex justify-content-between">
                                            <strong class="text-light">CPI Report</strong>
                                            <span class="text-nasdaq-blue">+91% Confidence</span>
                                        </div>
                                        <small class="text-muted">Market underpricing inflation risk</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Pricing Preview -->
<section class="py-5 bg-dark">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Choose Your Plan</h2>
            <p class="lead text-muted">Start with 14-day free trial, no credit card required</p>
        </div>
        
        <div class="row g-4">
            <?php
            $pdo = getDBConnection();
            $stmt = $pdo->query("SELECT * FROM subscription_plans ORDER BY monthly_price");
            $plans = $stmt->fetchAll();
            
            foreach($plans as $plan):
                // Map database tier names to display names
                $display_name = match($plan['tier_name']) {
                    'explorer' => 'Standard',
                    'professional' => 'Pro',
                    'institutional' => 'Premium',
                    default => ucfirst($plan['tier_name'])
                };
                
                // Determine styling based on tier
                $is_popular = $plan['tier_name'] == 'professional'; // Pro is most popular
                $border_class = $is_popular ? 'nasdaq-blue border-3' : 'secondary';
                $btn_class = match($plan['tier_name']) {
                    'professional' => 'nasdaq-blue', // Pro gets primary button
                    'institutional' => 'outline-nasdaq-blue', // Premium gets outline
                    default => 'outline-light' // Standard gets basic
                };
            ?>
            <div class="col-md-4">
                <div class="card h-100 border-<?php echo $border_class; ?>">
                    <?php if($is_popular): ?>
                    <div class="card-header bg-nasdaq-blue text-center py-3">
                        <span class="badge bg-dark">MOST POPULAR</span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4"><?php echo $display_name; ?></h3>
                        <div class="text-center mb-4">
                            <span class="display-4 fw-bold">$<?php echo $plan['monthly_price']; ?></span>
                            <span class="text-muted">/month</span>
                            <div class="text-muted small">$<?php echo $plan['annual_price']; ?> billed annually</div>
                        </div>
                        
                        <ul class="list-unstyled mb-4">
                            <?php
                            $features = json_decode($plan['features'], true);
                            foreach($features as $feature):
                            ?>
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-nasdaq-green me-2"></i>
                                <?php echo htmlspecialchars($feature); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <div class="text-center">
                            <a href="subscription/checkout.php?plan=<?php echo $plan['tier_name']; ?>" 
                               class="btn btn-<?php echo $btn_class; ?> w-100">
                                Start Free Trial
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

    <!-- Half-height Footer -->
    <footer class="bg-black py-4" style="height: 40vh; min-height: 200px;">
        <div class="container h-100">
            <div class="row h-100">
                <div class="col-md-4">
                    <h5 class="text-nasdaq-blue mb-3">EventFlow Institutional</h5>
                    <p class="text-muted small">
                        AI-powered event intelligence platform for systematic traders and institutions.
                    </p>
                </div>
                <div class="col-md-2">
                    <h6 class="text-light mb-3">Product</h6>
                    <ul class="list-unstyled">
                        <li><a href="features.php" class="text-muted small text-decoration-none">Features</a></li>
                        <li><a href="pricing.php" class="text-muted small text-decoration-none">Pricing</a></li>
                        <li><a href="#demo" class="text-muted small text-decoration-none">Demo</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6 class="text-light mb-3">Company</h6>
                    <ul class="list-unstyled">
                        <li><a href="about.php" class="text-muted small text-decoration-none">About</a></li>
                        <li><a href="contact.php" class="text-muted small text-decoration-none">Contact</a></li>
                        <li><a href="#" class="text-muted small text-decoration-none">Careers</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-light mb-3">Connect With Us</h6>
                    <div class="d-flex gap-3 mb-3">
                        <a href="#" class="text-nasdaq-blue"><i class="bi bi-twitter" style="font-size: 1.5rem;"></i></a>
                        <a href="#" class="text-nasdaq-blue"><i class="bi bi-linkedin" style="font-size: 1.5rem;"></i></a>
                        <a href="#" class="text-nasdaq-blue"><i class="bi bi-github" style="font-size: 1.5rem;"></i></a>
                    </div>
                    <p class="text-muted small mb-0">
                        &copy; <?php echo date('Y'); ?> EventFlow Institutional. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/api-consumer.js"></script>
    <script>
        // Fetch live signal preview
        document.addEventListener('DOMContentLoaded', function() {
            fetchLiveSignalPreview();
            
            // Auto-refresh every 30 seconds
            setInterval(fetchLiveSignalPreview, 30000);
        });
        
        function fetchLiveSignalPreview() {
            fetch('api/get-preview-signals.php')
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('liveSignalPreview').innerHTML = data.html;
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>