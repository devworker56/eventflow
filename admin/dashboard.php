<?php
session_start();
require_once '../config/database.php';

// Check if user is admin
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$pdo = getDBConnection();

// Get statistics
$stats = [
    'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'active_subscriptions' => $pdo->query("SELECT COUNT(*) FROM users WHERE subscription_status = 'active'")->fetchColumn(),
    'total_revenue' => $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'succeeded'")->fetchColumn() ?? 0,
    'signals_today' => $pdo->query("SELECT COUNT(*) FROM signals WHERE DATE(generated_at) = CURDATE()")->fetchColumn()
];

// Get recent users
$recentUsers = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 10")->fetchAll();

// Get recent payments
$recentPayments = $pdo->query("
    SELECT p.*, u.email 
    FROM payments p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.created_at DESC 
    LIMIT 10
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EventFlow</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 145, 218, 0.1);
        }
    </style>
</head>
<body>
    <!-- Admin Navigation -->
    <nav class="navbar navbar-dark bg-dark border-bottom border-nasdaq-blue">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <span class="text-nasdaq-blue">EventFlow</span> Admin
            </a>
            <div class="d-flex align-items-center">
                <a href="../index.php" class="btn btn-sm btn-outline-light me-2">
                    <i class="bi bi-arrow-left me-1"></i>Back to Site
                </a>
                <a href="../logout.php" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Admin Sidebar & Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-black vh-100">
                <div class="p-3">
                    <h6 class="text-nasdaq-blue mb-3">Admin Panel</h6>
                    <div class="list-group list-group-flush">
                        <a href="dashboard.php" class="list-group-item list-group-item-action bg-black text-light active">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                        <a href="manage-users.php" class="list-group-item list-group-item-action bg-black text-light">
                            <i class="bi bi-people me-2"></i>Users
                        </a>
                        <a href="manage-subscriptions.php" class="list-group-item list-group-item-action bg-black text-light">
                            <i class="bi bi-credit-card me-2"></i>Subscriptions
                        </a>
                        <a href="analytics.php" class="list-group-item list-group-item-action bg-black text-light">
                            <i class="bi bi-graph-up me-2"></i>Analytics
                        </a>
                        <a href="system-logs.php" class="list-group-item list-group-item-action bg-black text-light">
                            <i class="bi bi-journal-text me-2"></i>System Logs
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 py-4">
                <h2 class="mb-4">Admin Dashboard</h2>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-dark border-nasdaq-blue">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Total Users</h6>
                                        <h3 class="mt-2 mb-0"><?php echo $stats['total_users']; ?></h3>
                                    </div>
                                    <div class="bg-nasdaq-blue rounded-circle p-3">
                                        <i class="bi bi-people fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card stat-card bg-dark border-nasdaq-green">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Active Subs</h6>
                                        <h3 class="mt-2 mb-0"><?php echo $stats['active_subscriptions']; ?></h3>
                                    </div>
                                    <div class="bg-nasdaq-green rounded-circle p-3">
                                        <i class="bi bi-check-circle fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card stat-card bg-dark border-nasdaq-red">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Total Revenue</h6>
                                        <h3 class="mt-2 mb-0">$<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                                    </div>
                                    <div class="bg-nasdaq-red rounded-circle p-3">
                                        <i class="bi bi-cash-coin fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card stat-card bg-dark border-nasdaq-light-blue">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Signals Today</h6>
                                        <h3 class="mt-2 mb-0"><?php echo $stats['signals_today']; ?></h3>
                                    </div>
                                    <div class="bg-nasdaq-light-blue rounded-circle p-3">
                                        <i class="bi bi-lightning-charge fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Users -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-dark">
                            <div class="card-header border-nasdaq-blue d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Users</h5>
                                <a href="manage-users.php" class="btn btn-sm btn-nasdaq-blue">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover">
                                        <thead>
                                            <tr>
                                                <th>Email</th>
                                                <th>Tier</th>
                                                <th>Joined</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($recentUsers as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $user['subscription_tier'] == 'professional' ? 'nasdaq-blue' : ($user['subscription_tier'] == 'institutional' ? 'danger' : 'secondary'); ?>">
                                                        <?php echo ucfirst($user['subscription_tier']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M j', strtotime($user['created_at'])); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $user['subscription_status'] == 'active' ? 'success' : 'warning'; ?>">
                                                        <?php echo ucfirst($user['subscription_status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Payments -->
                    <div class="col-md-6">
                        <div class="card bg-dark">
                            <div class="card-header border-nasdaq-green d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Payments</h5>
                                <a href="manage-subscriptions.php" class="btn btn-sm btn-nasdaq-green">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Amount</th>
                                                <th>Plan</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($recentPayments as $payment): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($payment['email']); ?></td>
                                                <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                                                <td><?php echo ucfirst($payment['plan_tier']); ?></td>
                                                <td><?php echo date('M j', strtotime($payment['created_at'])); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $payment['status'] == 'succeeded' ? 'success' : 'danger'; ?>">
                                                        <?php echo ucfirst($payment['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-dark">
                            <div class="card-header border-nasdaq-blue">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-3">
                                    <button class="btn btn-nasdaq-blue" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                        <i class="bi bi-person-plus me-2"></i>Add User
                                    </button>
                                    <button class="btn btn-nasdaq-green" data-bs-toggle="modal" data-bs-target="#updatePlanModal">
                                        <i class="bi bi-credit-card me-2"></i>Update Plan
                                    </button>
                                    <button class="btn btn-nasdaq-red" data-bs-toggle="modal" data-bs-target="#revokeAccessModal">
                                        <i class="bi bi-person-x me-2"></i>Revoke Access
                                    </button>
                                    <a href="system-logs.php" class="btn btn-outline-light">
                                        <i class="bi bi-journal-text me-2"></i>View Logs
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modals -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header border-nasdaq-blue">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="add-user.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control bg-black text-light" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="plan" class="form-label">Subscription Plan</label>
                            <select class="form-select bg-black text-light" id="plan" name="plan" required>
                                <option value="explorer">Explorer ($79/month)</option>
                                <option value="professional" selected>Professional ($299/month)</option>
                                <option value="institutional">Institutional ($1,499/month)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="trialDays" class="form-label">Trial Days</label>
                            <input type="number" class="form-control bg-black text-light" id="trialDays" name="trial_days" value="14" min="0" max="30">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-nasdaq-blue">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Add other modals similarly -->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>