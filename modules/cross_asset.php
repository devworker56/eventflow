<?php
require_once '../config/database.php';
require_once '../includes/auth-functions.php';

requireLogin();
$userTier = getUserTier();

if(!in_array($userTier, ['professional', 'institutional'])) {
    echo '<div class="alert alert-warning">This module requires Professional or Institutional tier.</div>';
    return;
}
?>
<div class="module-container">
    <h3 class="text-light mb-4">Cross-Asset Intelligence</h3>
    
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card bg-dark">
                <div class="card-header border-nasdaq-blue">
                    <h5 class="mb-0">Asset Correlation Matrix</h5>
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
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="assets[]" value="equities" checked>
                                <label class="form-check-label">Equities</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="assets[]" value="options" checked>
                                <label class="form-check-label">Options</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="assets[]" value="futures">
                                <label class="form-check-label">Futures</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="assets[]" value="fx">
                                <label class="form-check-label">FX</label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-nasdaq-blue w-100">
                            <i class="bi bi-lightning-charge me-2"></i>Analyze
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
                    <h5 class="mb-0">Recent Cross-Asset Signals</h5>
                    <button class="btn btn-sm btn-outline-nasdaq-blue" onclick="refreshCrossAssetSignals()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
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
// This script will be executed when module loads
function initializeCrossAssetModule() {
    loadCorrelationMatrix();
    loadCrossAssetSignals();
    
    // Form submission
    document.getElementById('assetAnalysisForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        analyzeAssets(formData);
    });
}

function loadCorrelationMatrix() {
    fetch('../api/get-correlation-matrix.php')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                renderCorrelationMatrix(data.matrix);
            }
        });
}

function loadCrossAssetSignals() {
    fetch('../api/get-cross-asset-signals.php')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('crossAssetSignals').innerHTML = data.html;
            }
        });
}

function refreshCrossAssetSignals() {
    loadCrossAssetSignals();
}

// Initialize when module loads
initializeCrossAssetModule();
</script>