<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - AccuTradingSignals</title>
    <meta name="description" content="Learn about AccuTradingSignals' mission to provide multi-dimensional market intelligence for equity traders.">
    
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
                        Multi-Dimensional <span class="text-nasdaq-blue">Market Intelligence</span>
                    </h1>
                    <p class="lead mb-4">
                        AccuTradingSignals integrates AI and quantitative analysis with sentiment analysis and cross-asset signals to strive toward a clearer, faster, and more accurate view of where equities are headed next.
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
                        Traditional equity analysis is often reactive. We provide an alternative by fusing sentiment analysis on corporate, economic, and geopolitical events with real-time signals from options, futures, FX, and fixed income markets.
                    </p>
                    <p>
                        Using advanced machine learning, AccuTradingSignals seeks to identify potential stock movements 3-5 days in advance. This integrated approach—combining quantitative models with qualitative analysis—aims to highlight both direct impacts and their ripple effects, working to turn complexity into clarity and market noise into actionable signals.
                    </p>
                </div>
                <div class="col-lg-6">
                    <div class="card bg-black border-nasdaq-blue">
                        <div class="card-body p-4">
                            <h4 class="card-title text-nasdaq-blue mb-4">The Challenge We Address</h4>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Reactive, Not Predictive</h6>
                                    <p class="small text-muted mb-0">Traditional analysis often chases moves after they've happened</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-clock text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Fragmented Intelligence</h6>
                                    <p class="small text-muted mb-0">Viewing asset classes in isolation can miss cross-asset signals</p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-cash text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>Signal vs. Noise</h6>
                                    <p class="small text-muted mb-0">Separating meaningful sentiment from market chatter requires sophisticated analysis</p>
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
                                Former derivatives trader at Goldman Sachs. Built quantitative systems that integrate sentiment analysis with derivatives data to seek earlier indications of equity movements.
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
                                PhD in Computational Finance from MIT. Published research on cross-asset signal integration and AI-driven sentiment analysis.
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
                                Former lead engineer at Two Sigma. Built systems processing 1M+ market events per second across equities, options, and futures.
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
                            We explain how our models integrate sentiment and cross-asset data to generate signals.
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
                            Every feature is designed to provide earlier insights for equity movement analysis.
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
                            We make multi-dimensional market intelligence accessible to all traders.
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
                            We continuously refine our AI models and expand data sources for improved analysis.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>