<?php
require_once '../config/database.php';
require_once '../includes/auth-functions.php';

requireLogin();
$userTier = getUserTier();

// Define which tiers can access this module
// Based on your description: Standard has basic options, Pro has advanced
$allowedTiers = ['standard', 'pro', 'premium'];

if(!in_array($userTier, $allowedTiers)) {
    echo '<div class="alert alert-warning">
            <i class="bi bi-shield-lock me-2"></i>
            <strong>Feature Not Available</strong><br>
            Options Intelligence requires at least Standard tier access.<br>
            <a href="../subscription/" class="btn btn-sm btn-outline-warning mt-2">View Plans</a>
          </div>';
    return;
}

// Set tier-specific limitations
$isStandard = ($userTier === 'standard');
$isPro = ($userTier === 'pro');
$isPremium = ($userTier === 'premium');

// Tier-specific configuration
if($isStandard) {
    $stockLimit = 10; // Standard tier limited to 10 stocks
    $refreshRate = 60; // 60 second refresh rate (delayed)
    $historicalDays = 7; // 7 days of historical data
    $maxStocksInTable = 5; // Show only 5 stocks in table
    $hasIVScanner = false; // No IV scanner for Standard
    $hasPCRAnalysis = true; // Basic PCR analysis only
} elseif($isPro) {
    $stockLimit = 50; // Pro tier up to 50 stocks
    $refreshRate = 30; // 30 second refresh rate
    $historicalDays = 30; // 30 days of historical data
    $maxStocksInTable = 20; // Show 20 stocks in table
    $hasIVScanner = true; // Full IV scanner for Pro
    $hasPCRAnalysis = true; // Full PCR analysis
} else { // Premium
    $stockLimit = 500; // Premium tier up to 500 stocks
    $refreshRate = 10; // 10 second refresh rate (real-time)
    $historicalDays = 365; // 1 year of historical data
    $maxStocksInTable = 50; // Show 50 stocks in table
    $hasIVScanner = true; // Full IV scanner for Premium
    $hasPCRAnalysis = true; // Full PCR analysis
}
?>
<div class="module-container">
    <h3 class="text-light mb-4">
        <i class="bi bi-activity text-nasdaq-blue me-2"></i>Options Intelligence for Stock Predictions
        <?php if($isStandard): ?>
        <span class="badge bg-secondary ms-2">BASIC</span>
        <small class="text-muted ms-2">Limited to <?php echo $stockLimit; ?> stocks</small>
        <?php elseif($isPro): ?>
        <span class="badge bg-nasdaq-blue ms-2">PRO</span>
        <?php else: ?>
        <span class="badge bg-nasdaq-green ms-2">PREMIUM</span>
        <?php endif; ?>
    </h3>
    
    <!-- Tier Info Banner -->
    <div class="alert alert-dark border-nasdaq-blue mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-info-circle text-nasdaq-blue me-2"></i>
                <small>
                    <?php if($isStandard): ?>
                    Standard Tier: Basic options analysis (<?php echo $stockLimit; ?> stocks) with delayed data
                    <?php elseif($isPro): ?>
                    Pro Tier: Advanced options scanning with <?php echo $stockLimit; ?> stock limit
                    <?php else: ?>
                    Premium Tier: Unlimited options analysis with real-time data
                    <?php endif; ?>
                </small>
            </div>
            <?php if($isStandard || $isPro): ?>
            <a href="../subscription/" class="btn btn-sm btn-outline-nasdaq-blue">
                <i class="bi bi-arrow-up-circle me-1"></i> Upgrade Plan
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="row mb-4">
        <?php if($hasIVScanner): ?>
        <!-- IV Scanner (Pro+ only) -->
        <div class="col-md-6">
            <div class="card bg-dark">
                <div class="card-header border-nasdaq-blue d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">IV Surface Scanner</h5>
                        <small class="text-muted">
                            <?php if($isPro): ?>
                            Pro: Up to <?php echo $stockLimit; ?> stocks
                            <?php else: ?>
                            Premium: Unlimited scanning
                            <?php endif; ?>
                        </small>
                    </div>
                    <button class="btn btn-sm btn-outline-nasdaq-blue" onclick="scanIVSurfaces()">
                        <i class="bi bi-search me-1"></i>Scan
                    </button>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Find stocks where options volatility is mispriced relative to expected moves</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Sector Focus</label>
                        <select class="form-select bg-dark" id="sectorSelect">
                            <option value="all">All Sectors</option>
                            <option value="tech">Technology</option>
                            <option value="healthcare">Healthcare</option>
                            <option value="financial">Financial</option>
                            <option value="energy">Energy</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">IV Percentile Threshold</label>
                        <input type="range" class="form-range" id="ivThreshold" min="0" max="100" 
                               value="<?php echo $isPro ? '70' : '50'; ?>"
                               <?php echo $isPro ? '' : 'disabled'; ?>>
                        <div class="d-flex justify-content-between">
                            <small>0%</small>
                            <small id="ivValue"><?php echo $isPro ? '70%' : 'N/A'; ?></small>
                            <small>100%</small>
                        </div>
                        <?php if(!$isPro): ?>
                        <small class="text-warning"><i class="bi bi-lock me-1"></i> Advanced thresholds require Pro+</small>
                        <?php endif; ?>
                    </div>
                    
                    <div id="ivScanResults" class="mt-3">
                        <!-- Results will appear here -->
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Standard Tier Limited View -->
        <div class="col-md-6">
            <div class="card bg-dark border-secondary">
                <div class="card-header border-secondary">
                    <h5 class="mb-0">Options Analysis (Standard)</h5>
                    <small class="text-muted">Basic analysis for <?php echo $stockLimit; ?> stocks</small>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        <i class="bi bi-info-circle me-2"></i>
                        Advanced IV Surface Scanner requires <strong>Pro tier</strong> or higher.
                    </p>
                    
                    <div class="text-center py-4">
                        <i class="bi bi-activity text-muted" style="font-size: 3rem;"></i>
                        <p class="mt-3">Upgrade to Pro for advanced options intelligence</p>
                        <a href="../subscription/" class="btn btn-sm btn-outline-nasdaq-blue">
                            <i class="bi bi-arrow-up-circle me-1"></i> Upgrade to Pro
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Put/Call Ratio Analysis (All tiers) -->
        <div class="col-md-6">
            <div class="card bg-dark">
                <div class="card-header border-nasdaq-blue d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Put/Call Ratio Analysis</h5>
                        <small class="text-muted">
                            <?php if($isStandard): ?>
                            Standard: Basic analysis
                            <?php elseif($isPro): ?>
                            Pro: Advanced analysis
                            <?php else: ?>
                            Premium: Real-time analysis
                            <?php endif; ?>
                        </small>
                    </div>
                    <?php if(!$isStandard): ?>
                    <button class="btn btn-sm btn-outline-nasdaq-blue" onclick="refreshPCRData()">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Track options flow to identify institutional positioning</p>
                    
                    <div id="pcrChart" style="height: 200px;">
                        <!-- Chart will be loaded -->
                        <div class="text-center py-4">
                            <?php if($isStandard): ?>
                            <i class="bi bi-clock-history text-muted me-2"></i>
                            <span class="text-muted">Delayed data (30 min)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6>Top PCR Signals</h6>
                        <small class="text-muted">
                            Showing top <?php echo $isStandard ? 3 : ($isPro ? 5 : 10); ?> signals
                        </small>
                        <div id="pcrSignals" class="mt-2">
                            <!-- Signals will appear here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark">
                <div class="card-header border-nasdaq-blue d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Equity Signals from Options Data</h5>
                        <small class="text-muted">
                            <?php if($isStandard): ?>
                            Standard: Top <?php echo $maxStocksInTable; ?> stocks (delayed)
                            <?php elseif($isPro): ?>
                            Pro: Top <?php echo $maxStocksInTable; ?> stocks
                            <?php else: ?>
                            Premium: All signals (real-time)
                            <?php endif; ?>
                        </small>
                    </div>
                    <div>
                        <?php if($isStandard): ?>
                        <span class="badge bg-dark me-2"><i class="bi bi-clock me-1"></i> 30-min delay</span>
                        <?php elseif($isPro): ?>
                        <span class="badge bg-nasdaq-blue me-2">Real-time</span>
                        <?php else: ?>
                        <span class="badge bg-nasdaq-green me-2">Real-time</span>
                        <?php endif; ?>
                        <button class="btn btn-sm btn-outline-nasdaq-blue" onclick="loadOptionsEquitySignals()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($isStandard && $stockLimit < 5): ?>
                    <div class="alert alert-dark border-nasdaq-blue mb-3">
                        <i class="bi bi-info-circle text-nasdaq-blue me-2"></i>
                        <small>Standard tier limited to <?php echo $stockLimit; ?> stocks. 
                        <a href="../subscription/" class="text-nasdaq-blue">Upgrade to Pro</a> for full coverage.</small>
                    </div>
                    <?php endif; ?>
                    
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>Stock</th>
                                <th>Current IV</th>
                                <th>IV Percentile</th>
                                <th>PCR Signal</th>
                                <th>Expected Move</th>
                                <th>Our Prediction</th>
                                <th>Edge</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="optionsEquitySignals">
                            <!-- Data loaded via JavaScript -->
                        </tbody>
                    </table>
                    
                    <?php if($isPro && $maxStocksInTable < 50): ?>
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Showing top <?php echo $maxStocksInTable; ?> signals. 
                            <a href="../subscription/" class="text-nasdaq-blue">Upgrade to Premium</a> for unlimited signals.
                        </small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Tier-specific configuration
