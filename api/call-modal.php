<?php
session_start();
require_once '../config/database.php';
require_once '../config/modal-config.php';

header('Content-Type: application/json');

// Check API key
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
if(empty($apiKey)) {
    http_response_code(401);
    echo json_encode(['error' => 'API key required']);
    exit();
}

// Validate API key
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT id, subscription_tier, subscription_status FROM users WHERE api_key = ?");
$stmt->execute([$apiKey]);
$user = $stmt->fetch();

if(!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid API key']);
    exit();
}

// Check rate limiting
if(!checkRateLimit($user['id'])) {
    http_response_code(429);
    echo json_encode(['error' => 'Rate limit exceeded']);
    exit();
}

// Get request data
$input = json_decode(file_get_contents('php://input'), true);
$functionName = $input['function'] ?? '';
$data = $input['data'] ?? [];

// Validate function name
if(!in_array($functionName, [
    'get_live_signals',
    'analyze_event',
    'get_cross_asset_correlations',
    'get_temporal_analysis',
    'get_ripple_effects',
    'get_hedging_suggestions',
    'get_liquidity_analysis',
    'batch_process_events'
])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid function name']);
    exit();
}

// Check tier access
if(!checkTierAccess($user['subscription_tier'], $functionName)) {
    http_response_code(403);
    echo json_encode(['error' => 'Function not available for your tier']);
    exit();
}

// Call Modal function
try {
    $result = callModalAPI($functionName, $data);
    
    if($result) {
        // Log API call
        logAPICall($user['id'], $functionName, $data);
        
        echo json_encode([
            'success' => true,
            'data' => $result,
            'timestamp' => time()
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Modal API call failed']);
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function checkRateLimit($userId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM api_logs 
        WHERE user_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    
    // Get user tier limits
    $stmt = $pdo->prepare("SELECT max_api_calls FROM subscription_plans sp JOIN users u ON u.subscription_tier = sp.tier_name WHERE u.id = ?");
    $stmt->execute([$userId]);
    $limits = $stmt->fetch();
    
    $hourlyLimit = $limits['max_api_calls'] / 24; // Rough hourly limit
    
    return $result['count'] < $hourlyLimit;
}

function checkTierAccess($tier, $function) {
    $tierAccess = [
        'explorer' => ['get_live_signals', 'analyze_event'],
        'professional' => ['get_live_signals', 'analyze_event', 'get_cross_asset_correlations', 'get_temporal_analysis', 'get_hedging_suggestions'],
        'institutional' => ['get_live_signals', 'analyze_event', 'get_cross_asset_correlations', 'get_temporal_analysis', 'get_ripple_effects', 'get_hedging_suggestions', 'get_liquidity_analysis', 'batch_process_events']
    ];
    
    return in_array($function, $tierAccess[$tier]);
}

function logAPICall($userId, $function, $data) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        INSERT INTO api_logs (user_id, function_name, request_data)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$userId, $function, json_encode($data)]);
}
?>