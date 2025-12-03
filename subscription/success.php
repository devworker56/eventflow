<?php
// subscription/success.php
session_start();
require_once '../config/database.php';
require_once '../includes/auth-functions.php';

requireLogin();

$pdo = getDBConnection();

// Get user's current subscription info
$stmt = $pdo->prepare("
    SELECT u.*, sp.description as plan_description 
    FROM users u
    LEFT JOIN subscription_plans sp ON u.subscription_tier = sp.tier_name
    WHERE u.id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Successful - EventFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container py-5" style="padding-top: 100px !important;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-dark border-success text-center">
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        </div>
                        
                        <h1 class="mb-3">Subscription Activated!</h1>
                        <p class="lead mb-4">
                            Welcome to EventFlow <strong class="text-nasdaq-blue"><?php echo ucfirst($user['subscription_tier']); ?></strong> tier.
                        </p>
                        
                        <div class="card bg-black border-nasdaq-blue mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Your Subscription Details</h5>
                                <div class="row text-start">
                                    <div class="col-md-6">
                                        <p><strong>Plan:</strong> <?php echo ucfirst($user['subscription_tier']); ?></p>
                                        <p><strong>Status:</strong> <span class="badge bg-success">Active Trial</span></p>
                                        <p><strong>Trial Ends:</strong> <?php echo date('F j, Y', strtotime($user['trial_ends_at'])); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Account Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                        <p><strong>API Key:</strong> <code class="text-muted"><?php echo substr($user['api_key'], 0, 8) . '...'; ?></code></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-muted mb-4">
                            Your 14-day free trial has started. You'll get access to all <?php echo ucfirst($user['subscription_tier']); ?> features immediately.
                            No charges will be made until after the trial period.
                        </p>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="../dashboard/" class="btn btn-nasdaq-blue btn-lg">
                                <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                            </a>
                            <a href="../pricing.php" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-arrow-left-circle me-2"></i>Back to Pricing
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>