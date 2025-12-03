<?php
require_once '../config/database.php';
require_once '../includes/auth-functions.php';

requireLogin();
$userTier = getUserTier();

// Define which tiers can access this module
// Based on your description: Pro and Premium have access
$allowedTiers = ['pro', 'premium'];

if(!in_array($userTier, $allowedTiers)) {
    echo '<div class="alert alert-warning">
            <i class="bi bi-shield-lock me-2"></i>
            <strong>Advanced Feature Required</strong><br>
            Cross-Asset Intelligence is available on Pro and Premium tiers.<br>
            <a href="../subscription/" class="btn btn-sm btn-outline-warning mt-2">Upgrade Subscription</a>
          </div>';
    return;
}

// Additional tier-specific limitations
$isPremium = ($userTier === 'premium');
$isPro = ($userTier === 'pro');

// Set limits based on tier
if($isPro) {
    $assetLimit = 10; // Pro tier limited to 10 concurrent assets
    $refreshRate = 30; // 30 second refresh rate
    $historicalDays = 30; // 30 days of historical data
} else {
    // Premium tier gets full access
    $assetLimit = 50; // Premium tier up to 50 concurrent assets
    $refreshRate = 10; // 10 second refresh rate
    $historicalDays = 365; // 1 year of historical data
}
?>
<div class="module-container">
    <h3 class="text-light mb-4">
        Cross-Asset Intelligence
        <?php if($isPro): ?>
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
                    <?php if($isPro): ?>
                    Pro Tier: Analyzing up to <?php echo $assetLimit; ?> assets with <?php echo $historicalDays; ?>-day history
                    <?php else: ?>
                    Premium Tier: Unlimited analysis with <?php echo $historicalDays; ?>-day history
                    <?php endif; ?>
                </small>
            </div>
            <?php if($isPro): ?>
            <a href="../subscription/" class="btn btn-sm btn-outline-nasdaq-blue">
                <i class="bi bi-arrow-up-circle me-1"></i> Upgrade to Premium
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card bg-dark">
                <div class="card-header border-nasdaq-blue d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Asset Correlation Matrix</h5>
                    <small class="text-muted">Real-time updates every <?php echo $refreshRate; ?>s</small>
                </div>
                <div class="card-body">
                    <div id="correlationMatrix" style="height: 400px;">
                        <!-- Chart will be loaded by JavaScript -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-nasdaq-blue" role="status"></div>
                            <p class="mt-3">Loading correlation data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-dark">
                <div class="card-header border-nasdaq-blue">
                    <h5 class="mb-0">Quick Analysis</h5>
                </div>
                <div class="card-body">
                    <form id="assetAnalysisForm">
                        <div class="mb-3">
                            <label class="form-label">Select Asset Classes</label>
                            <?php 
                            // Define available asset classes based on tier
                            $availableAssets = [
                                'equities' => ['Equities', 'standard'],
                                'options' => ['Options', 'standard'],
                                'futures' => ['Futures', 'pro'],
                                'fx' => ['FX', 'pro'],
                                'fixed_income' => ['Fixed Income', 'pro'],
                                'commodities' => ['Commodities', 'premium'],
                                'crypto' => ['Crypto', 'premium']
                            ];
                            
                            foreach($availableAssets as $assetKey => $assetInfo):
                                $assetName = $assetInfo[0];
                                $requiredTier = $assetInfo[1];
                                
                                // Check if user's tier can access this asset class
                                $tierHierarchy = ['standard' => 1, 'pro' => 2, 'premium' => 3];
                                $userTierLevel = $tierHierarchy[$userTier];
                                $requiredTierLevel = $tierHierarchy[$requiredTier];
                                $isDisabled = ($userTierLevel < $requiredTierLevel);
                            ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       name="assets[]" value="<?php echo $assetKey; ?>"
                                       <?php echo ($assetKey === 'equities' || $assetKey === 'options') ? 'checked' : ''; ?>
                                       <?php echo $isDisabled ? 'disabled' : ''; ?>>
                                <label class="form-check-label <?php echo $isDisabled ? 'text-muted' : ''; ?>">
                                    <?php echo $assetName; ?>
                                    <?php if($isDisabled): ?>
                                    <small class="text-warning">(<?php echo ucfirst($requiredTier); ?>+)</small>
                                    <?php endif; ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if($isPro): ?>
                        <div class="mb-3">
                            <label class="form-label">Maximum Assets</label>
                            <input type="range" class="form-range" min="1" max="<?php echo $assetLimit; ?>" value="5" 
                                   oninput="document.getElementById('assetCount').textContent = this.value + ' assets'">
                            <div class="d-flex justify-content-between">
                                <small>1 asset</small>
                                <small id="assetCount">5 assets</small>
                                <small><?php echo $assetLimit; ?> assets</small>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <button type="submit" class="btn btn-nasdaq-blue w-100">
                            <i class="bi bi-lightning-charge me-2"></i>
                            <?php echo $isPro ? 'Analyze (Pro Mode)' : 'Analyze (Premium Mode)'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark">
                <div class="card-header border-nasdaq-blue d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Recent Cross-Asset Signals</h5>
                        <small class="text-muted">Auto-refresh every <?php echo $refreshRate; ?> seconds</small>
                    </div>
                    <div>
                        <?php if($isPro): ?>
                        <span class="badge bg-dark me-2">API: 10,000/mo</span>
                        <?php endif; ?>
                        <button class="btn btn-sm btn-outline-nasdaq-blue" onclick="refreshCrossAssetSignals()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="crossAssetSignals">
                        <!-- Signals loaded by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Tier-specific configuration
