<?php
/**
 * ============================================================================
 * TravelEase - Admin Manage Contacts
 * File: admin/contacts.php
 *
 * Displays contact messages submitted through the public Contact page.
 * Admins can view, mark as read, and delete messages.
 * ============================================================================
 */

session_start();
require_once '../config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$successMsg = '';
$errorMsg   = '';
if (isset($_GET['success']))  $successMsg = htmlspecialchars($_GET['success']);
if (isset($_GET['error']))    $errorMsg   = htmlspecialchars($_GET['error']);

// ----------------------------------------------------------------------------
// Handle mark as read
// ----------------------------------------------------------------------------
if (isset($_GET['read']) && (int)$_GET['read'] > 0) {
    $contactId = (int)$_GET['read'];
    $stmt = $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE id = :id");
    $stmt->execute([':id' => $contactId]);
    header('Location: contacts.php?success=Message marked as read.');
    exit;
}

// ----------------------------------------------------------------------------
// Handle delete
// ----------------------------------------------------------------------------
if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    $contactId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = :id");
    $result = $stmt->execute([':id' => $contactId]);

    if ($result) {
        header('Location: contacts.php?success=Message deleted successfully.');
    } else {
        header('Location: contacts.php?error=Failed to delete message.');
    }
    exit;
}

// ----------------------------------------------------------------------------
// Fetch all contact messages
// ----------------------------------------------------------------------------
$contacts = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
$unreadCount = (int)$pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Messages - TravelEase Admin</title>

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
        <li class="nav-item"><a class="nav-link" href="packages.php"><i class="fa-solid fa-suitcase"></i> Packages</a></li>
        <li class="nav-item"><a class="nav-link" href="bookings.php"><i class="fa-solid fa-calendar-check"></i> Bookings</a></li>
        <li class="nav-item"><a class="nav-link active" href="contacts.php"><i class="fa-solid fa-envelope"></i> Messages
            <?php if ($unreadCount > 0): ?>
                <span class="badge bg-danger ms-auto"><?= $unreadCount ?></span>
            <?php endif; ?>
        </a></li>
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
            <h5>Manage Messages</h5>
        </div>
        <span class="badge bg-primary"><?= count($contacts) ?> total</span>
    </div>

    <!-- Messages -->
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
    <!-- MESSAGES LIST                                                   -->
    <!-- ---------------------------------------------------------------- -->
    <div class="admin-table-card" data-aos="fade-up">
        <?php if (empty($contacts)): ?>
            <div class="text-center py-5">
                <i class="fa-solid fa-envelope fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No messages found.</h5>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table admin-table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $c): ?>
                            <tr class="<?= !$c['is_read'] ? 'table-light' : '' ?>">
                                <td>#<?= (int)$c['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($c['name']) ?></strong>
                                    <?php if (!$c['is_read']): ?>
                                        <span class="badge bg-danger ms-1">New</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($c['email']) ?></td>
                                <td><?= htmlspecialchars($c['subject']) ?></td>
                                <td style="max-width: 250px;">
                                    <p class="mb-0 text-muted" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?= htmlspecialchars($c['message']) ?>
                                    </p>
                                </td>
                                <td><?= date('M j, Y g:i A', strtotime($c['created_at'])) ?></td>
                                <td>
                                    <?php if ($c['is_read']): ?>
                                        <span class="status-badge status-confirmed">Read</span>
                                    <?php else: ?>
                                        <span class="status-badge status-pending">Unread</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <!-- View button (modal) -->
                                    <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#contactModal<?= (int)$c['id'] ?>" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <?php if (!$c['is_read']): ?>
                                        <!-- Mark as read -->
                                        <a href="contacts.php?read=<?= (int)$c['id'] ?>" class="btn btn-outline-success btn-sm" title="Mark as Read">
                                            <i class="fa-solid fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                    <!-- Delete -->
                                    <a href="contacts.php?delete=<?= (int)$c['id'] ?>"
                                       class="btn btn-outline-danger btn-sm" title="Delete"
                                       onclick="return confirm('Are you sure you want to delete this message?');">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Contact details modal -->
                            <div class="modal fade" id="contactModal<?= (int)$c['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Message from <?= htmlspecialchars($c['name']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-borderless">
                                                <tr><td class="text-muted">Name:</td><td class="fw-semibold"><?= htmlspecialchars($c['name']) ?></td></tr>
                                                <tr><td class="text-muted">Email:</td><td><?= htmlspecialchars($c['email']) ?></td></tr>
                                                <tr><td class="text-muted">Subject:</td><td><?= htmlspecialchars($c['subject']) ?></td></tr>
                                                <tr><td class="text-muted">Date:</td><td><?= date('M j, Y g:i A', strtotime($c['created_at'])) ?></td></tr>
                                            </table>
                                            <hr>
                                            <h6>Message:</h6>
                                            <p class="text-muted"><?= nl2br(htmlspecialchars($c['message'])) ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="mailto:<?= htmlspecialchars($c['email']) ?>" class="btn btn-primary rounded-pill">
                                                <i class="fa-solid fa-reply me-1"></i> Reply via Email
                                            </a>
                                            <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="../assets/js/main.js"></script>
<script>
    AOS.init({ duration: 800, once: true });

    // Auto-mark as read when opening a modal
    $('.modal').on('shown.bs.modal', function () {
        var modalId = $(this).attr('id');
        var id = modalId.replace('contactModal', '');
        if (id && !isNaN(id)) {
            // Silently mark as read via AJAX
            $.get('contacts.php', { read: id });
        }
    });
</script>
</body>
</html>
