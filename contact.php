<?php
session_start();

// Handle contact form submission
$success = false;
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    // Basic validation
    if(empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all fields';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        // In production, you would send an email here
        // For now, we'll just log to database
        try {
            require_once 'config/database.php';
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                INSERT INTO contact_messages (name, email, subject, message, ip_address)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $email, $subject, $message, $_SERVER['REMOTE_ADDR']]);
            $success = true;
        } catch(Exception $e) {
            $error = 'There was an error sending your message. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - EventFlow Institutional</title>
    <meta name="description" content="Contact the EventFlow team for sales inquiries, technical support, or partnership opportunities.">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Hero Section -->
    <section class="py-5" style="padding-top: 100px !important; background: linear-gradient(135deg, #000 0%, var(--nasdaq-dark-gray) 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-4 fw-bold mb-4">
                        Get in <span class="text-nasdaq-blue">Touch</span>
                    </h1>
                    <p class="lead mb-4">
                        Have questions about our platform? Need enterprise solutions? 
                        Our team is here to help you get the most out of derivatives intelligence.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form & Info -->
    <section class="py-5 bg-dark">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 mb-5 mb-lg-0">
                    <div class="card bg-black border-nasdaq-blue">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="card-title mb-4">Send us a message</h3>
                            
                            <?php if($success): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                Thank you for your message! We'll get back to you within 24 hours.
                            </div>
                            <?php elseif($error): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Your Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <select class="form-select" id="subject" name="subject" required>
                                        <option value="" selected disabled>Select a subject</option>
                                        <option value="Sales Inquiry">Sales Inquiry</option>
                                        <option value="Technical Support">Technical Support</option>
                                        <option value="Enterprise Solution">Enterprise Solution</option>
                                        <option value="Partnership">Partnership</option>
                                        <option value="Billing">Billing</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-nasdaq-blue btn-lg">
                                        <i class="bi bi-send me-2"></i>Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-5">
                    <div class="card bg-black border-nasdaq-blue h-100">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="card-title mb-4">Contact Information</h3>
                            
                            <div class="mb-4">
                                <h5 class="text-nasdaq-blue mb-3">
                                    <i class="bi bi-envelope me-2"></i>Email
                                </h5>
                                <p class="mb-0">
                                    <a href="mailto:support@eventflow.com" class="text-light text-decoration-none">
                                        support@eventflow.com
                                    </a>
                                </p>
                                <small class="text-muted">General inquiries and support</small>
                            </div>
                            
                            <div class="mb-4">
                                <h5 class="text-nasdaq-blue mb-3">
                                    <i class="bi bi-building me-2"></i>Office Hours
                                </h5>
                                <p class="mb-1">Monday - Friday: 9 AM - 6 PM EST</p>
                                <p class="mb-0">Saturday - Sunday: 10 AM - 4 PM EST</p>
                                <small class="text-muted">Support available during market hours</small>
                            </div>
                            
                            <div class="mb-4">
                                <h5 class="text-nasdaq-blue mb-3">
                                    <i class="bi bi-telephone me-2"></i>Phone
                                </h5>
                                <p class="mb-1">
                                    <a href="tel:+18885551234" class="text-light text-decoration-none">
                                        +1 (888) 555-1234
                                    </a>
                                </p>
                                <small class="text-muted">Sales and enterprise inquiries only</small>
                            </div>
                            
                            <div class="mb-4">
                                <h5 class="text-nasdaq-blue mb-3">
                                    <i class="bi bi-geo-alt me-2"></i>Location
                                </h5>
                                <address class="mb-0">
                                    EventFlow Institutional<br>
                                    123 Wall Street<br>
                                    New York, NY 10005<br>
                                    United States
                                </address>
                            </div>
                            
                            <div>
                                <h5 class="text-nasdaq-blue mb-3">Follow Us</h5>
                                <div class="d-flex gap-3">
                                    <a href="#" class="text-nasdaq-blue">
                                        <i class="bi bi-twitter" style="font-size: 1.5rem;"></i>
                                    </a>
                                    <a href="#" class="text-nasdaq-blue">
                                        <i class="bi bi-linkedin" style="font-size: 1.5rem;"></i>
                                    </a>
                                    <a href="#" class="text-nasdaq-blue">
                                        <i class="bi bi-github" style="font-size: 1.5rem;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Quick Links -->
    <section class="py-5 bg-black">
        <div class="container">
            <h2 class="text-center display-5 fw-bold mb-5">Quick Answers</h2>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card bg-dark h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-question-circle text-nasdaq-blue" style="font-size: 2rem;"></i>
                            </div>
                            <h5>Technical Support</h5>
                            <p class="text-muted small">
                                Having issues with the platform? Check our knowledge base or contact support.
                            </p>
                            <a href="support.php" class="btn btn-sm btn-outline-nasdaq-blue">Visit Support</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-dark h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-credit-card text-nasdaq-green" style="font-size: 2rem;"></i>
                            </div>
                            <h5>Billing Questions</h5>
                            <p class="text-muted small">
                                Questions about your subscription, invoices, or payment methods?
                            </p>
                            <a href="subscription/billing.php" class="btn btn-sm btn-outline-nasdaq-green">Billing Portal</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-dark h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-file-text text-nasdaq-light-blue" style="font-size: 2rem;"></i>
                            </div>
                            <h5>Documentation</h5>
                            <p class="text-muted small">
                                API documentation, integration guides, and platform tutorials.
                            </p>
                            <a href="docs.php" class="btn btn-sm btn-outline-nasdaq-light-blue">View Docs</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>