const TIER_CONFIG = {
    tier: '<?php echo $userTier; ?>',
    assetLimit: <?php echo $assetLimit; ?>,
    refreshRate: <?php echo $refreshRate; ?>,
    historicalDays: <?php echo $historicalDays; ?>,
    isPremium: <?php echo $isPremium ? 'true' : 'false'; ?>,
    isPro: <?php echo $isPro ? 'true' : 'false'; ?>
};

// Initialize when module loads
function initializeCrossAssetModule() {
    console.log('Initializing Cross-Asset Module for', TIER_CONFIG.tier, 'tier');
    
    // Apply tier-specific UI adjustments
    if(TIER_CONFIG.isPro) {
        document.getElementById('correlationMatrix').style.maxHeight = '400px';
    } else if(TIER_CONFIG.isPremium) {
        document.getElementById('correlationMatrix').style.maxHeight = '600px';
    }
    
    loadCorrelationMatrix();
    loadCrossAssetSignals();
    
    // Auto-refresh based on tier
    if(TIER_CONFIG.refreshRate > 0) {
        setInterval(() => {
            if(!document.hidden) {
                loadCrossAssetSignals();
            }
        }, TIER_CONFIG.refreshRate * 1000);
    }
    
    // Form submission with tier validation
    document.getElementById('assetAnalysisForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const selectedAssets = formData.getAll('assets[]');
        
        // Enforce asset limit for Pro tier
        if(TIER_CONFIG.isPro && selectedAssets.length > TIER_CONFIG.assetLimit) {
            alert(`Pro tier limited to ${TIER_CONFIG.assetLimit} assets. Please select fewer assets or upgrade to Premium.`);
            return;
        }
        
        analyzeAssets(formData);
    });
}

function loadCorrelationMatrix() {
    const params = new URLSearchParams({
        tier: TIER_CONFIG.tier,
        limit: TIER_CONFIG.assetLimit,
        days: TIER_CONFIG.historicalDays
    });
    
    fetch('../api/get-correlation-matrix.php?' + params.toString())
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                if(data.tier_limited && TIER_CONFIG.isPro) {
                    // Show upgrade suggestion for Pro users hitting limits
                    showTierLimitWarning(data.message);
                }
                renderCorrelationMatrix(data.matrix);
            } else if(data.error === 'tier_limit') {
                // Handle tier limit error
                showTierLimitWarning(data.message);
            }
        })
        .catch(error => {
            console.error('Error loading correlation matrix:', error);
        });
}

function loadCrossAssetSignals() {
    const params = new URLSearchParams({
        tier: TIER_CONFIG.tier,
        premium: TIER_CONFIG.isPremium ? '1' : '0'
    });
    
    fetch('../api/get-cross-asset-signals.php?' + params.toString())
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('crossAssetSignals').innerHTML = data.html;
            } else if(data.error === 'api_limit') {
                // Handle API limit reached
                document.getElementById('crossAssetSignals').innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        API limit reached. <a href="../subscription/">Upgrade</a> for unlimited access.
                    </div>`;
            }
        });
}

function refreshCrossAssetSignals() {
    loadCrossAssetSignals();
}

function analyzeAssets(formData) {
    formData.append('tier', TIER_CONFIG.tier);
    
    fetch('../api/analyze-assets.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            if(data.tier_limited) {
                showTierLimitWarning(data.message);
            } else {
                // Display analysis results
                alert('Analysis complete!');
            }
        } else if(data.error === 'tier_limit') {
            showTierLimitWarning(data.message);
        }
    });
}

function showTierLimitWarning(message) {
    const warningHTML = `
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-shield-lock me-2"></i>
            <strong>Tier Limit</strong><br>
            ${message}
            <div class="mt-2">
                <a href="../subscription/" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-arrow-up-circle me-1"></i> Upgrade to Premium
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
initializeCrossAssetModule();
</script>