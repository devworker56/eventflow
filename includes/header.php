<?php
// This is a partial header included in other files
// Main header is in index.php
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top border-bottom border-nasdaq-blue">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <span class="fw-bold text-nasdaq-blue">EventFlow</span>
            <span class="text-light ms-1">Institutional</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="features.php">Features</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pricing.php">Pricing</a>
                </li>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard/">Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <?php echo htmlspecialchars($_SESSION['user_email']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="dashboard/">Dashboard</a></li>
                            <li><a class="dropdown-item" href="dashboard/profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-nasdaq-blue ms-2" href="register.php">Free Trial</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>