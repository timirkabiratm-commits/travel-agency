<?php
/**
 * ============================================================================
 * TravelEase - Admin Login Page
 * File: admin/login.php
 *
 * Handles administrator authentication. Checks the email and password
 * against the `admins` table using password_verify() with bcrypt hashes.
 * On success, stores the admin info in the session and redirects to the
 * dashboard.
 * ============================================================================
 */

// Start the session (must be the very first thing)
session_start();

// Include the database connection
require_once '../config/db.php';

// If admin is already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Initialize error variable
$error = '';

// ----------------------------------------------------------------------------
// Handle login form submission
// ----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? ''; // Don't sanitize password before hashing

    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Look up the admin by email using a prepared statement
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email AND status = 1");
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // Password is correct — set session variables
            $_SESSION['admin_id']    = $admin['id'];
            $_SESSION['admin_name']  = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_role']  = $admin['role'];

            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);

            // Redirect to the dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - TravelEase</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="admin-body">

<!-- ============================================================================ -->
<!-- ADMIN LOGIN CARD                                                              -->
<!-- ============================================================================ -->
<div class="admin-login-wrapper">
    <div class="admin-login-card" data-aos="fade-up">
        <!-- Header -->
        <div class="login-header">
            <i class="fa-solid fa-plane-departure pulse"></i>
            <h4 class="mb-0">TravelEase Admin</h4>
            <p class="mb-0 text-light-opacity">Sign in to manage your travel agency</p>
        </div>

        <!-- Body (Form) -->
        <div class="login-body">
            <!-- Error message -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-custom">
                    <i class="fa-solid fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="admin@travelease.com" required
                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Enter your password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 mb-3">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Sign In
                </button>
            </form>

            <!-- Demo credentials hint -->
            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fa-solid fa-info-circle me-1"></i>
                    Demo: admin@travelease.com / admin123
                </small>
            </div>

            <hr class="my-3">
            <div class="text-center">
                <a href="../index.php" class="text-decoration-none small">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Website
                </a>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });

    // Toggle password visibility
    $('#togglePassword').on('click', function () {
        var $input = $('#password');
        var $icon   = $(this).find('i');
        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            $input.attr('type', 'password');
            $icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
</script>
</body>
</html>
