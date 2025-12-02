<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Demo - EventFlow Institutional</title>
    <meta name="description" content="See EventFlow Institutional in action. Watch our demo video and explore platform features.">
    
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
                <div class="col-lg-10 text-center">
                    <h1 class="display-4 fw-bold mb-4">
                        See <span class="text-nasdaq-blue">EventFlow</span> in Action
                    </h1>
                    <p class="lead mb-4">
                        Watch how derivatives intelligence transforms equity trading decisions.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Video -->
    <section class="py-5 bg-dark">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card bg-black border-nasdaq-blue">
                        <div class="card-body p-0">
                            <div class="ratio ratio-16x9">
                                <!-- Placeholder for demo video -->
                                <div class="d-flex align-items-center justify-content-center bg-dark" style="height: 100%;">
                                    <div class="text-center">
                                        <i class="bi bi-play-circle text-nasdaq-blue" style="font-size: 4rem;"></i>
                                        <h4 class="mt-3">Platform Demo Video</h4>
                                        <p class="text-muted">Coming soon</p>
                                        <a href="register.php" class="btn btn-nasdaq-blue mt-3">
                                            <i class="bi bi-lightning-charge me-2"></i>Try Live Demo
                                        </a>
                                    </div>
                                </div>
                                <!-- In production, replace with:
                                <iframe src="https://www.youtube.com/embed/your-video-id" 
                                        title="EventFlow Institutional Demo" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen>
                                </iframe>
                                -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Demo -->
    <section class="py-5 bg-black">
        <div class="container">
            <h2 class="text-center display-5 fw-bold mb-5">Interactive Demo</h2>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card bg-dark h-100">
                        <div class="card-header border-nasdaq-blue">
                            <h5 class="mb-0">Options Signal Generator</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label">Select a Stock</label>
                                <select class="form-select" id="demoStockSelect">
                                    <option value="AAPL">Apple (AAPL)</option>
                                    <option value="MSFT">Microsoft (MSFT)</option>
                                    <option value="GOOGL">Alphabet (GOOGL)</option>
                                    <option value="AMZN">Amazon (AMZN)</option>
                                    <option value="TSLA">Tesla (TSLA)</option>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Event Type</label>
                                <select class="form-select" id="demoEventSelect">
                                    <option value="earnings">Earnings Report</option>
                                    <option value="product">Product Launch</option>
                                    <option value="fda">FDA Decision</option>
                                    <option value="macro">Macro Event</option>
                                </select>
                            </div>
                            
                            <button class="btn btn-nasdaq-blue w-100" onclick="runDemoAnalysis()">
                                <i class="bi bi-lightning-charge me-2"></i>Generate Signal
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card bg-dark h-100">
                        <div class="card-header border-nasdaq-blue">
                            <h5 class="mb-0">Demo Results</h5>
                        </div>
                        <div class="card-body">
                            <div id="demoResults">
                                <div class="text-center py-5">
                                    <i class="bi bi-graph-up text-nasdaq-blue" style="font-size: 3rem;"></i>
                                    <p class="mt-3">Run analysis to see results</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Walkthrough -->
    <section class="py-5 bg-dark">
        <div class="container">
            <h2 class="text-center display-5 fw-bold mb-5">Platform Walkthrough</h2>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card bg-black border-nasdaq-blue h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <div class="icon-circle bg-nasdaq-blue mx-auto">
                                    <i class="bi bi-speedometer2 text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h5>Dashboard Overview</h5>
                            <p class="text-muted small">
                                Real-time signal feed, portfolio impact analysis, and market overview
                            </p>
                            <button class="btn btn-sm btn-outline-nasdaq-blue" onclick="showDemoStep(1)">
                                View Demo
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-black border-nasdaq-green h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <div class="icon-circle bg-nasdaq-green mx-auto">
                                    <i class="bi bi-activity text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h5>Options Intelligence</h5>
                            <p class="text-muted small">
                                IV surface analysis, put/call ratios, and volatility forecasting
                            </p>
                            <button class="btn btn-sm btn-outline-nasdaq-green" onclick="showDemoStep(2)">
                                View Demo
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-black border-nasdaq-light-blue h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <div class="icon-circle bg-nasdaq-light-blue mx-auto">
                                    <i class="bi bi-cash-coin text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h5>Cross-Asset Analysis</h5>
                            <p class="text-muted small">
                                Futures correlations, fixed income signals, and FX impacts
                            </p>
                            <button class="btn btn-sm btn-outline-nasdaq-light-blue" onclick="showDemoStep(3)">
                                View Demo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Live Demo CTA -->
    <section class="py-5 bg-black">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="display-5 fw-bold mb-4">Ready for the Live Experience?</h2>
                    <p class="lead mb-4">
                        Get hands-on with our full platform. No credit card required.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="register.php" class="btn btn-nasdaq-blue btn-lg px-5">
                            <i class="bi bi-lightning-charge-fill me-2"></i>Start Free Trial
                        </a>
                        <a href="contact.php" class="btn btn-outline-light btn-lg px-5">
                            <i class="bi bi-calendar me-2"></i>Schedule Live Demo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    
    <script>
    function runDemoAnalysis() {
        const stock = document.getElementById('demoStockSelect').value;
        const eventType = document.getElementById('demoEventSelect').value;
        
        // Show loading
        document.getElementById('demoResults').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-nasdaq-blue" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Analyzing ${stock} ${eventType} data...</p>
            </div>
        `;
        
        // Simulate API call
        setTimeout(() => {
            const signals = {
                'AAPL': {
                    earnings: {
                        iv_percentile: 85,
                        expected_move: '±6.2%',
                        pcr_signal: 'bearish',
                        confidence: 78,
                        recommendation: 'Consider buying puts or selling calls'
                    },
                    product: {
                        iv_percentile: 65,
                        expected_move: '±4.8%',
                        pcr_signal: 'bullish',
                        confidence: 82,
                        recommendation: 'Consider buying calls or selling puts'
                    }
                },
                'MSFT': {
                    earnings: {
                        iv_percentile: 72,
                        expected_move: '±5.1%',
                        pcr_signal: 'neutral',
                        confidence: 65,
                        recommendation: 'Consider strangle or straddle'
                    }
                }
            };
            
            const result = signals[stock]?.[eventType] || {
                iv_percentile: 50,
                expected_move: '±3.5%',
                pcr_signal: 'neutral',
                confidence: 60,
                recommendation: 'No strong signal detected'
            };
            
            document.getElementById('demoResults').innerHTML = `
                <h6>${stock} ${eventType.replace(/([A-Z])/g, ' $1').trim()} Analysis</h6>
                <div class="row mt-3">
                    <div class="col-6">
                        <div class="border rounded p-2 mb-2">
                            <small class="text-muted">IV Percentile</small>
                            <div class="fw-bold ${result.iv_percentile > 70 ? 'text-danger' : result.iv_percentile > 50 ? 'text-warning' : 'text-success'}">
                                ${result.iv_percentile}%
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2 mb-2">
                            <small class="text-muted">Expected Move</small>
                            <div class="fw-bold">${result.expected_move}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="border rounded p-2 mb-2">
                            <small class="text-muted">PCR Signal</small>
                            <div class="fw-bold ${result.pcr_signal === 'bullish' ? 'text-success' : result.pcr_signal === 'bearish' ? 'text-danger' : 'text-warning'}">
                                ${result.pcr_signal}
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2 mb-2">
                            <small class="text-muted">Confidence</small>
                            <div class="fw-bold ${result.confidence > 70 ? 'text-success' : result.confidence > 50 ? 'text-warning' : 'text-danger'}">
                                ${result.confidence}%
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <h6>Recommendation</h6>
                    <p class="small">${result.recommendation}</p>
                </div>
            `;
        }, 1500);
    }
    
    function showDemoStep(step) {
        alert(`Demo step ${step} would show here. In the live platform, this would navigate to the corresponding module.`);
    }
    </script>
</body>
</html>