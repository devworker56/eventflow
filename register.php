<?php
session_start();
require_once 'config/database.php';

if(isset($_SESSION['user_id'])) {
    header('Location: dashboard/');
    exit();
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
            $error = 'Email already registered';
        } else {
            // Create user
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $apiKey = generateApiKey(0); // 0 as placeholder
            
            $stmt = $pdo->prepare("
                INSERT INTO users (email, password_hash, first_name, last_name, company, api_key, trial_ends_at)
                VALUES (?, ?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 14 DAY))
            ");
            
            if($stmt->execute([$email, $passwordHash, $firstName, $lastName, $company, $apiKey])) {
                $userId = $pdo->lastInsertId();
                
                // Update API key with actual user ID
                $apiKey = generateApiKey($userId);
                $stmt = $pdo->prepare("UPDATE users SET api_key = ? WHERE id = ?");
                $stmt->execute([$apiKey, $userId]);
                
                // Create dashboard settings
                $stmt = $pdo->prepare("INSERT INTO user_dashboard_settings (user_id) VALUES (?)");
                $stmt->execute([$userId]);
                
                $success = 'Account created successfully! You can now login.';
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
    <title>Register - EventFlow</title>
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
                        <h2 class="text-center mb-4">Start Your Free Trial</h2>
                        
                        <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        
                        <?php if($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="company" class="form-label">Company (Optional)</label>
                                <input type="text" class="form-control" id="company" name="company">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="terms.php" class="text-nasdaq-blue">Terms of Service</a> and 
                                        <a href="privacy.php" class="text-nasdaq-blue">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-nasdaq-blue btn-lg">Start 14-Day Free Trial</button>
                                <p class="text-center text-muted mt-2">
                                    No credit card required. Get full access to Explorer tier features.
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>