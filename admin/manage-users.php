<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth-functions.php';

requireAdmin();

$pdo = getDBConnection();
$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query
$where = '';
$params = [];
if($search) {
    $where = "WHERE email LIKE ? OR first_name LIKE ? OR last_name LIKE ?";
    $searchTerm = "%$search%";
    $params = array_fill(0, 3, $searchTerm);
}

// Get users
$stmt = $pdo->prepare("
    SELECT * FROM users 
    $where 
    ORDER BY created_at DESC 
    LIMIT $limit OFFSET $offset
");
$stmt->execute($params);
$users = $stmt->fetchAll();

// Get total count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users $where");
$stmt->execute($params);
$totalUsers = $stmt->fetchColumn();
$totalPages = ceil($totalUsers / $limit);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - EventFlow Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'includes/admin-nav.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card bg-dark">
                    <div class="card-header border-nasdaq-blue d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Manage Users</h4>
                        <div class="d-flex gap-2">
                            <form class="d-flex">
                                <input type="text" class="form-control me-2" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                                <button type="submit" class="btn btn-outline-light">Search</button>
                            </form>
                            <button class="btn btn-nasdaq-blue" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="bi bi-person-plus me-1"></i> Add User
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Email</th>
                                        <th>Name</th>
                                        <th>Tier</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['subscription_tier'] == 'professional' ? 'nasdaq-blue' : ($user['subscription_tier'] == 'institutional' ? 'danger' : 'secondary'); ?>">
                                                <?php echo ucfirst($user['subscription_tier']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['subscription_status'] == 'active' ? 'success' : ($user['subscription_status'] == 'trialing' ? 'warning' : 'danger'); ?>">
                                                <?php echo ucfirst($user['subscription_status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-light" onclick="editUser(<?php echo $user['id']; ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header border-nasdaq-blue">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="add-user.php" method="POST">
                    <div class="modal-body">
                        <!-- Form fields -->
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-nasdaq-blue">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function editUser(userId) {
        window.location.href = `edit-user.php?id=${userId}`;
    }
    
    function deleteUser(userId) {
        if(confirm('Are you sure you want to delete this user?')) {
            fetch(`delete-user.php?id=${userId}`, { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    }
                });
        }
    }
    </script>
</body>
</html>