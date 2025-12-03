<?php
session_start();
require_once 'config/database.php';

$success = false;
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token in database
            $stmt = $pdo->prepare("
                INSERT INTO password_resets (user_id, token, expires_at)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$user['id'], $token, $expires]);
            
            // In production, send email here
            $reset_link = "https://eventflow.com/reset-password.php?token=$token";
            
            // For demo purposes, we'll show the link
            $success = "Password reset link has been generated. In production, this would be emailed to you.";
            $demo_link = $reset_link; // For demo display only
        } else {
            // Don't reveal if email exists
            $success = "If an account exists with that email, a password reset link has been sent.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - EventFlow Institutional</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container py-5" style="padding-top: 100px !important;">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-dark border-nasdaq-blue">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Reset Your Password</h2>
                        
                        <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        
                        <?php if($success): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                            
                            <?php if(isset($demo_link)): ?>
                            <div class="mt-3">
                                <small class="text-muted">Demo reset link:</small>
                                <div class="bg-black p-2 rounded mt-1">
                                    <code class="text-light"><?php echo htmlspecialchars($demo_link); ?></code>
                                </div>
                                <a href="<?php echo htmlspecialchars($demo_link); ?>" class="btn btn-sm btn-nasdaq-blue mt-2">
                                    Click to reset password
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!$success): ?>
                        <p class="text-muted text-center mb-4">
                            Enter your email address and we'll send you a link to reset your password.
                        </p>
                        
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required 
                                       placeholder="Enter the email associated with your account">
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-nasdaq-blue">Send Reset Link</button>
                                <a href="login.php" class="btn btn-outline-light">Back to Login</a>
                            </div>
                        </form>
                        <?php else: ?>
                        <div class="text-center">
                            <a href="login.php" class="btn btn-nasdaq-blue">Back to Login</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>