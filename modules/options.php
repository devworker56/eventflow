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
    <h3 class="text-light mb-4">
        <i class="bi bi-activity text-nasdaq-blue me-2"></i>Options Intelligence for Stock Predictions
    </h3>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-dark">
                <div class="card-header border-nasdaq-blue d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">IV Surface Scanner</h5>
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
                        <input type="range" class="form-range" id="ivThreshold" min="0" max="100" value="70">
                        <div class="d-flex justify-content-between">
                            <small>0%</small>
                            <small id="ivValue">70%</small>
                            <small>100%</small>
                        </div>
                    </div>
                    
                    <div id="ivScanResults" class="mt-3">
                        <!-- Results will appear here -->
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card bg-dark">
                <div class="card-header border-nasdaq-blue">
                    <h5 class="mb-0">Put/Call Ratio Analysis</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Track options flow to identify institutional positioning</p>
                    
                    <div id="pcrChart" style="height: 200px;">
                        <!-- Chart will be loaded -->
                    </div>
                    
                    <div class="mt-3">
                        <h6>Top PCR Signals</h6>
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
                <div class="card-header border-nasdaq-blue">
                    <h5 class="mb-0">Equity Signals from Options Data</h5>
                </div>
                <div class="card-body">
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
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function initializeOptionsModule() {
    loadOptionsEquitySignals();
    updatePCRChart();
    
    // IV threshold slider
    document.getElementById('ivThreshold').addEventListener('input', function() {
        document.getElementById('ivValue').textContent = this.value + '%';
    });
}

function scanIVSurfaces() {
    const sector = document.getElementById('sectorSelect').value;
    const threshold = document.getElementById('ivThreshold').value;
    
    fetch(`../api/scan-iv-surface.php?sector=${sector}&threshold=${threshold}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                renderIVScanResults(data.results);
            }
        });
}

function loadOptionsEquitySignals() {
    fetch('../api/get-options-equity-signals.php')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                renderOptionsEquitySignals(data.signals);
            }
        });
}

function renderOptionsEquitySignals(signals) {
    const tbody = document.getElementById('optionsEquitySignals');
    tbody.innerHTML = signals.map(signal => `
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
                <button class="btn btn-sm btn-outline-nasdaq-blue" onclick="analyzeStock('${signal.symbol}')">
                    Analyze
                </button>
            </td>
        </tr>
    `).join('');
}

initializeOptionsModule();
</script>