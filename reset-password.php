<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = false;
$token = $_GET['token'] ?? '';

// Validate token
if(empty($token)) {
    $error = 'Invalid reset token';
} else {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT pr.*, u.email 
        FROM password_resets pr
        JOIN users u ON pr.user_id = u.id
        WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used = 0
    ");
    $stmt->execute([$token]);
    $reset_request = $stmt->fetch();
    
    if(!$reset_request) {
        $error = 'Invalid or expired reset token';
    }
}

// Handle password reset
if($_SERVER['REQUEST_METHOD'] == 'POST' && !$error) {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    
    if(empty($password) || empty($confirm)) {
        $error = 'Please fill in all fields';
    } elseif($password !== $confirm) {
        $error = 'Passwords do not match';
    } elseif(strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } else {
        // Update password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $pdo->beginTransaction();
        try {
            // Update user password
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$password_hash, $reset_request['user_id']]);
            
            // Mark token as used
            $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
            $stmt->execute([$reset_request['id']]);
            
            $pdo->commit();
            $success = true;
        } catch(Exception $e) {
            $pdo->rollBack();
            $error = 'Error resetting password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - EventFlow Institutional</title>
    
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
                        <h2 class="text-center mb-4">Set New Password</h2>
                        
                        <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <div class="text-center">
                            <a href="forgot-password.php" class="btn btn-nasdaq-blue">Request New Reset Link</a>
                        </div>
                        <?php elseif($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Your password has been reset successfully!
                        </div>
                        <div class="text-center">
                            <a href="login.php" class="btn btn-nasdaq-blue">Login with New Password</a>
                        </div>
                        <?php else: ?>
                        <p class="text-muted text-center mb-4">
                            Set a new password for your account.
                        </p>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Must be at least 8 characters</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-nasdaq-blue">Reset Password</button>
                                <a href="login.php" class="btn btn-outline-light">Back to Login</a>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>