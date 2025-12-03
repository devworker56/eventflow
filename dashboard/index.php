<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth-functions.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Get user info
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user dashboard settings
$stmt = $pdo->prepare("SELECT * FROM user_dashboard_settings WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$settings = $stmt->fetch();
if(!$settings) {
    // Create default settings
    $stmt = $pdo->prepare("INSERT INTO user_dashboard_settings (user_id) VALUES (?)");
    $stmt->execute([$_SESSION['user_id']]);
    $settings = ['layout' => '{"default": "grid"}'];
}

// Get signals based on user tier
$tier = $user['subscription_tier'];
$stmt = $pdo->prepare("
    SELECT s.*, e.title, e.scheduled_time 
    FROM signals s
    JOIN market_events e ON s.event_id = e.id
    WHERE s.tier_visibility = ? OR s.tier_visibility = 'explorer'
    ORDER BY s.generated_at DESC 
    LIMIT 20
");
$stmt->execute([$tier]);
$signals = $stmt->fetchAll();

// Get upcoming events
$stmt = $pdo->query("
    SELECT * FROM market_events 
    WHERE scheduled_time > NOW() 
    ORDER BY scheduled_time ASC 
    LIMIT 10
");
$events = $stmt->fetchAll();

// Determine accessible modules based on tier
$accessibleModules = [
    'explorer' => ['events', 'basic_signals', 'sentiment'],
    'professional' => ['events', 'signals', 'cross_asset', 'temporal', 'hedging'],
    'institutional' => ['events', 'signals', 'cross_asset', 'temporal', 'ripple', 'hedging', 'liquidity']
];
$userModules = $accessibleModules[$tier];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EventFlow Institutional</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="../assets/css/custom.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .signal-card {
            border-left: 4px solid var(--nasdaq-blue);
            transition: transform 0.2s;
        }
        .signal-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 145, 218, 0.2);
        }
        .confidence-high { border-left-color: var(--nasdaq-green) !important; }
        .confidence-medium { border-left-color: var(--nasdaq-blue) !important; }
        .confidence-low { border-left-color: var(--nasdaq-red) !important; }
        .module-card { min-height: 200px; }
    </style>
