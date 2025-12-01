// Modal API Consumer for EventFlow

class EventFlowAPI {
    constructor(apiKey) {
        this.apiKey = apiKey;
        this.baseURL = window.location.origin + '/api/';
        this.modalFunctions = {
            'cross_asset': 'analyze_cross_asset_impact',
            'temporal': 'analyze_temporal_patterns',
            'ripple': 'analyze_network_effects',
            'hedging': 'find_cross_asset_hedges',
            'liquidity': 'stress_test_liquidity'
        };
    }

    // Call Modal API via our backend
    async callModalFunction(functionName, data = {}) {
        try {
            const response = await fetch(this.baseURL + 'call-modal.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-API-Key': this.apiKey
                },
                body: JSON.stringify({
                    function: functionName,
                    data: data
                })
            });

            if (!response.ok) {
                throw new Error(`API call failed: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Modal API Error:', error);
            throw error;
        }
    }

    // Get live signals
    async getLiveSignals(limit = 10) {
        return this.callModalFunction('get_live_signals', { limit });
    }

    // Analyze specific event
    async analyzeEvent(eventId) {
        return this.callModalFunction('analyze_event', { event_id: eventId });
    }

    // Get cross-asset correlations
    async getCrossAssetCorrelations(symbols) {
        return this.callModalFunction('get_cross_asset_correlations', { symbols });
    }

    // Get temporal analysis
    async getTemporalAnalysis(eventId, timeframe = 'all') {
        return this.callModalFunction('get_temporal_analysis', {
            event_id: eventId,
            timeframe: timeframe
        });
    }

    // Get ripple effects
    async getRippleEffects(eventId, depth = 2) {
        return this.callModalFunction('get_ripple_effects', {
            event_id: eventId,
            depth: depth
        });
    }

    // Get hedging suggestions
    async getHedgingSuggestions(portfolio, eventData) {
        return this.callModalFunction('get_hedging_suggestions', {
            portfolio: portfolio,
            event_data: eventData
        });
    }

    // Get liquidity analysis
    async getLiquidityAnalysis(portfolio, scenarios = ['stress', 'normal']) {
        return this.callModalFunction('get_liquidity_analysis', {
            portfolio: portfolio,
            scenarios: scenarios
        });
    }

    // Stream real-time signals (WebSocket)
    connectSignalStream(callback) {
        const ws = new WebSocket(`wss://${window.location.host}/api/signal-stream`);

        ws.onopen = () => {
            ws.send(JSON.stringify({
                action: 'subscribe',
                api_key: this.apiKey
            }));
        };

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            callback(data);
        };

        ws.onerror = (error) => {
            console.error('WebSocket Error:', error);
        };

        return ws;
    }

    // Batch process multiple events
    async batchProcessEvents(eventIds) {
        return this.callModalFunction('batch_process_events', { event_ids: eventIds });
    }
}

// Dashboard-specific utilities
class DashboardManager {
    constructor() {
        this.api = new EventFlowAPI(window.userApiKey || '');
        this.refreshInterval = 30000; // 30 seconds
        this.timer = null;
    }

    initialize() {
        this.loadDashboardData();
        this.setupEventListeners();
        this.startAutoRefresh();
    }

    async loadDashboardData() {
        try {
            // Load signals
            const signals = await this.api.getLiveSignals(6);
            this.updateSignalGrid(signals);

            // Load events
            const events = await this.fetchEvents();
            this.updateEventList(events);

            // Load performance data
            const performance = await this.fetchPerformance();
            this.updatePerformanceChart(performance);
        } catch (error) {
            this.showError('Failed to load dashboard data');
        }
    }

