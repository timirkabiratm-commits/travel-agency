<?php
/**
 * ============================================================================
 * TravelEase - Admin Edit Package
 * File: admin/edit-package.php
 *
 * Displays a pre-filled form to edit an existing tour package.
 * Handles image replacement, form validation, and database update.
 * ============================================================================
 */

session_start();
require_once '../config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$error   = '';
$success = '';

// ----------------------------------------------------------------------------
// Get the package ID from the URL
// ----------------------------------------------------------------------------
$packageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($packageId <= 0) {
    header('Location: packages.php?error=Invalid package ID.');
    exit;
}

// Fetch the current package data
$stmt = $pdo->prepare("SELECT * FROM packages WHERE id = :id");
$stmt->execute([':id' => $packageId]);
$pkg = $stmt->fetch();

if (!$pkg) {
    header('Location: packages.php?error=Package not found.');
    exit;
}

// ----------------------------------------------------------------------------
// Handle form submission (update)
// ----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title        = sanitize_input($_POST['title'] ?? '');
    $location     = sanitize_input($_POST['location'] ?? '');
    $category     = sanitize_input($_POST['category'] ?? '');
    $price        = (float)($_POST['price'] ?? 0);
    $durationDays = (int)($_POST['duration_days'] ?? 1);
    $description  = sanitize_input($_POST['description'] ?? '');
    $itinerary    = sanitize_input($_POST['itinerary'] ?? '');
    $included     = sanitize_input($_POST['included'] ?? '');
    $excluded     = sanitize_input($_POST['excluded'] ?? '');
    $featured     = isset($_POST['featured']) ? 1 : 0;
    $status       = isset($_POST['status']) ? 1 : 0;
    $imageName    = $pkg['image']; // Keep existing image by default

    // Validation
    if (empty($title) || empty($location) || empty($category) || empty($description)) {
        $error = 'Please fill in all required fields.';
    } elseif ($price <= 0) {
        $error = 'Price must be greater than zero.';
    } else {
        // Handle new main image upload (replace old one)
        if (!empty($_FILES['image']['name'])) {
            $uploadDir  = '../assets/uploads/';
            $fileExt    = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($fileExt, $allowedExt)) {
                $error = 'Invalid image format. Allowed: JPG, JPEG, PNG, GIF, WEBP.';
            } elseif ($_FILES['image']['size'] > 5_000_000) {
                $error = 'Image size must be less than 5MB.';
            } else {
                $newImageName = uniqid('pkg_', true) . '.' . $fileExt;
                $uploadPath   = $uploadDir . $newImageName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    // Delete the old image file if it exists
                    if (!empty($pkg['image']) && file_exists($uploadDir . $pkg['image'])) {
                        unlink($uploadDir . $pkg['image']);
                    }
                    $imageName = $newImageName;
                } else {
                    $error = 'Failed to upload image.';
                }
            }
        }

        // Handle new gallery images (append to existing)
        $galleryStr = $pkg['gallery'];
        if (!empty($_FILES['gallery']['name'][0])) {
            $uploadDir    = '../assets/uploads/';
            $galleryNames = [];
            foreach ($_FILES['gallery']['name'] as $key => $name) {
                if (empty($name)) continue;
                $fileExt = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']) && $_FILES['gallery']['size'][$key] <= 5_000_000) {
                    $gName = uniqid('gal_', true) . '.' . $fileExt;
                    if (move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $uploadDir . $gName)) {
                        $galleryNames[] = $gName;
                    }
                }
            }
            if (!empty($galleryNames)) {
                $existing   = !empty($galleryStr) ? explode(',', $galleryStr) : [];
                $merged     = array_merge($existing, $galleryNames);
                $galleryStr = implode(',', $merged);
            }
        }

        // Update the database if no errors
        if (empty($error)) {
            $sql = "UPDATE packages SET
                        title = :title,
                        location = :location,
                        category = :category,
                        price = :price,
                        duration_days = :duration,
                        image = :image,
                        gallery = :gallery,
                        description = :description,
                        itinerary = :itinerary,
                        included = :included,
                        excluded = :excluded,
                        featured = :featured,
                        status = :status
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                ':title'       => $title,
                ':location'    => $location,
                ':category'    => $category,
                ':price'       => $price,
                ':duration'    => $durationDays,
                ':image'       => $imageName,
                ':gallery'     => $galleryStr,
                ':description' => $description,
                ':itinerary'   => $itinerary,
                ':included'    => $included,
                ':excluded'    => $excluded,
                ':featured'    => $featured,
                ':status'      => $status,
                ':id'          => $packageId
            ]);

            if ($result) {
                header('Location: packages.php?success=Package updated successfully.');
                exit;
            } else {
                $error = 'Failed to update package.';
            }
        }
    }

    // Re-fetch the package so the form shows updated values after an error
    $stmt = $pdo->prepare("SELECT * FROM packages WHERE id = :id");
    $stmt->execute([':id' => $packageId]);
    $pkg = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Package - TravelEase Admin</title>

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
            <h5>Edit Package: <?= htmlspecialchars($pkg['title']) ?></h5>
        </div>
        <a href="packages.php" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Error message -->
    <?php if ($error): ?>
        <div class="alert alert-danger alert-custom">
            <i class="fa-solid fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- ---------------------------------------------------------------- -->
    <!-- EDIT PACKAGE FORM                                                -->
    <!-- ---------------------------------------------------------------- -->
    <div class="admin-form-card" data-aos="fade-up">
        <form method="POST" action="edit-package.php?id=<?= $packageId ?>" enctype="multipart/form-data">
            <div class="row g-3">
                <!-- Current image preview -->
                <?php if (!empty($pkg['image']) && file_exists('../assets/uploads/' . $pkg['image'])): ?>
                    <div class="col-12 mb-2">
                        <label class="form-label">Current Main Image</label>
                        <div>
                            <img src="../assets/uploads/<?= htmlspecialchars($pkg['image']) ?>"
                                 alt="Current" style="max-height: 150px; border-radius: 8px;">
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Title -->
                <div class="col-md-8">
                    <label class="form-label" for="title">Package Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" required
                           value="<?= htmlspecialchars($pkg['title']) ?>">
                </div>
                <!-- Category -->
                <div class="col-md-4">
                    <label class="form-label" for="category">Category <span class="text-danger">*</span></label>
                    <select class="form-select" id="category" name="category" required>
                        <?php $cats = ['Beach', 'Mountain', 'Adventure', 'City', 'Cultural']; ?>
                        <?php foreach ($cats as $c): ?>
                            <option value="<?= $c ?>" <?= $pkg['category'] === $c ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Location -->
                <div class="col-md-6">
                    <label class="form-label" for="location">Location <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="location" name="location" required
                           value="<?= htmlspecialchars($pkg['location']) ?>">
                </div>
                <!-- Price -->
                <div class="col-md-3">
                    <label class="form-label" for="price">Price ($) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" required
                           value="<?= htmlspecialchars($pkg['price']) ?>">
                </div>
                <!-- Duration -->
                <div class="col-md-3">
                    <label class="form-label" for="duration_days">Duration (days) <span class="text-danger">*</span></label>
                    <input type="number" min="1" class="form-control" id="duration_days" name="duration_days" required
                           value="<?= (int)$pkg['duration_days'] ?>">
                </div>

                <!-- Replace Main Image -->
                <div class="col-md-6">
                    <label class="form-label" for="image">Replace Main Image (optional)</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <small class="text-muted">Leave empty to keep current image.</small>
                </div>
                <!-- Add Gallery Images -->
                <div class="col-md-6">
                    <label class="form-label" for="gallery">Add Gallery Images (optional)</label>
                    <input type="file" class="form-control" id="gallery" name="gallery[]" accept="image/*" multiple>
                    <small class="text-muted">New images will be added to existing gallery.</small>
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label class="form-label" for="description">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($pkg['description']) ?></textarea>
                </div>

                <!-- Itinerary -->
                <div class="col-12">
                    <label class="form-label" for="itinerary">Itinerary (one day per line)</label>
                    <textarea class="form-control" id="itinerary" name="itinerary" rows="4"><?= htmlspecialchars($pkg['itinerary'] ?? '') ?></textarea>
                </div>

                <!-- Included -->
                <div class="col-md-6">
                    <label class="form-label" for="included">What's Included (one per line)</label>
                    <textarea class="form-control" id="included" name="included" rows="4"><?= htmlspecialchars($pkg['included'] ?? '') ?></textarea>
                </div>
                <!-- Excluded -->
                <div class="col-md-6">
                    <label class="form-label" for="excluded">What's Excluded (one per line)</label>
                    <textarea class="form-control" id="excluded" name="excluded" rows="4"><?= htmlspecialchars($pkg['excluded'] ?? '') ?></textarea>
                </div>

                <!-- Featured & Status -->
                <div class="col-md-6">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="featured" name="featured"
                               <?= $pkg['featured'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="featured">Show as Featured Package</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="status" name="status"
                               <?= $pkg['status'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status">Published (visible on website)</label>
                    </div>
                </div>

                <!-- Submit -->
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary rounded-pill px-5">
                        <i class="fa-solid fa-save me-2"></i> Save Changes
                    </button>
                    <a href="packages.php" class="btn btn-outline-secondary rounded-pill px-4 ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="../assets/js/main.js"></script>
<script>AOS.init({ duration: 800, once: true });</script>
</body>
</html>
