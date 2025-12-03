<?php
session_start();
require_once 'config/database.php';

if(isset($_SESSION['user_id'])) {
    // Redirect based on role if already logged in
    if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: dashboard/');
    }
    exit();
}

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if($user && password_verify($password, $user['password_hash'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_tier'] = $user['subscription_tier'];
        
        // Check if user is admin (using is_admin column)
        if(isset($user['is_admin']) && $user['is_admin'] == 1) {
            $_SESSION['user_role'] = 'admin';
        } else {
            $_SESSION['user_role'] = 'user';
        }
        
        // Update last login timestamp
        $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $updateStmt->execute([$user['id']]);
        
        // Redirect based on user role
        if($_SESSION['user_role'] === 'admin') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: dashboard/');
        }
        exit();
    } else {
        $error = 'Invalid email or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EventFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/custom.css">
    
    <style>
        .login-container {
            padding-top: 100px !important;
        }
        .admin-login-hint {
            font-size: 0.85rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container py-5 login-container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-dark border-nasdaq-blue">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Login to EventFlow</h2>
                        
                        <?php if($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control bg-black text-light" id="email" name="email" required 
                                       placeholder="Enter your email address">
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control bg-black text-light" id="password" name="password" required 
                                       placeholder="Enter your password">
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Remember me</label>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-nasdaq-blue">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                </button>
                                <a href="register.php" class="btn btn-outline-light">
                                    <i class="bi bi-person-plus me-2"></i>Create New Account
                                </a>
                            </div>
                            
                            <div class="text-center mt-3">
                                <a href="forgot-password.php" class="text-nasdaq-blue text-decoration-none">
                                    <i class="bi bi-key me-1"></i>Forgot Password?
                                </a>
                            </div>
                            
                            <?php
                            // Show admin login hint if no users exist yet (fresh installation)
                            $pdo = getDBConnection();
                            $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                            if($userCount == 0): ?>
                            <div class="admin-login-hint text-center mt-4">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    First user registration will be granted admin privileges.
                                </small>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>