<div class="module-container">
    <h3 class="text-light mb-4">
        <i class="bi bi-currency-exchange text-nasdaq-green me-2"></i>Futures & FX Signals for Equity Timing
    </h3>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-dark">
                <div class="card-header border-nasdaq-green">
                    <h6 class="mb-0">VIX Futures Curve</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">When VIX futures invert, stocks tend to decline within 3-5 days</p>
                    <div id="vixCurveChart" style="height: 150px;"></div>
                    <div class="mt-3">
                        <span class="badge bg-dark">Current Signal: <span id="vixSignal" class="text-success">Neutral</span></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-dark">
                <div class="card-header border-nasdaq-green">
                    <h6 class="mb-0">Sector Futures Correlation</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Which sectors move with commodity futures?</p>
                    <ul class="list-unstyled" id="sectorFuturesList">
                        <!-- Loaded via JavaScript -->
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-dark">
                <div class="card-header border-nasdaq-green">
                    <h6 class="mb-0">Currency Impact Matrix</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">USD strength impacts different sectors differently</p>
                    <div id="currencyImpactMatrix">
                        <!-- Matrix will be loaded -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card bg-dark mb-4">
        <div class="card-header border-nasdaq-green">
            <h5 class="mb-0">Equity Recommendations from Futures Data</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>Futures Signal</th>
                            <th>Impact on Stocks</th>
                            <th>Most Affected</th>
                            <th>Least Affected</th>
                            <th>Time Horizon</th>
                            <th>Confidence</th>
                        </tr>
                    </thead>
                    <tbody id="futuresEquitySignals">
                        <!-- Loaded via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript for futures module would go here
</script>