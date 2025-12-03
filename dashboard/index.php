<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EventFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dashboard-card {
            transition: transform 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container py-5" style="padding-top: 100px !important;">
        <div class="row mb-4">
            <div class="col">
                <h1>Welcome, <?php echo htmlspecialchars($user['email']); ?></h1>
                <p class="text-muted">Your subscription tier: 
                    <span class="badge bg-nasdaq-blue"><?php echo ucfirst($user['subscription_tier']); ?></span>
                </p>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card dashboard-card bg-dark border-nasdaq-blue">
                    <div class="card-body">
                        <h6 class="text-muted">Your Signals Today</h6>
                        <h3>0</h3>
                        <small>Based on your plan</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card bg-dark border-nasdaq-green">
                    <div class="card-body">
                        <h6 class="text-muted">Subscription Status</h6>
                        <h3 class="text-<?php echo $user['subscription_status'] == 'active' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($user['subscription_status']); ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card bg-dark border-nasdaq-red">
                    <div class="card-body">
                        <h6 class="text-muted">Last Login</h6>
                        <h3>
                            <?php echo $user['last_login'] ? date('M j', strtotime($user['last_login'])) : 'First time'; ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card bg-dark border-nasdaq-light-blue">
                    <div class="card-body">
                        <h6 class="text-muted">Account Created</h6>
                        <h3><?php echo date('M j, Y', strtotime($user['created_at'])); ?></h3>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Signals Feed -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-dark">
                    <div class="card-header border-nasdaq-blue">
                        <h5 class="mb-0">Recent Signals</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-center text-muted">
                            No signals yet. Signals will appear here based on your subscription plan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>