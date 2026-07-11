<?php
/**
 * ============================================================================
 * TravelEase - Admin Delete Package
 * File: admin/delete-package.php
 *
 * Deletes a tour package and its associated image files from the server.
 * Bookings linked to the package are automatically deleted due to the
 * ON DELETE CASCADE foreign key constraint in the database.
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
// Get the package ID from the URL
// ----------------------------------------------------------------------------
$packageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($packageId <= 0) {
    header('Location: packages.php?error=Invalid package ID.');
    exit;
}

// Fetch the package to get image file names before deleting
$stmt = $pdo->prepare("SELECT image, gallery FROM packages WHERE id = :id");
$stmt->execute([':id' => $packageId]);
$pkg = $stmt->fetch();

if (!$pkg) {
    header('Location: packages.php?error=Package not found.');
    exit;
}

// ----------------------------------------------------------------------------
// Delete image files from the server
// ----------------------------------------------------------------------------
$uploadDir = '../assets/uploads/';

// Delete main image
if (!empty($pkg['image']) && file_exists($uploadDir . $pkg['image'])) {
    unlink($uploadDir . $pkg['image']);
}

// Delete gallery images
if (!empty($pkg['gallery'])) {
    $galleryImages = explode(',', $pkg['gallery']);
    foreach ($galleryImages as $img) {
        $img = trim($img);
        if (!empty($img) && file_exists($uploadDir . $img)) {
            unlink($uploadDir . $img);
        }
    }
}

// ----------------------------------------------------------------------------
// Delete the package from the database
// (Bookings are auto-deleted via ON DELETE CASCADE foreign key)
// ----------------------------------------------------------------------------
$deleteStmt = $pdo->prepare("DELETE FROM packages WHERE id = :id");
$result = $deleteStmt->execute([':id' => $packageId]);

if ($result) {
    header('Location: packages.php?success=Package deleted successfully.');
} else {
    header('Location: packages.php?error=Failed to delete package.');
}
exit;
