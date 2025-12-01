<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$signalId = $_GET['id'] ?? 0;
$pdo = getDBConnection();

$stmt = $pdo->prepare("
    SELECT s.*, e.title, e.description, e.scheduled_time
    FROM signals s
    JOIN market_events e ON s.event_id = e.id
    WHERE s.id = ?
");
$stmt->execute([$signalId]);
$signal = $stmt->fetch();

if(!$signal) {
    echo json_encode(['success' => false, 'error' => 'Signal not found']);
    exit();
}

// Parse signal data
$signalData = json_decode($signal['signal_data'], true);

$html = '
<div class="signal-details">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h4>' . htmlspecialchars($signal['title']) . '</h4>
            <p class="text-muted">' . htmlspecialchars($signal['description']) . '</p>
        </div>
        <span class="badge bg-' . ($signal['confidence_score'] >= 0.7 ? 'success' : ($signal['confidence_score'] >= 0.5 ? 'primary' : 'danger')) . '">
            ' . round($signal['confidence_score'] * 100) . '% Confidence
        </span>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <h6>Event Details</h6>
            <ul class="list-unstyled">
                <li class="mb-2"><strong>Scheduled:</strong> ' . date('F j, Y H:i', strtotime($signal['scheduled_time'])) . '</li>
                <li class="mb-2"><strong>Generated:</strong> ' . date('F j, Y H:i', strtotime($signal['generated_at'])) . '</li>
                <li class="mb-2"><strong>Signal Type:</strong> ' . ucfirst(str_replace('_', ' ', $signal['signal_type'])) . '</li>
            </ul>
        </div>
        
        <div class="col-md-6">
            <h6>Quick Stats</h6>
            <div class="row text-center">
                <div class="col-4">
                    <div class="border rounded p-2">
                        <div class="fw-bold">' . ($signalData['assets_impacted'] ?? 'N/A') . '</div>
                        <small class="text-muted">Assets</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border rounded p-2">
                        <div class="fw-bold">' . ($signalData['expected_move'] ?? 'N/A') . '</div>
                        <small class="text-muted">Expected Move</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border rounded p-2">
                        <div class="fw-bold">' . ($signalData['time_horizon'] ?? 'N/A') . '</div>
                        <small class="text-muted">Horizon</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <hr>
    
    <div class="mt-4">
        <h6>Analysis & Recommendations</h6>
        <div class="alert alert-dark">
            <pre class="mb-0 text-light" style="white-space: pre-wrap;">' . json_encode($signalData, JSON_PRETTY_PRINT) . '</pre>
        </div>
    </div>
</div>
';

echo json_encode(['success' => true, 'html' => $html]);
?>