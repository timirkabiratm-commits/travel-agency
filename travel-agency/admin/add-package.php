<?php
/**
 * ============================================================================
 * TravelEase - Admin Add Package
 * File: admin/add-package.php
 *
 * Displays a form to create a new tour package. Handles image upload,
 * form validation, and database insertion using prepared statements.
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
// Handle form submission
// ----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize inputs
    $title         = sanitize_input($_POST['title'] ?? '');
    $location      = sanitize_input($_POST['location'] ?? '');
    $category      = sanitize_input($_POST['category'] ?? '');
    $price         = (float)($_POST['price'] ?? 0);
    $durationDays  = (int)($_POST['duration_days'] ?? 1);
    $description   = sanitize_input($_POST['description'] ?? '');
    $itinerary     = sanitize_input($_POST['itinerary'] ?? '');
    $included      = sanitize_input($_POST['included'] ?? '');
    $excluded      = sanitize_input($_POST['excluded'] ?? '');
    $featured      = isset($_POST['featured']) ? 1 : 0;
    $status        = isset($_POST['status']) ? 1 : 0;
    $imageName     = '';

    // Validation
    if (empty($title) || empty($location) || empty($category) || empty($description)) {
        $error = 'Please fill in all required fields.';
    } elseif ($price <= 0) {
        $error = 'Price must be greater than zero.';
    } else {
        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $uploadDir  = '../assets/uploads/';
            $fileExt    = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($fileExt, $allowedExt)) {
                $error = 'Invalid image format. Allowed: JPG, JPEG, PNG, GIF, WEBP.';
            } elseif ($_FILES['image']['size'] > 5_000_000) {
                $error = 'Image size must be less than 5MB.';
            } else {
                // Generate a unique file name to prevent overwrites
                $imageName = uniqid('pkg_', true) . '.' . $fileExt;
                $uploadPath = $uploadDir . $imageName;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $error = 'Failed to upload image. Please try again.';
                    $imageName = '';
                }
            }
        }

        // Handle gallery images (multiple)
        $galleryNames = [];
        if (!empty($_FILES['gallery']['name'][0])) {
            $uploadDir = '../assets/uploads/';
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
        }
        $galleryStr = implode(',', $galleryNames);

        // If no errors, insert into database
        if (empty($error)) {
            $sql = "INSERT INTO packages
                    (title, location, category, price, duration_days, image, gallery,
                     description, itinerary, included, excluded, featured, status)
                    VALUES
                    (:title, :location, :category, :price, :duration, :image, :gallery,
                     :description, :itinerary, :included, :excluded, :featured, :status)";

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
                ':status'      => $status
            ]);

            if ($result) {
                header('Location: packages.php?success=Package added successfully.');
                exit;
            } else {
                $error = 'Failed to add package. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Package - TravelEase Admin</title>

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
            <h5>Add New Package</h5>
        </div>
        <a href="packages.php" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Packages
        </a>
    </div>

    <!-- Error message -->
    <?php if ($error): ?>
        <div class="alert alert-danger alert-custom">
            <i class="fa-solid fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- ---------------------------------------------------------------- -->
    <!-- ADD PACKAGE FORM                                                 -->
    <!-- ---------------------------------------------------------------- -->
    <div class="admin-form-card" data-aos="fade-up">
        <form method="POST" action="add-package.php" enctype="multipart/form-data">
            <div class="row g-3">
                <!-- Title -->
                <div class="col-md-8">
                    <label class="form-label" for="title">Package Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" required
                           value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
                </div>
                <!-- Category -->
                <div class="col-md-4">
                    <label class="form-label" for="category">Category <span class="text-danger">*</span></label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="">Select...</option>
                        <option value="Beach"     <?= ($_POST['category'] ?? '') === 'Beach'     ? 'selected' : '' ?>>Beach</option>
                        <option value="Mountain"  <?= ($_POST['category'] ?? '') === 'Mountain'  ? 'selected' : '' ?>>Mountain</option>
                        <option value="Adventure" <?= ($_POST['category'] ?? '') === 'Adventure' ? 'selected' : '' ?>>Adventure</option>
                        <option value="City"      <?= ($_POST['category'] ?? '') === 'City'      ? 'selected' : '' ?>>City</option>
                        <option value="Cultural"  <?= ($_POST['category'] ?? '') === 'Cultural'  ? 'selected' : '' ?>>Cultural</option>
                    </select>
                </div>

                <!-- Location -->
                <div class="col-md-6">
                    <label class="form-label" for="location">Location <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="location" name="location" required
                           value="<?= isset($_POST['location']) ? htmlspecialchars($_POST['location']) : '' ?>">
                </div>
                <!-- Price -->
                <div class="col-md-3">
                    <label class="form-label" for="price">Price ($) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" required
                           value="<?= isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '' ?>">
                </div>
                <!-- Duration -->
                <div class="col-md-3">
                    <label class="form-label" for="duration_days">Duration (days) <span class="text-danger">*</span></label>
                    <input type="number" min="1" class="form-control" id="duration_days" name="duration_days" required
                           value="<?= isset($_POST['duration_days']) ? (int)$_POST['duration_days'] : 1 ?>">
                </div>

                <!-- Main Image -->
                <div class="col-md-6">
                    <label class="form-label" for="image">Main Image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <small class="text-muted">JPG, JPEG, PNG, GIF, WEBP. Max 5MB.</small>
                </div>
                <!-- Gallery Images -->
                <div class="col-md-6">
                    <label class="form-label" for="gallery">Gallery Images (optional)</label>
                    <input type="file" class="form-control" id="gallery" name="gallery[]" accept="image/*" multiple>
                    <small class="text-muted">You can select multiple images.</small>
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label class="form-label" for="description">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="4" required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                </div>

                <!-- Itinerary -->
                <div class="col-12">
                    <label class="form-label" for="itinerary">Itinerary (one day per line)</label>
                    <textarea class="form-control" id="itinerary" name="itinerary" rows="4"
                              placeholder="Day 1: Arrival and welcome dinner&#10;Day 2: City tour..."><?= isset($_POST['itinerary']) ? htmlspecialchars($_POST['itinerary']) : '' ?></textarea>
                </div>

                <!-- Included -->
                <div class="col-md-6">
                    <label class="form-label" for="included">What's Included (one per line)</label>
                    <textarea class="form-control" id="included" name="included" rows="4"
                              placeholder="4 nights hotel&#10;Daily breakfast&#10;Airport transfers"><?= isset($_POST['included']) ? htmlspecialchars($_POST['included']) : '' ?></textarea>
                </div>
                <!-- Excluded -->
                <div class="col-md-6">
                    <label class="form-label" for="excluded">What's Excluded (one per line)</label>
                    <textarea class="form-control" id="excluded" name="excluded" rows="4"
                              placeholder="International flights&#10;Personal expenses&#10;Travel insurance"><?= isset($_POST['excluded']) ? htmlspecialchars($_POST['excluded']) : '' ?></textarea>
                </div>

                <!-- Featured & Status -->
                <div class="col-md-6">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="featured" name="featured"
                               <?= isset($_POST['featured']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="featured">Show as Featured Package</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                        <label class="form-check-label" for="status">Published (visible on website)</label>
                    </div>
                </div>

                <!-- Submit -->
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary rounded-pill px-5">
                        <i class="fa-solid fa-plus me-2"></i> Add Package
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
