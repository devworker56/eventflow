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
    <meta name="description" content="Decode derivatives intelligence for superior equity predictions with AI-powered cross-asset analysis.">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link href="assets/css/custom.css" rel="stylesheet">
    
    <!-- Three.js for 3D effects -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    
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
        
        /* Transparent Hero Styles */
        .transparent-hero {
            position: relative;
            background: transparent !important;
            min-height: 80vh; /* Reduced to bring content higher */
            overflow: hidden;
        }
        
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
        
        .stock-chart-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.4;
            z-index: 1;
        }
        
        #neuralNetwork {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            pointer-events: none;
        }
        
        .chart-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .data-point {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            pointer-events: none;
            box-shadow: 0 0 10px currentColor;
        }
        
        .connection-line {
            position: absolute;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--nasdaq-blue), transparent);
            transform-origin: left center;
            pointer-events: none;
        }
        
        .floating-number {
            position: absolute;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: var(--nasdaq-green);
            font-size: 14px;
            text-shadow: 0 0 10px currentColor;
            pointer-events: none;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <!-- Fixed Navigation Bar - Simplified version -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top border-bottom border-nasdaq-blue">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/images/logo.png" alt="AccuTrading Signals" height="40" class="me-2">
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
                            <a class="btn btn-nasdaq-blue ms-2" href="register.php">Get Started</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Transparent Hero Banner with Dynamic Effects -->
    <section class="transparent-hero" style="padding-top: 60px;"> <!-- Reduced padding -->
        <!-- Stock Chart Background -->
        <div class="stock-chart-container">
            <canvas id="stockChart" class="chart-canvas"></canvas>
        </div>
        
        <!-- Neural Network / Particle Effect -->
        <div id="neuralNetwork"></div>
        
        <!-- Content Overlay - No visible box -->
        <div class="container h-100">
            <div class="row align-items-start justify-content-center h-100"> <!-- align-items-start instead of align-items-center -->
                <div class="col-lg-10 col-xl-8 mt-5"> <!-- Added mt-5 to move closer to top -->
                    <div class="text-center">
                        <h1 class="display-4 fw-bold mb-4">
                            Decode <span class="text-nasdaq-blue">Derivatives Intelligence</span>
                        </h1>
                        <p class="lead mb-4 text-light">
                            AccuTradingSignals is an intelligence engine that decodes market events to provide a predictive trading edge. We combine rigorous quantitative finance—analyzing metrics like options volatility surfaces and futures term structures—with advanced machine learning models that interpret news sentiment and social narratives. This allows us to identify triggers, model cross-asset reactions, and forecast their ripple effects into equity markets.
                        </p>
                        <!-- Removed buttons -->
                    </div>
                    
                    <!-- Live Data Ticker -->
                    <div class="row mt-4 justify-content-center">
                        <div class="col-md-8">
                            <div class="card bg-dark border-nasdaq-blue opacity-75">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-nasdaq-blue me-2">LIVE</span>
                                            <small class="text-light">Market Signals</small>
                                        </div>
                                        <div id="liveTicker" class="text-end">
                                            <small class="text-success">Loading real-time data...</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Cross-Asset Analysis Section -->
    <section class="py-5 bg-dark" id="cross-asset-analysis">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Cross-Asset Event Intelligence</h2>
                <p class="lead text-muted">Integrated analysis across 7 asset classes</p>
            </div>
            
            <div class="row g-4">
                <!-- Equities & ETFs -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-nasdaq-blue bg-black">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <i class="bi bi-graph-up text-nasdaq-blue" style="font-size: 2.5rem;"></i>
                            </div>
                            <h4 class="card-title text-center mb-4">Equities & ETFs</h4>
                            <p class="card-text text-center text-muted mb-4">Direct exposure analysis with sector rotation signals</p>
                        </div>
                    </div>
                </div>
                
                <!-- Options Markets with Detailed Signals -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-nasdaq-green bg-black">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <i class="bi bi-activity text-nasdaq-green" style="font-size: 2.5rem;"></i>
                            </div>
                            <h4 class="card-title text-center mb-3">Options Intelligence</h4>
                            <p class="card-text text-center mb-4">
                                Options traders price in earnings surprises, M&A rumors, and product launches <strong>days before</strong> stock traders react.
                            </p>
                            <div class="mt-3">
                                <h6 class="text-nasdaq-green mb-3">Key Signals:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Implied Volatility Surfaces</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Put/Call Ratio Extremes</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Skew & Smile Analysis</li>
                                    <li><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Unusual Options Activity</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- FX & Futures with Detailed Signals -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-nasdaq-red bg-black">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <i class="bi bi-currency-exchange text-nasdaq-red" style="font-size: 2.5rem;"></i>
                            </div>
                            <h4 class="card-title text-center mb-3">Futures & FX Signals</h4>
                            <p class="card-text text-center mb-4">
                                Futures term structure predicts <strong>sector rotations</strong> and <strong>market regimes</strong> that determine which stocks will outperform.
                            </p>
                            <div class="mt-3">
                                <h6 class="text-nasdaq-red mb-3">Key Signals:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>VIX Futures Curve</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Commodity Term Structure</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Currency Futures Positioning</li>
                                    <li><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Index Futures Roll</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Fixed Income with Detailed Signals -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-nasdaq-light-blue bg-black">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <i class="bi bi-cash-coin text-nasdaq-light-blue" style="font-size: 2.5rem;"></i>
                            </div>
                            <h4 class="card-title text-center mb-3">Fixed Income Alpha</h4>
                            <p class="card-text text-center mb-4">
                                Credit spreads and yield curve shifts forecast <strong>market stress</strong> and <strong>risk appetite</strong> that drive equity valuations.
                            </p>
                            <div class="mt-3">
                                <h6 class="text-nasdaq-light-blue mb-3">Key Signals:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Credit Spread Changes</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Yield Curve Steepening/Flattening</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-nasdaq-green me-2"></i>TED Spread Analysis</li>
                                    <li><i class="bi bi-check-circle text-nasdaq-green me-2"></i>Inflation Expectations</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Platform Features -->
    <section class="py-5 bg-black">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
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
                        <li><a href="#cross-asset-analysis" class="text-muted small text-decoration-none">Features</a></li>
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
    
    <!-- Advanced Visual Effects -->
    <script>
        // Stock Chart Generator
        class StockChart {
            constructor(canvasId) {
                this.canvas = document.getElementById(canvasId);
                this.ctx = this.canvas.getContext('2d');
                this.points = [];
                this.time = 0;
                this.initialize();
            }
            
            initialize() {
                this.resize();
                window.addEventListener('resize', () => this.resize());
                
                // Generate initial data points
                this.generateData();
                
                // Start animation
                this.animate();
            }
            
            resize() {
                this.canvas.width = this.canvas.parentElement.clientWidth;
                this.canvas.height = this.canvas.parentElement.clientHeight;
            }
            
            generateData() {
                this.points = [];
                const width = this.canvas.width;
                const height = this.canvas.height;
                const pointCount = 100;
                
                let y = height / 2;
                let trend = 0;
                
                for (let i = 0; i < pointCount; i++) {
                    trend += (Math.random() - 0.5) * 0.1;
                    trend = Math.max(-0.5, Math.min(0.5, trend));
                    
                    y += trend * 5;
                    y += (Math.random() - 0.5) * 20;
                    y = Math.max(height * 0.2, Math.min(height * 0.8, y));
                    
                    this.points.push({
                        x: (i / (pointCount - 1)) * width,
                        y: y,
                        color: y > height / 2 ? '#FF4D4D' : '#00D18C',
                        volatility: Math.random() * 10
                    });
                }
            }
            
            draw() {
                const ctx = this.ctx;
                const width = this.canvas.width;
                const height = this.canvas.height;
                
                // Clear with slight fade effect
                ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
                ctx.fillRect(0, 0, width, height);
                
                // Draw grid
                ctx.strokeStyle = 'rgba(0, 145, 218, 0.1)';
                ctx.lineWidth = 1;
                
                // Horizontal grid lines
                for (let i = 0; i <= 10; i++) {
                    const y = (i / 10) * height;
                    ctx.beginPath();
                    ctx.moveTo(0, y);
                    ctx.lineTo(width, y);
                    ctx.stroke();
                }
                
                // Vertical grid lines
                for (let i = 0; i <= 20; i++) {
                    const x = (i / 20) * width;
                    ctx.beginPath();
                    ctx.moveTo(x, 0);
                    ctx.lineTo(x, height);
                    ctx.stroke();
                }
                
                // Draw stock line
                if (this.points.length > 1) {
                    ctx.beginPath();
                    ctx.moveTo(this.points[0].x, this.points[0].y);
                    
                    for (let i = 1; i < this.points.length; i++) {
                        const p1 = this.points[i - 1];
                        const p2 = this.points[i];
                        
                        // Create smooth curve
                        const cp1x = p1.x + (p2.x - p1.x) / 3;
                        const cp1y = p1.y;
                        const cp2x = p1.x + 2 * (p2.x - p1.x) / 3;
                        const cp2y = p2.y;
                        
                        ctx.bezierCurveTo(cp1x, cp1y, cp2x, cp2y, p2.x, p2.y);
                    }
                    
                    // Gradient for the line
                    const gradient = ctx.createLinearGradient(0, 0, width, 0);
                    gradient.addColorStop(0, 'rgba(0, 145, 218, 0.8)');
                    gradient.addColorStop(0.5, 'rgba(0, 209, 140, 0.8)');
                    gradient.addColorStop(1, 'rgba(255, 77, 77, 0.8)');
                    
                    ctx.strokeStyle = gradient;
                    ctx.lineWidth = 2;
                    ctx.stroke();
                    
                    // Fill under the curve
                    ctx.lineTo(this.points[this.points.length - 1].x, height);
                    ctx.lineTo(this.points[0].x, height);
                    ctx.closePath();
                    
                    const fillGradient = ctx.createLinearGradient(0, 0, 0, height);
                    fillGradient.addColorStop(0, 'rgba(0, 145, 218, 0.1)');
                    fillGradient.addColorStop(1, 'rgba(0, 145, 218, 0.01)');
                    
                    ctx.fillStyle = fillGradient;
                    ctx.fill();
                }
                
                // Update points for animation
                this.time += 0.01;
                
                // Move points to the left and add new ones
                this.points.forEach((point, i) => {
                    point.x -= 0.5;
                    
                    // Add some noise
                    point.y += Math.sin(this.time + i * 0.1) * point.volatility;
                    point.y = Math.max(height * 0.2, Math.min(height * 0.8, point.y));
                    
                    // Remove points that have moved off screen
                    if (point.x < -10) {
                        const lastPoint = this.points[this.points.length - 1];
                        point.x = lastPoint.x + (width / (this.points.length - 1));
                        point.y = lastPoint.y + (Math.random() - 0.5) * 40;
                        point.volatility = Math.random() * 10;
                    }
                });
                
                // Sort by x position
                this.points.sort((a, b) => a.x - b.x);
            }
            
            animate() {
                this.draw();
                requestAnimationFrame(() => this.animate());
            }
        }
        
        // Neural Network / Particle System
        class NeuralNetworkEffect {
            constructor(containerId) {
                this.container = document.getElementById(containerId);
                this.particles = [];
                this.connections = [];
                this.initialize();
            }
            
            initialize() {
                this.createParticles();
                this.animate();
            }
            
            createParticles() {
                const colors = [
                    'var(--nasdaq-blue)',
                    'var(--nasdaq-green)',
                    'var(--nasdaq-red)',
                    'var(--nasdaq-light-blue)'
                ];
                
                // Create particle-like elements
                for (let i = 0; i < 50; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'data-point';
                    
                    const size = 4 + Math.random() * 8;
                    particle.style.width = `${size}px`;
                    particle.style.height = `${size}px`;
                    
                    const color = colors[Math.floor(Math.random() * colors.length)];
                    particle.style.backgroundColor = color;
                    particle.style.borderColor = color;
                    
                    particle.style.left = `${Math.random() * 100}%`;
                    particle.style.top = `${Math.random() * 100}%`;
                    
                    this.container.appendChild(particle);
                    
                    this.particles.push({
                        element: particle,
                        x: parseFloat(particle.style.left),
                        y: parseFloat(particle.style.top),
                        vx: (Math.random() - 0.5) * 0.3,
                        vy: (Math.random() - 0.5) * 0.3,
                        color: color,
                        size: size,
                        connections: []
                    });
                }
                
                // Create floating numbers
                for (let i = 0; i < 20; i++) {
                    const number = document.createElement('div');
                    number.className = 'floating-number';
                    number.textContent = this.generateStockNumber();
                    number.style.left = `${Math.random() * 100}%`;
                    number.style.top = `${Math.random() * 100}%`;
                    
                    this.container.appendChild(number);
                    
                    this.particles.push({
                        element: number,
                        x: parseFloat(number.style.left),
                        y: parseFloat(number.style.top),
                        vx: (Math.random() - 0.5) * 0.2,
                        vy: (Math.random() - 0.5) * 0.2,
                        isNumber: true,
                        value: number.textContent
                    });
                }
            }
            
            generateStockNumber() {
                const symbols = ['AAPL', 'TSLA', 'MSFT', 'GOOGL', 'AMZN', 'NVDA', 'META'];
                const symbol = symbols[Math.floor(Math.random() * symbols.length)];
                const change = (Math.random() > 0.5 ? '+' : '-') + 
                             (Math.random() * 5).toFixed(2) + '%';
                return `${symbol} ${change}`;
            }
            
            updateParticles() {
                const containerWidth = this.container.clientWidth;
                const containerHeight = this.container.clientHeight;
                
                this.particles.forEach(particle => {
                    // Update position
                    particle.x += particle.vx;
                    particle.y += particle.vy;
                    
                    // Bounce off walls
                    if (particle.x < 0 || particle.x > 100) particle.vx *= -1;
                    if (particle.y < 0 || particle.y > 100) particle.vy *= -1;
                    
                    // Keep within bounds
                    particle.x = Math.max(0, Math.min(100, particle.x));
                    particle.y = Math.max(0, Math.min(100, particle.y));
                    
                    // Update element position
                    particle.element.style.left = `${particle.x}%`;
                    particle.element.style.top = `${particle.y}%`;
                    
                    // Update number values occasionally
                    if (particle.isNumber && Math.random() < 0.02) {
                        particle.value = this.generateStockNumber();
                        particle.element.textContent = particle.value;
                    }
                });
                
                // Create connections between nearby particles
                this.updateConnections();
            }
            
            updateConnections() {
                // Remove old connections
                this.connections.forEach(conn => conn.element.remove());
                this.connections = [];
                
                // Create new connections between nearby particles
                for (let i = 0; i < this.particles.length; i++) {
                    for (let j = i + 1; j < this.particles.length; j++) {
                        const p1 = this.particles[i];
                        const p2 = this.particles[j];
                        
                        // Calculate distance
                        const dx = (p2.x - p1.x) * this.container.clientWidth / 100;
                        const dy = (p2.y - p1.y) * this.container.clientHeight / 100;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        
                        // Connect if particles are close enough
                        if (distance < 150 && Math.random() < 0.3) {
                            const connection = document.createElement('div');
                            connection.className = 'connection-line';
                            
                            const angle = Math.atan2(dy, dx);
                            const length = distance;
                            
                            connection.style.width = `${length}px`;
                            connection.style.left = `${p1.x}%`;
                            connection.style.top = `${p1.y}%`;
                            connection.style.transform = `rotate(${angle}rad)`;
                            connection.style.opacity = (1 - distance / 150) * 0.5;
                            
                            // Use gradient based on particle colors
                            if (!p1.isNumber && !p2.isNumber) {
                                connection.style.background = 
                                    `linear-gradient(90deg, ${p1.color}, ${p2.color})`;
                            }
                            
                            this.container.appendChild(connection);
                            this.connections.push({
                                element: connection,
                                p1: p1,
                                p2: p2
                            });
                        }
                    }
                }
            }
            
            animate() {
                this.updateParticles();
                requestAnimationFrame(() => this.animate());
            }
        }
        
        // Live Ticker
        class LiveTicker {
            constructor(elementId) {
                this.element = document.getElementById(elementId);
                this.symbols = [
                    { symbol: 'AAPL', price: 182.63, change: 1.24 },
                    { symbol: 'TSLA', price: 204.99, change: -2.31 },
                    { symbol: 'MSFT', price: 404.87, change: 0.87 },
                    { symbol: 'NVDA', price: 880.08, change: 12.45 },
                    { symbol: 'GOOGL', price: 141.15, change: -0.32 }
                ];
                this.currentIndex = 0;
                this.start();
            }
            
            start() {
                this.update();
                setInterval(() => this.update(), 3000);
            }
            
            update() {
                const data = this.symbols[this.currentIndex];
                const isPositive = data.change >= 0;
                const changeColor = isPositive ? 'text-success' : 'text-danger';
                const changeIcon = isPositive ? '↗' : '↘';
                
                this.element.innerHTML = `
                    <span class="${changeColor} fw-bold">${data.symbol} $${data.price.toFixed(2)}</span>
                    <span class="${changeColor} ms-2">
                        ${changeIcon} ${Math.abs(data.change).toFixed(2)} (${(Math.abs(data.change)/data.price*100).toFixed(2)}%)
                    </span>
                `;
                
                this.currentIndex = (this.currentIndex + 1) % this.symbols.length;
            }
        }
        
        // Initialize effects when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize stock chart
            new StockChart('stockChart');
            
            // Initialize neural network effect
            new NeuralNetworkEffect('neuralNetwork');
            
            // Initialize live ticker
            new LiveTicker('liveTicker');
            
            // Original API consumer functionality
            fetchLiveSignalPreview();
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