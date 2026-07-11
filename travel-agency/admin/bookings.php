<?php
/**
 * ============================================================================
 * TravelEase - Admin Manage Bookings
 * File: admin/bookings.php
 *
 * Displays a table of all customer bookings with options to:
 *   - View booking details
 *   - Update booking status (pending, confirmed, cancelled, completed)
 *   - Delete bookings
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
// Handle status update (AJAX or regular POST)
// ----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $bookingId = (int)($_POST['booking_id'] ?? 0);
    $newStatus = sanitize_input($_POST['new_status'] ?? '');

    $validStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];

    if ($bookingId > 0 && in_array($newStatus, $validStatuses)) {
        $stmt = $pdo->prepare("UPDATE bookings SET booking_status = :status WHERE id = :id");
        $result = $stmt->execute([':status' => $newStatus, ':id' => $bookingId]);

        if ($result) {
            // If AJAX request, return JSON
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Status updated.']);
                exit;
            }
            header('Location: bookings.php?success=Booking status updated successfully.');
            exit;
        } else {
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
                exit;
            }
            header('Location: bookings.php?error=Failed to update booking status.');
            exit;
        }
    }
}

// ----------------------------------------------------------------------------
// Handle booking deletion (via GET with confirmation)
// ----------------------------------------------------------------------------
if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    $bookingId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = :id");
    $result = $stmt->execute([':id' => $bookingId]);

    if ($result) {
        header('Location: bookings.php?success=Booking deleted successfully.');
    } else {
        header('Location: bookings.php?error=Failed to delete booking.');
    }
    exit;
}

// ----------------------------------------------------------------------------
// Fetch all bookings with package titles
// ----------------------------------------------------------------------------
$bookings = $pdo->query(
    "SELECT b.*, p.title AS package_title, p.location AS package_location
     FROM bookings b
     JOIN packages p ON b.package_id = p.id
     ORDER BY b.created_at DESC"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - TravelEase Admin</title>

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
        <li class="nav-item"><a class="nav-link active" href="bookings.php"><i class="fa-solid fa-calendar-check"></i> Bookings</a></li>
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
            <h5>Manage Bookings</h5>
        </div>
        <span class="badge bg-primary"><?= count($bookings) ?> total</span>
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
    <!-- BOOKINGS TABLE                                                   -->
    <!-- ---------------------------------------------------------------- -->
    <div class="admin-table-card" data-aos="fade-up">
        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Package</th>
                        <th>Travelers</th>
                        <th>Travel Date</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-5">
                            <i class="fa-solid fa-calendar-check fa-2x d-block mb-2 text-muted"></i>
                            No bookings found.
                        </td></tr>
                    <?php else: foreach ($bookings as $b): ?>
                        <tr>
                            <td>#<?= (int)$b['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($b['customer_name']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($b['customer_email']) ?></small><br>
                                <small class="text-muted"><?= htmlspecialchars($b['customer_phone']) ?></small>
                            </td>
                            <td>
                                <?= htmlspecialchars($b['package_title']) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($b['package_location']) ?></small>
                            </td>
                            <td><?= (int)$b['travelers'] ?></td>
                            <td><?= date('M j, Y', strtotime($b['travel_date'])) ?></td>
                            <td>$<?= number_format($b['total_price'], 2) ?></td>
                            <td>
                                <!-- Status dropdown -->
                                <form method="POST" action="bookings.php" class="status-form" style="display: inline;">
                                    <input type="hidden" name="update_status" value="1">
                                    <input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
                                    <select class="form-select form-select-sm status-select" name="new_status"
                                            style="width: auto; font-size: 0.8rem;">
                                        <option value="pending"   <?= $b['booking_status'] === 'pending'   ? 'selected' : '' ?>>Pending</option>
                                        <option value="confirmed" <?= $b['booking_status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                        <option value="cancelled" <?= $b['booking_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        <option value="completed" <?= $b['booking_status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                    </select>
                                </form>
                            </td>
                            <td class="text-end">
                                <!-- View details button (modal trigger) -->
                                <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#bookingModal<?= (int)$b['id'] ?>" title="View Details">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <!-- Delete button -->
                                <a href="bookings.php?delete=<?= (int)$b['id'] ?>"
                                   class="btn btn-outline-danger btn-sm"
                                   title="Delete"
                                   onclick="return confirm('Are you sure you want to delete this booking?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Booking details modal -->
                        <div class="modal fade" id="bookingModal<?= (int)$b['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Booking #<?= (int)$b['id'] ?> - <?= htmlspecialchars($b['customer_name']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-borderless">
                                            <tr><td class="text-muted">Package:</td><td class="fw-semibold"><?= htmlspecialchars($b['package_title']) ?></td></tr>
                                            <tr><td class="text-muted">Location:</td><td><?= htmlspecialchars($b['package_location']) ?></td></tr>
                                            <tr><td class="text-muted">Email:</td><td><?= htmlspecialchars($b['customer_email']) ?></td></tr>
                                            <tr><td class="text-muted">Phone:</td><td><?= htmlspecialchars($b['customer_phone']) ?></td></tr>
                                            <tr><td class="text-muted">Travelers:</td><td><?= (int)$b['travelers'] ?></td></tr>
                                            <tr><td class="text-muted">Travel Date:</td><td><?= date('M j, Y', strtotime($b['travel_date'])) ?></td></tr>
                                            <tr><td class="text-muted">Total Price:</td><td class="fw-bold text-primary">$<?= number_format($b['total_price'], 2) ?></td></tr>
                                            <tr><td class="text-muted">Status:</td><td>
                                                <span class="status-badge status-<?= htmlspecialchars($b['booking_status']) ?>"><?= ucfirst(htmlspecialchars($b['booking_status'])) ?></span>
                                            </td></tr>
                                            <tr><td class="text-muted">Booked On:</td><td><?= date('M j, Y g:i A', strtotime($b['created_at'])) ?></td></tr>
                                            <?php if (!empty($b['special_request'])): ?>
                                                <tr><td class="text-muted">Special Request:</td><td><?= htmlspecialchars($b['special_request']) ?></td></tr>
                                            <?php endif; ?>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
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
<script>
    AOS.init({ duration: 800, once: true });

    // Auto-submit status form on change
    $('.status-select').on('change', function () {
        $(this).closest('form').submit();
    });
</script>
</body>
</html>
