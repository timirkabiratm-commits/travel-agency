<?php
/**
 * ============================================================================
 * TravelEase - Admin Manage Packages
 * File: admin/packages.php
 *
 * Displays a table of all tour packages with options to add, edit,
 * and delete. Shows package image, title, location, price, category,
 * status, and featured flag.
 * ============================================================================
 */

session_start();
require_once '../config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// ----------------------------------------------------------------------------
// Handle success/error messages from other admin pages (via URL params)
// ----------------------------------------------------------------------------
$successMsg = '';
$errorMsg   = '';
if (isset($_GET['success']))  $successMsg = htmlspecialchars($_GET['success']);
if (isset($_GET['error']))    $errorMsg   = htmlspecialchars($_GET['error']);

// ----------------------------------------------------------------------------
// Fetch all packages ordered by newest first
// ----------------------------------------------------------------------------
$packages = $pdo->query("SELECT * FROM packages ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages - TravelEase Admin</title>

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
        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="packages.php"><i class="fa-solid fa-suitcase"></i> Packages</a></li>
        <li class="nav-item"><a class="nav-link" href="bookings.php"><i class="fa-solid fa-calendar-check"></i> Bookings</a></li>
        <li class="nav-item"><a class="nav-link" href="contacts.php"><i class="fa-solid fa-envelope"></i> Messages</a></li>
        <li class="nav-item mt-3"><a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
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
            <h5>Manage Packages</h5>
        </div>
        <a href="add-package.php" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Add New Package
        </a>
    </div>

    <!-- Success / Error messages -->
    <?php if ($successMsg): ?>
        <div class="alert alert-success alert-custom alert-auto-dismiss">
            <i class="fa-solid fa-check-circle me-2"></i><?= $successMsg ?>
        </div>
    <?php endif; ?>
    <?php if ($errorMsg): ?>
        <div class="alert alert-danger alert-custom alert-auto-dismiss">
            <i class="fa-solid fa-exclamation-circle me-2"></i><?= $errorMsg ?>
        </div>
    <?php endif; ?>

    <!-- ---------------------------------------------------------------- -->
    <!-- PACKAGES TABLE                                                   -->
    <!-- ---------------------------------------------------------------- -->
    <div class="admin-table-card" data-aos="fade-up">
        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($packages)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-5">
                            <i class="fa-solid fa-suitcase fa-2x d-block mb-2 text-muted"></i>
                            No packages found. Click "Add New Package" to create one.
                        </td></tr>
                    <?php else: foreach ($packages as $p): ?>
                        <tr>
                            <td>
                                <?php
                                $imgPath = '../assets/uploads/' . htmlspecialchars($p['image']);
                                if (!empty($p['image']) && file_exists('../assets/uploads/' . $p['image'])) {
                                    echo '<img src="' . $imgPath . '" class="table-img" alt="' . htmlspecialchars($p['title']) . '">';
                                } else {
                                    echo '<div class="table-img d-flex align-items-center justify-content-center" style="background: var(--gray-100); color: var(--gray-500);"><i class="fa-solid fa-image"></i></div>';
                                }
                                ?>
                            </td>
                            <td><strong><?= htmlspecialchars($p['title']) ?></strong></td>
                            <td><?= htmlspecialchars($p['location']) ?></td>
                            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($p['category']) ?></span></td>
                            <td>$<?= number_format($p['price'], 2) ?></td>
                            <td><?= (int)$p['duration_days'] ?> days</td>
                            <td>
                                <?php if ($p['featured']): ?>
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-star"></i></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['status']): ?>
                                    <span class="status-badge status-confirmed">Published</span>
                                <?php else: ?>
                                    <span class="status-badge status-cancelled">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="edit-package.php?id=<?= (int)$p['id'] ?>"
                                   class="btn btn-outline-primary btn-sm" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="delete-package.php?id=<?= (int)$p['id'] ?>"
                                   class="btn btn-outline-danger btn-sm"
                                   title="Delete"
                                   onclick="return confirm('Are you sure you want to delete this package? This action cannot be undone.');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="../assets/js/main.js"></script>
<script>AOS.init({ duration: 800, once: true });</script>
</body>
</html>
