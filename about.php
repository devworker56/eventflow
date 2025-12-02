<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - EventFlow Institutional</title>
    <meta name="description" content="Learn about EventFlow's mission to democratize derivatives intelligence for equity traders.">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Hero Section -->
    <section class="py-5" style="padding-top: 100px !important; background: linear-gradient(135deg, #000 0%, var(--nasdaq-dark-gray) 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">
                        Democratizing <span class="text-nasdaq-blue">Derivatives Intelligence</span>
                    </h1>
                    <p class="lead mb-4">
                        We believe every equity trader should have access to the same derivatives insights 
                        that institutional investors use to gain an edge.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-5 bg-dark">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="display-5 fw-bold mb-4">Our Mission</h2>
                    <p class="lead mb-4">
                        For decades, derivatives markets have provided early warning signals for stock movements, 
                        but this intelligence was only accessible to large institutions with expensive Bloomberg 
                        terminals and dedicated quant teams.
                    </p>
                    <p>
                        EventFlow was founded to democratize this intelligence. We use artificial intelligence 
                        and cloud computing to analyze options, futures, and fixed income data at scale, 
                        delivering actionable equity signals to traders of all sizes.
                    </p>
                </div>
                <div class="col-lg-6">
                    <div class="card bg-black border-nasdaq-blue">
                        <div class="card-body p-4">
                            <h4 class="card-title text-nasdaq-blue mb-4">The Problem We Solve</h4>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Information Asymmetry</h6>
                                    <p class="small text-muted mb-0">Institutions see derivatives signals days before retail traders</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-clock text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Time Lag</h6>
                                    <p class="small text-muted mb-0">Traditional equity analysis is reactive, not predictive</p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-cash text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Cost Barrier</h6>
                                    <p class="small text-muted mb-0">Derivatives data and analysis tools cost $25,000+ per year</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-5 bg-black">
        <div class="container">
            <h2 class="display-5 fw-bold text-center mb-5">Our Team</h2>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card bg-dark border-nasdaq-blue h-100">
                        <div class="card-body text-center p-4">
                            <div class="team-photo mb-4">
                                <div class="rounded-circle bg-nasdaq-blue mx-auto d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                    <i class="bi bi-person text-white" style="font-size: 3rem;"></i>
                                </div>
                            </div>
                            <h4 class="card-title">Alex Chen</h4>
                            <p class="text-nasdaq-blue mb-3">CEO & Founder</p>
                            <p class="text-muted small">
                                Former derivatives trader at Goldman Sachs. Built quantitative trading systems 
                                that generated $200M+ in alpha over 8 years.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-dark border-nasdaq-green h-100">
                        <div class="card-body text-center p-4">
                            <div class="team-photo mb-4">
                                <div class="rounded-circle bg-nasdaq-green mx-auto d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                    <i class="bi bi-person text-white" style="font-size: 3rem;"></i>
                                </div>
                            </div>
                            <h4 class="card-title">Dr. Sarah Johnson</h4>
                            <p class="text-nasdaq-green mb-3">Chief Data Scientist</p>
                            <p class="text-muted small">
                                PhD in Computational Finance from MIT. Published research on options pricing 
                                anomalies and volatility forecasting.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-dark border-nasdaq-light-blue h-100">
                        <div class="card-body text-center p-4">
                            <div class="team-photo mb-4">
                                <div class="rounded-circle bg-nasdaq-light-blue mx-auto d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                    <i class="bi bi-person text-white" style="font-size: 3rem;"></i>
                                </div>
                            </div>
                            <h4 class="card-title">Michael Rodriguez</h4>
                            <p class="text-nasdaq-light-blue mb-3">CTO</p>
                            <p class="text-muted small">
                                Former lead engineer at Two Sigma. Built high-frequency trading systems 
                                processing 1M+ market events per second.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-5 bg-dark">
        <div class="container">
            <h2 class="display-5 fw-bold text-center mb-5">Our Values</h2>
            
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="bi bi-shield-check text-nasdaq-blue" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5>Transparency</h5>
                        <p class="text-muted small">
                            We show our backtested results and explain exactly how our signals are generated.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="bi bi-graph-up-arrow text-nasdaq-green" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5>Performance</h5>
                        <p class="text-muted small">
                            Every feature is designed to help our users make better trading decisions.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="bi bi-people text-nasdaq-light-blue" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5>Accessibility</h5>
                        <p class="text-muted small">
                            We make sophisticated derivatives analysis accessible to all traders.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="bi bi-lightning-charge text-nasdaq-red" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5>Innovation</h5>
                        <p class="text-muted small">
                            We constantly improve our AI models and add new data sources.
                        </p>
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
                    <h2 class="display-5 fw-bold mb-4">Join the Future of Trading</h2>
                    <p class="lead mb-4">
                        Be part of the movement to democratize derivatives intelligence. 
                        Whether you're a day trader, swing trader, or portfolio manager, 
                        EventFlow gives you the institutional edge.
                    </p>
                    <a href="register.php" class="btn btn-nasdaq-blue btn-lg px-5">
                        <i class="bi bi-lightning-charge-fill me-2"></i>Start Free Trial
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>