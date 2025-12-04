<?php
session_start();
require_once 'config/database.php';

if(isset($_SESSION['user_id'])) {
    header('Location: dashboard/');
    exit();
}

// Get plan and billing from URL (for Subscribe flow)
$plan = $_GET['plan'] ?? 'pro';
$billing = $_GET['billing'] ?? 'monthly';

// Validate plan
$validPlans = ['standard', 'pro', 'premium'];
if(!in_array($plan, $validPlans)) {
    $plan = 'pro'; // Default
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $firstName = htmlspecialchars($_POST['first_name']);
    $lastName = htmlspecialchars($_POST['last_name']);
    $company = htmlspecialchars($_POST['company']);
    $plan = $_POST['plan'] ?? 'pro';
    $billing = $_POST['billing'] ?? 'monthly';
    
    // Validation
    if($password !== $confirm) {
        $error = 'Passwords do not match';
    } elseif(strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } else {
        $pdo = getDBConnection();
        
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->fetch()) {
            $error = 'Email already registered. Please <a href="login.php" class="alert-link">login</a> instead.';
        } else {
            // Create user with selected plan (NO TRIAL - immediate subscription)
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $apiKey = generateApiKey(0); // 0 as placeholder
            
            // Get plan details for display name
            $stmt = $pdo->prepare("SELECT tier_name FROM subscription_plans WHERE tier_name = ?");
            $stmt->execute([$plan]);
            $planDetails = $stmt->fetch();
            $displayPlan = $planDetails ? ucfirst($planDetails['tier_name']) : 'Pro';
            
            $stmt = $pdo->prepare("
                INSERT INTO users (
                    email, password_hash, first_name, last_name, company, 
                    api_key, subscription_tier, subscription_status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())
            ");
            
            if($stmt->execute([$email, $passwordHash, $firstName, $lastName, $company, $apiKey, $plan])) {
                $userId = $pdo->lastInsertId();
                
                // Update API key with actual user ID
                $apiKey = generateApiKey($userId);
                $stmt = $pdo->prepare("UPDATE users SET api_key = ? WHERE id = ?");
                $stmt->execute([$apiKey, $userId]);
                
                // Create dashboard settings
                $stmt = $pdo->prepare("INSERT INTO user_dashboard_settings (user_id) VALUES (?)");
                $stmt->execute([$userId]);
                
                // Auto-login user after registration
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_tier'] = $plan;
                
                // Redirect to checkout immediately
                header("Location: subscription/checkout.php?plan=$plan&billing=$billing");
                exit();
                
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AccuTrading Signals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container py-5" style="padding-top: 100px !important;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-dark border-nasdaq-blue">
                    <div class="card-body p-5">
                        <?php 
                        $displayPlan = ucfirst($plan);
                        $displayBilling = $billing == 'annual' ? 'year' : 'month';
                        ?>
                        
                        <h2 class="text-center mb-4">Register for <?php echo $displayPlan; ?> Plan</h2>
                        
                        <div class="alert alert-info bg-black border-nasdaq-blue mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            After registration, you'll be redirected to complete your 
                            <strong><?php echo $displayPlan; ?></strong> subscription.
                        </div>
                        
                        <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="plan" value="<?php echo htmlspecialchars($plan); ?>">
                            <input type="hidden" name="billing" value="<?php echo htmlspecialchars($billing); ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required 
                                           value="<?php echo $_POST['first_name'] ?? ''; ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required
                                           value="<?php echo $_POST['last_name'] ?? ''; ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?php echo $_POST['email'] ?? ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="company" class="form-label">Company (Optional)</label>
                                <input type="text" class="form-control" id="company" name="company"
                                       value="<?php echo $_POST['company'] ?? ''; ?>">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password *</label>
                                    <input type="password" class="form-control" id="password" name="password" required minlength="8">
                                    <div class="form-text">At least 8 characters</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password *</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="terms.php" class="text-nasdaq-blue">Terms of Service</a> and 
                                        <a href="privacy.php" class="text-nasdaq-blue">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-nasdaq-blue btn-lg">
                                    <i class="bi bi-person-plus me-2"></i>Register & Continue to Payment
                                </button>
                                
                                <div class="text-center mt-3">
                                    <a href="login.php" class="text-nasdaq-blue">Already have an account? Login here</a>
                                </div>
                                
                                <div class="text-center text-muted small mt-3">
                                    By registering, you'll create an account and proceed to checkout for the 
                                    <strong><?php echo $displayPlan; ?></strong> plan.
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- Add Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>