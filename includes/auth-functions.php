<?php
// Authentication helper functions

function requireLogin() {
    if(!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    
    // Check if user is admin (you need to add role to users table)
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if($user['role'] !== 'admin') {
        header('Location: ../dashboard/');
        exit();
    }
}

function getUserTier() {
    if(!isset($_SESSION['user_id'])) {
        return 'guest';
    }
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT subscription_tier FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    return $user['subscription_tier'] ?? 'explorer';
}

function checkSubscriptionStatus() {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT subscription_status, trial_ends_at 
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if($user['subscription_status'] === 'trialing' && strtotime($user['trial_ends_at']) < time()) {
        // Trial expired
        $stmt = $pdo->prepare("UPDATE users SET subscription_status = 'expired' WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        return false;
    }
    
    return $user['subscription_status'] === 'active' || $user['subscription_status'] === 'trialing';
}
?>