</head>
<body>
    <!-- Fixed Dashboard Header -->
    <nav class="navbar navbar-dark bg-dark border-bottom border-nasdaq-blue">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <span class="text-nasdaq-blue">EventFlow</span> Dashboard
            </a>
            
            <div class="d-flex align-items-center">
                <!-- Tier Badge -->
                <span class="badge bg-nasdaq-blue me-3">
                    <?php echo ucfirst($tier); ?> Tier
                </span>
                
                <!-- Refresh Button -->
                <button id="refreshBtn" class="btn btn-sm btn-outline-nasdaq-blue me-3">
                    <i class="bi bi-arrow-clockwise"></i>
                    <span id="refreshTimer">30s</span>
                </button>
                
                <!-- User Menu -->
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-2"></i>
                        <?php echo htmlspecialchars($user['email']); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header"><?php echo ucfirst($tier); ?> Account</h6></li>
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li><a class="dropdown-item" href="../subscription/"><i class="bi bi-credit-card me-2"></i>Subscription</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Dashboard Container -->
    <div class="container-fluid mt-3">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2">
                <div class="card bg-dark mb-3">
                    <div class="card-body">
                        <h6 class="card-title text-nasdaq-blue mb-3">Modules</h6>
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action bg-dark text-light active" data-module="overview">
                                <i class="bi bi-speedometer2 me-2"></i>Overview
                            </a>
                            
                            <?php if(in_array('events', $userModules)): ?>
                            <a href="#" class="list-group-item list-group-item-action bg-dark text-light" data-module="events">
                                <i class="bi bi-calendar-event me-2"></i>Event Calendar
                            </a>
                            <?php endif; ?>
                            
                            <?php if(in_array('signals', $userModules)): ?>
                            <a href="#" class="list-group-item list-group-item-action bg-dark text-light" data-module="signals">
                                <i class="bi bi-lightning-charge me-2"></i>AI Signals
                            </a>
                            <?php endif; ?>
                            
                            <?php if(in_array('cross_asset', $userModules)): ?>
                            <a href="#" class="list-group-item list-group-item-action bg-dark text-light" data-module="cross_asset">
                                <i class="bi bi-diagram-3 me-2"></i>Cross-Asset
                            </a>
                            <?php endif; ?>
                            
                            <?php if(in_array('temporal', $userModules)): ?>
                            <a href="#" class="list-group-item list-group-item-action bg-dark text-light" data-module="temporal">
                                <i class="bi bi-clock-history me-2"></i>Temporal Analysis
                            </a>
                            <?php endif; ?>
                            
                            <?php if(in_array('ripple', $userModules)): ?>
                            <a href="#" class="list-group-item list-group-item-action bg-dark text-light" data-module="ripple">
                                <i class="bi bi-share me-2"></i>Ripple Effects
                            </a>
                            <?php endif; ?>
                            
                            <?php if(in_array('hedging', $userModules)): ?>
                            <a href="#" class="list-group-item list-group-item-action bg-dark text-light" data-module="hedging">
                                <i class="bi bi-shield-check me-2"></i>Hedging
                            </a>
                            <?php endif; ?>
                            
                            <?php if(in_array('liquidity', $userModules)): ?>
                            <a href="#" class="list-group-item list-group-item-action bg-dark text-light" data-module="liquidity">
                                <i class="bi bi-cash-stack me-2"></i>Liquidity
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="card bg-dark">
                    <div class="card-body">
                        <h6 class="card-title text-nasdaq-blue mb-3">Quick Stats</h6>
                        <div class="mb-2">
                            <small class="text-muted">Active Events</small>
                            <div class="fw-bold"><?php echo count($events); ?></div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Signals Today</small>
                            <div class="fw-bold"><?php echo count($signals); ?></div>
                        </div>
                        <div>
                            <small class="text-muted">API Calls</small>
                            <div class="fw-bold"><?php echo $tier == 'professional' ? '10,000' : ($tier == 'institutional' ? 'Unlimited' : '1,000'); ?>/mo</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-10">
                <!-- Module Content Container -->
                <div id="moduleContent">
                    <!-- Default Overview Module -->
                    <div class="module-container" id="overviewModule">
                        <div class="row mb-4">
                            <div class="col">
                                <h3 class="text-light">Dashboard Overview</h3>
                                <p class="text-muted">Welcome back! Here's your market intelligence summary.</p>
                            </div>
                        </div>
                        
                        <!-- Signal Feed -->
                        <div class="row mb-4">
                            <div class="col">
                                <div class="card bg-dark">
                                    <div class="card-header border-nasdaq-blue d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Recent AI Signals</h5>
                                        <small class="text-muted">Powered by Modal.com AI Engine</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php foreach(array_slice($signals, 0, 6) as $signal): 
                                                $confidence = $signal['confidence_score'];
                                                $confidenceClass = $confidence >= 0.7 ? 'confidence-high' : ($confidence >= 0.5 ? 'confidence-medium' : 'confidence-low');
                                            ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="card signal-card <?php echo $confidenceClass; ?>">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h6 class="card-title mb-0"><?php echo htmlspecialchars($signal['title']); ?></h6>
                                                            <span class="badge bg-<?php echo $confidence >= 0.7 ? 'success' : ($confidence >= 0.5 ? 'primary' : 'danger'); ?>">
                                                                <?php echo round($confidence * 100); ?>%
                                                            </span>
                                                        </div>
                                                        <p class="card-text small text-muted mb-2">
                                                            <?php echo date('M j, H:i', strtotime($signal['generated_at'])); ?>
                                                        </p>
                                                        <button class="btn btn-sm btn-outline-nasdaq-blue w-100" 
                                                                onclick="viewSignalDetails(<?php echo $signal['id']; ?>)">
                                                            View Details
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Upcoming Events -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-dark h-100">
                                    <div class="card-header border-nasdaq-blue">
                                        <h5 class="mb-0">Upcoming Events</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush">
                                            <?php foreach($events as $event): ?>
                                            <div class="list-group-item bg-dark text-light border-secondary">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h6>
                                                    <small class="text-nasdaq-blue">
                                                        <?php echo date('H:i', strtotime($event['scheduled_time'])); ?>
                                                    </small>
                                                </div>
                                                <small class="text-muted">
                                                    <?php 
                                                    $assetClasses = json_decode($event['asset_classes'], true);
                                                    echo implode(', ', array_slice($assetClasses, 0, 3));
                                                    ?>
                                                </small>
                                                <div class="mt-2">
                                                    <span class="badge bg-dark">Impact: <?php echo round($event['importance_score'] * 100); ?>%</span>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Performance Chart -->
                            <div class="col-md-6">
                                <div class="card bg-dark h-100">
                                    <div class="card-header border-nasdaq-blue">
                                        <h5 class="mb-0">Signal Performance</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="performanceChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Focused Module Access Cards -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card module-card bg-dark border-nasdaq-blue">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-activity text-nasdaq-blue" style="font-size: 2rem;"></i>
                                        </div>
                                        <h6>Options Intelligence</h6>
                                        <p class="small text-muted">IV surfaces, skew, put/call ratios for stock predictions</p>
                                        <button class="btn btn-sm btn-outline-nasdaq-blue" onclick="loadModule('options')">
                                            <i class="bi bi-arrow-right me-1"></i>Analyze
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card module-card bg-dark border-nasdaq-green">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-currency-exchange text-nasdaq-green" style="font-size: 2rem;"></i>
                                        </div>
                                        <h6>Futures & FX Signals</h6>
                                        <p class="small text-muted">Term structure, VIX futures, currency correlations</p>
                                        <button class="btn btn-sm btn-outline-nasdaq-green" onclick="loadModule('futures')">
                                            <i class="bi bi-arrow-right me-1"></i>Analyze
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card module-card bg-dark border-nasdaq-light-blue">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-cash-coin text-nasdaq-light-blue" style="font-size: 2rem;"></i>
                                        </div>
                                        <h6>Fixed Income Alpha</h6>
                                        <p class="small text-muted">Credit spreads, yield curves, rate sensitivity</p>
                                        <button class="btn btn-sm btn-outline-nasdaq-light-blue" onclick="loadModule('fixed_income')">
                                            <i class="bi bi-arrow-right me-1"></i>Analyze
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Other modules will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal for Signal Details -->
    <div class="modal fade" id="signalModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark">
                <div class="modal-header border-nasdaq-blue">
                    <h5 class="modal-title">Signal Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="signalModalBody">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/dashboard.js"></script>
    <script>
        // Initialize performance chart
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const performanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Signal Accuracy',
                    data: [65, 72, 78, 75, 82, 85],
                    borderColor: 'rgb(0, 145, 218)',
                    backgroundColor: 'rgba(0, 145, 218, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: { color: '#fff' }
                    }
                },
                scales: {
                    y: {
                        ticks: { color: '#fff' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    },
                    x: {
                        ticks: { color: '#fff' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    }
                }
            }
        });
        
        // Module switching
        document.querySelectorAll('[data-module]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const module = this.dataset.module;
                loadModule(module);
                
                // Update active state
                document.querySelectorAll('[data-module]').forEach(item => {
                    item.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
        
        function loadModule(module) {
            fetch(`modules/${module}.php`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('moduleContent').innerHTML = html;
                    initializeModuleCharts(module);
                })
                .catch(error => {
                    console.error('Error loading module:', error);
                    document.getElementById('moduleContent').innerHTML = 
                        '<div class="alert alert-danger">Error loading module. Please try again.</div>';
                });
        }
        
        function viewSignalDetails(signalId) {
            fetch(`../api/get-signal-details.php?id=${signalId}`)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('signalModalBody').innerHTML = data.html;
                        const modal = new bootstrap.Modal(document.getElementById('signalModal'));
                        modal.show();
                    }
                });
        }
        
        // Auto-refresh timer
        let refreshSeconds = 30;
        const refreshTimer = setInterval(() => {
            refreshSeconds--;
            document.getElementById('refreshTimer').textContent = refreshSeconds + 's';
            
            if(refreshSeconds <= 0) {
                refreshDashboard();
                refreshSeconds = 30;
            }
        }, 1000);
        
        document.getElementById('refreshBtn').addEventListener('click', function() {
            refreshDashboard();
            refreshSeconds = 30;
        });
        
        function refreshDashboard() {
            // Implement dashboard refresh logic
            console.log('Refreshing dashboard...');
            // You can reload specific components or the entire page
        }
    </script>
</body>
</html>