const TIER_CONFIG = {
    tier: '<?php echo $userTier; ?>',
    isStandard: <?php echo $isStandard ? 'true' : 'false'; ?>,
    isPro: <?php echo $isPro ? 'true' : 'false'; ?>,
    isPremium: <?php echo $isPremium ? 'true' : 'false'; ?>,
    stockLimit: <?php echo $stockLimit; ?>,
    refreshRate: <?php echo $refreshRate; ?>,
    historicalDays: <?php echo $historicalDays; ?>,
    maxStocksInTable: <?php echo $maxStocksInTable; ?>,
    hasIVScanner: <?php echo $hasIVScanner ? 'true' : 'false'; ?>,
    hasPCRAnalysis: <?php echo $hasPCRAnalysis ? 'true' : 'false'; ?>
};

function initializeOptionsModule() {
    console.log('Initializing Options Module for', TIER_CONFIG.tier, 'tier');
    
    // Apply tier-specific UI adjustments
    if(TIER_CONFIG.isStandard) {
        document.getElementById('ivThreshold').disabled = true;
    }
    
    loadOptionsEquitySignals();
    updatePCRChart();
    
    // IV threshold slider
    const ivSlider = document.getElementById('ivThreshold');
    if(ivSlider) {
        ivSlider.addEventListener('input', function() {
            document.getElementById('ivValue').textContent = this.value + '%';
        });
    }
    
    // Auto-refresh for Pro and Premium tiers
    if(!TIER_CONFIG.isStandard && TIER_CONFIG.refreshRate > 0) {
        setInterval(() => {
            if(!document.hidden) {
                loadOptionsEquitySignals();
            }
        }, TIER_CONFIG.refreshRate * 1000);
    }
}

