<?php
/**
 * ============================================================================
 * TravelEase - Admin Dashboard
 * File: admin/dashboard.php
 *
 * Displays an overview of the system:
 *   - Total packages
 *   - Total bookings
 *   - Total customers (unique emails in bookings)
 *   - Contact messages (unread + total)
 *   - Recent bookings table
 *   - Recent contact messages
 * ============================================================================
 */

// Start the session
session_start();

// Include the database connection
require_once '../config/db.php';

// Check if the admin is logged in — if not, redirect to login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// ----------------------------------------------------------------------------
// Fetch statistics for the dashboard cards
// ----------------------------------------------------------------------------

// Total packages
$totalPackages = (int)$pdo->query("SELECT COUNT(*) FROM packages")->fetchColumn();

// Total bookings
$totalBookings = (int)$pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();

// Total customers (unique emails from bookings)
$totalCustomers = (int)$pdo->query("SELECT COUNT(DISTINCT customer_email) FROM bookings")->fetchColumn();

// Total contact messages
$totalContacts = (int)$pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();

// Unread contact messages
$unreadContacts = (int)$pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn();

// Pending bookings
$pendingBookings = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE booking_status = 'pending'")->fetchColumn();

// Total revenue (confirmed + completed bookings)
$revenueStmt = $pdo->query("SELECT SUM(total_price) FROM bookings WHERE booking_status IN ('confirmed','completed')");
$totalRevenue = (float)$revenueStmt->fetchColumn();

// ----------------------------------------------------------------------------
// Fetch recent bookings (latest 5)
// ----------------------------------------------------------------------------
$recentBookingsStmt = $pdo->query(
    "SELECT b.*, p.title AS package_title
     FROM bookings b
     JOIN packages p ON b.package_id = p.id
     ORDER BY b.created_at DESC
     LIMIT 5"
);
$recentBookings = $recentBookingsStmt->fetchAll();

// ----------------------------------------------------------------------------
// Fetch recent contact messages (latest 5)
// ----------------------------------------------------------------------------
$recentContactsStmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");
$recentContacts = $recentContactsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TravelEase Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="admin-body">

<!-- ============================================================================ -->
<!-- ADMIN SIDEBAR                                                                -->
<!-- ============================================================================ -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-logo">
        <h4><i class="fa-solid fa-plane-departure me-2"></i>TravelEase</h4>
        <small class="text-light-opacity">Admin Panel</small>
    </div>
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link active" href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="packages.php"><i class="fa-solid fa-suitcase"></i> Packages</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="bookings.php"><i class="fa-solid fa-calendar-check"></i> Bookings
                <?php if ($pendingBookings > 0): ?>
                    <span class="badge bg-danger ms-auto"><?= $pendingBookings ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="contacts.php"><i class="fa-solid fa-envelope"></i> Messages
                <?php if ($unreadContacts > 0): ?>
                    <span class="badge bg-danger ms-auto"><?= $unreadContacts ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item mt-3">
            <a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </li>
    </ul>
</aside>

<!-- ============================================================================ -->
<!-- ADMIN MAIN CONTENT                                                          -->
<!-- ============================================================================ -->
<div class="admin-main">

    <!-- Topbar -->
    <div class="admin-topbar">
        <div class="d-flex align-items-center gap-2">
            <button class="admin-toggle" id="adminToggle"><i class="fa-solid fa-bars"></i></button>
            <h5>Dashboard</h5>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">
                <i class="fa-solid fa-user-circle me-1"></i>
                Welcome, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>
            </span>
            <a href="../index.php" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-external-link me-1"></i> View Site
            </a>
        </div>
    </div>

    <!-- ---------------------------------------------------------------- -->
    <!-- STAT CARDS                                                       -->
    <!-- ---------------------------------------------------------------- -->
    <div class="row g-3 mb-4">
        <!-- Total Packages -->
        <div class="col-md-6 col-xl-3" data-aos="fade-up">
            <div class="stat-card">
                <div class="stat-icon-box" style="background: var(--primary-light); color: var(--primary);">
                    <i class="fa-solid fa-suitcase"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $totalPackages ?></h3>
                    <p>Total Packages</p>
                </div>
            </div>
        </div>
        <!-- Total Bookings -->
        <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card">
                <div class="stat-icon-box" style="background: #d3f9d8; color: #2b8a3e;">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $totalBookings ?></h3>
                    <p>Total Bookings</p>
                </div>
            </div>
        </div>
        <!-- Total Customers -->
        <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card">
                <div class="stat-icon-box" style="background: #fff3bf; color: #b08900;">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $totalCustomers ?></h3>
                    <p>Total Customers</p>
                </div>
            </div>
        </div>
        <!-- Contact Messages -->
        <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card">
                <div class="stat-icon-box" style="background: #ffe0e0; color: #c92a2a;">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $totalContacts ?></h3>
                    <p>Messages (<?= $unreadContacts ?> unread)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue banner -->
    <div class="row g-3 mb-4">
        <div class="col-12" data-aos="fade-up">
            <div class="cta-section" style="padding: 30px; border-radius: 16px;">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="mb-1"><i class="fa-solid fa-dollar-sign me-2"></i>Total Revenue</h3>
                        <p class="mb-0">From confirmed and completed bookings</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <h2 class="mb-0">$<?= number_format($totalRevenue, 2) ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ---------------------------------------------------------------- -->
    <!-- RECENT BOOKINGS TABLE                                           -->
    <!-- ---------------------------------------------------------------- -->
    <div class="row g-3">
        <div class="col-lg-7" data-aos="fade-up">
            <div class="admin-table-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fa-solid fa-calendar-check text-primary me-2"></i>Recent Bookings</h5>
                    <a href="bookings.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Package</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentBookings)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">No bookings yet.</td></tr>
                            <?php else: foreach ($recentBookings as $b): ?>
                                <tr>
                                    <td><?= htmlspecialchars($b['customer_name']) ?></td>
                                    <td><?= htmlspecialchars(substr($b['package_title'], 0, 20)) ?></td>
                                    <td><?= date('M j, Y', strtotime($b['travel_date'])) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= htmlspecialchars($b['booking_status']) ?>">
                                            <?= ucfirst(htmlspecialchars($b['booking_status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ------------------------------------------------------------- -->
        <!-- RECENT MESSAGES                                              -->
        <!-- ------------------------------------------------------------- -->
        <div class="col-lg-5" data-aos="fade-up" data-aos-delay="100">
            <div class="admin-table-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fa-solid fa-envelope text-primary me-2"></i>Recent Messages</h5>
                    <a href="contacts.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <?php if (empty($recentContacts)): ?>
                    <p class="text-center text-muted py-4">No messages yet.</p>
                <?php else: foreach ($recentContacts as $c): ?>
                    <div class="d-flex gap-3 py-3 border-bottom">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 40px; height: 40px; background: var(--primary-light); color: var(--primary); font-weight: 600;">
                                <?= strtoupper(substr($c['name'], 0, 1)) ?>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-0"><?= htmlspecialchars($c['name']) ?></h6>
                                <small class="text-muted"><?= date('M j', strtotime($c['created_at'])) ?></small>
                            </div>
                            <p class="text-muted small mb-1"><?= htmlspecialchars($c['subject']) ?></p>
                            <p class="text-muted small mb-0" style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= htmlspecialchars($c['message']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="../assets/js/main.js"></script>
<script>AOS.init({ duration: 800, once: true });</script>
</body>
</html>