    updateSignalGrid(signals) {
        const container = document.getElementById('signalGrid');
        if (!container) return;

        container.innerHTML = signals.map(signal => `
            <div class="col-md-4 mb-3">
                <div class="card signal-card confidence-${this.getConfidenceClass(signal.confidence)}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">${signal.title}</h6>
                            <span class="badge bg-${this.getConfidenceColor(signal.confidence)}">
                                ${Math.round(signal.confidence * 100)}%
                            </span>
                        </div>
                        <p class="card-text small text-muted mb-2">
                            ${new Date(signal.generated_at).toLocaleString()}
                        </p>
                        <p class="card-text small">${signal.summary}</p>
                        <button class="btn btn-sm btn-outline-nasdaq-blue w-100" 
                                onclick="dashboard.viewSignalDetails(${signal.id})">
                            View Details
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    getConfidenceClass(confidence) {
        if (confidence >= 0.7) return 'high';
        if (confidence >= 0.5) return 'medium';
        return 'low';
    }

    getConfidenceColor(confidence) {
        if (confidence >= 0.7) return 'success';
        if (confidence >= 0.5) return 'primary';
        return 'danger';
    }

    async viewSignalDetails(signalId) {
        try {
            const details = await this.fetchSignalDetails(signalId);
            this.showSignalModal(details);
        } catch (error) {
            this.showError('Failed to load signal details');
        }
    }

    showSignalModal(details) {
        // Implement modal display
        const modal = new bootstrap.Modal(document.getElementById('signalModal'));
        document.getElementById('signalModalBody').innerHTML = this.renderSignalDetails(details);
        modal.show();
    }

    renderSignalDetails(details) {
        return `
            <div class="signal-details">
                <h6>${details.title}</h6>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>Impact Analysis</h6>
                        <ul class="list-unstyled">
                            ${details.impacts.map(impact => `
                                <li class="mb-2">
                                    <span class="badge bg-dark me-2">${impact.asset}</span>
                                    ${impact.direction} ${impact.magnitude}%
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Recommendation</h6>
                        <div class="alert alert-${details.recommendation.type}">
                            <strong>${details.recommendation.action}</strong>
                            <p class="mb-0 mt-2">${details.recommendation.reason}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    setupEventListeners() {
        // Refresh button
        document.getElementById('refreshBtn')?.addEventListener('click', () => {
            this.loadDashboardData();
        });

        // Module navigation
        document.querySelectorAll('[data-module]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const module = e.target.dataset.module;
                this.loadModule(module);
            });
        });
    }

    async loadModule(moduleName) {
        try {
            const response = await fetch(`modules/${moduleName}.php`);
            const html = await response.text();
            document.getElementById('moduleContent').innerHTML = html;
            this.initializeModule(moduleName);
        } catch (error) {
            console.error('Failed to load module:', error);
        }
    }

    initializeModule(moduleName) {
        // Initialize module-specific functionality
        switch (moduleName) {
            case 'cross_asset':
                this.initializeCrossAssetModule();
                break;
            case 'temporal':
                this.initializeTemporalModule();
                break;
            case 'ripple':
                this.initializeRippleModule();
                break;
            case 'hedging':
                this.initializeHedgingModule();
                break;
            case 'liquidity':
                this.initializeLiquidityModule();
                break;
        }
    }

    startAutoRefresh() {
        this.stopAutoRefresh();
        this.timer = setInterval(() => {
            this.loadDashboardData();
        }, this.refreshInterval);
    }

    stopAutoRefresh() {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }

    showError(message) {
        // Show error toast or alert
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-bg-danger border-0';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        new bootstrap.Toast(toast).show();
    }

    // Fetch methods (to be implemented with actual API calls)
    async fetchEvents() {
        const response = await fetch('/api/get-events.php');
        return response.json();
    }

    async fetchPerformance() {
        const response = await fetch('/api/get-performance.php');
        return response.json();
    }

    async fetchSignalDetails(signalId) {
        const response = await fetch(`/api/get-signal-details.php?id=${signalId}`);
        return response.json();
    }
}

// Initialize dashboard when page loads
document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.userApiKey !== 'undefined') {
        window.dashboard = new DashboardManager();
        window.dashboard.initialize();
    }
});