function scanIVSurfaces() {
    if(TIER_CONFIG.isStandard) {
        alert('IV Surface Scanner requires Pro or Premium tier. Please upgrade your plan.');
        return;
    }
    
    const sector = document.getElementById('sectorSelect').value;
    const threshold = document.getElementById('ivThreshold').value;
    
    const params = new URLSearchParams({
        sector: sector,
        threshold: threshold,
        tier: TIER_CONFIG.tier,
        limit: TIER_CONFIG.stockLimit
    });
    
    fetch(`../api/scan-iv-surface.php?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                if(data.tier_limited && TIER_CONFIG.isPro) {
                    showTierLimitWarning(data.message);
                }
                renderIVScanResults(data.results);
            } else if(data.error === 'tier_limit') {
                showTierLimitWarning(data.message);
            }
        });
}

function loadOptionsEquitySignals() {
    const params = new URLSearchParams({
        tier: TIER_CONFIG.tier,
        limit: TIER_CONFIG.maxStocksInTable,
        premium: TIER_CONFIG.isPremium ? '1' : '0'
    });
    
    fetch(`../api/get-options-equity-signals.php?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                if(data.tier_limited) {
                    showTierLimitWarning(data.message);
                }
                renderOptionsEquitySignals(data.signals);
            } else if(data.error === 'tier_limit') {
                document.getElementById('optionsEquitySignals').innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                ${data.message}<br>
                                <a href="../subscription/" class="btn btn-sm btn-outline-warning mt-2">Upgrade Plan</a>
                            </div>
                        </td>
                    </tr>`;
            }
        });
}

function renderOptionsEquitySignals(signals) {
    const tbody = document.getElementById('optionsEquitySignals');
    
    if(!signals || signals.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="alert alert-dark">
                        <i class="bi bi-info-circle me-2"></i>
                        No signals found. ${TIER_CONFIG.isStandard ? 'Standard tier has limited coverage.' : 'Try broadening your search criteria.'}
                    </div>
                </td>
            </tr>`;
        return;
    }
    
    tbody.innerHTML = signals.slice(0, TIER_CONFIG.maxStocksInTable).map(signal => `
        <tr>
            <td>
                <strong>${signal.symbol}</strong><br>
                <small class="text-muted">${signal.event || 'No event'}</small>
            </td>
            <td>${signal.current_iv}%</td>
            <td>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar ${signal.iv_percentile > 70 ? 'bg-danger' : signal.iv_percentile > 50 ? 'bg-warning' : 'bg-success'}" 
                         style="width: ${signal.iv_percentile}%"></div>
                </div>
                <small>${signal.iv_percentile}%</small>
            </td>
            <td>
                <span class="badge ${signal.pcr_signal === 'bullish' ? 'bg-success' : 'bg-danger'}">
                    ${signal.pcr_signal}
                </span>
            </td>
            <td>${signal.expected_move}</td>
            <td>
                <span class="${signal.prediction_direction === 'up' ? 'text-success' : 'text-danger'}">
                    ${signal.prediction_direction === 'up' ? '↑' : '↓'} ${signal.prediction_magnitude}
                </span>
            </td>
            <td>
                <span class="badge ${signal.edge > 0.2 ? 'bg-success' : signal.edge > 0.1 ? 'bg-warning' : 'bg-secondary'}">
                    ${(signal.edge * 100).toFixed(1)}%
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-outline-nasdaq-blue" onclick="analyzeStock('${signal.symbol}')"
                        ${TIER_CONFIG.isStandard && signal.symbol !== 'AAPL' && signal.symbol !== 'MSFT' ? 'disabled' : ''}>
                    ${TIER_CONFIG.isStandard && signal.symbol !== 'AAPL' && signal.symbol !== 'MSFT' ? 'Upgrade to Analyze' : 'Analyze'}
                </button>
            </td>
        </tr>
    `).join('');
    
    // Add tier limit warning if we truncated results
    if(signals.length > TIER_CONFIG.maxStocksInTable && !TIER_CONFIG.isPremium) {
        const upgradeTier = TIER_CONFIG.isStandard ? 'Pro' : 'Premium';
        tbody.innerHTML += `
            <tr>
                <td colspan="8" class="text-center py-3 bg-dark">
                    <small class="text-warning">
                        <i class="bi bi-info-circle me-1"></i>
                        Showing top ${TIER_CONFIG.maxStocksInTable} of ${signals.length} signals. 
                        <a href="../subscription/" class="text-nasdaq-blue">Upgrade to ${upgradeTier}</a> for full access.
                    </small>
                </td>
            </tr>`;
    }
}

function updatePCRChart() {
    const params = new URLSearchParams({
        tier: TIER_CONFIG.tier,
        days: TIER_CONFIG.historicalDays
    });
    
    fetch(`../api/get-pcr-data.php?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                renderPCRChart(data.pcrData);
                renderPCRSignals(data.signals);
            }
        });
}

function refreshPCRData() {
    updatePCRChart();
}

function analyzeStock(symbol) {
    if(TIER_CONFIG.isStandard && !['AAPL', 'MSFT', 'GOOGL', 'AMZN', 'TSLA'].includes(symbol)) {
        alert('Standard tier limited to top 5 stocks. Upgrade to Pro for full stock analysis.');
        return;
    }
    
    window.location.href = `../dashboard/?module=stock_detail&symbol=${symbol}`;
}

function showTierLimitWarning(message) {
    const warningHTML = `
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-shield-lock me-2"></i>
            <strong>Tier Limit</strong><br>
            ${message}
            <div class="mt-2">
                <a href="../subscription/" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-arrow-up-circle me-1"></i> Upgrade Plan
                </a>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insert at top of module
    const moduleContainer = document.querySelector('.module-container');
    const existingAlert = moduleContainer.querySelector('.alert-warning');
    if(existingAlert) {
        existingAlert.remove();
    }
    moduleContainer.insertAdjacentHTML('afterbegin', warningHTML);
}

// Initialize module
initializeOptionsModule();
</script>