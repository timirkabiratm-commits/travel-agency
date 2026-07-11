<?php
/**
 * ============================================================================
 * TravelEase - Package Details Page
 * File: package-details.php
 *
 * Displays full details of a single tour package:
 *   - Image gallery
 *   - Description, price, duration
 *   - Day-by-day itinerary
 *   - What's included / excluded
 *   - Booking sidebar with "Book Now" button
 * ============================================================================
 */

require_once 'config/db.php';

$page_title    = 'Package Details - TravelEase';
$current_page  = 'packages';

require_once 'includes/header.php';
require_once 'includes/navbar.php';

// ----------------------------------------------------------------------------
// Get the package ID from the URL (must be a positive integer)
// ----------------------------------------------------------------------------
$packageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($packageId <= 0) {
    // Invalid ID — redirect to packages page
    header('Location: packages.php');
    exit;
}

// Fetch the package using a prepared statement (prevents SQL injection)
$stmt = $pdo->prepare("SELECT * FROM packages WHERE id = :id AND status = 1");
$stmt->execute([':id' => $packageId]);
$pkg = $stmt->fetch();

if (!$pkg) {
    // Package not found — show error and redirect link
    echo '<section class="section-padding text-center" style="margin-top: 100px;">';
    echo '<div class="container">';
    echo '<i class="fa-solid fa-triangle-exclamation fa-3x text-warning mb-3"></i>';
    echo '<h2 class="mb-3">Package Not Found</h2>';
    echo '<p class="text-muted mb-4">The package you are looking for does not exist or has been removed.</p>';
    echo '<a href="packages.php" class="btn btn-primary rounded-pill px-4"><i class="fa-solid fa-arrow-left me-1"></i> Back to Packages</a>';
    echo '</div></section>';
    require_once 'includes/footer.php';
    exit;
}

// Parse the gallery images (stored as comma-separated string)
$galleryImages = !empty($pkg['gallery']) ? explode(',', $pkg['gallery']) : [];

// Determine the main image path
$mainImage = 'assets/uploads/' . htmlspecialchars($pkg['image']);
if (empty($pkg['image']) || !file_exists('assets/uploads/' . $pkg['image'])) {
    // Fallback to a Pexels stock photo based on the location
    $stockMap = [
        'Maldives'    => '3224168',
        'Switzerland' => '417074',
        'Tanzania'    => '7224109',
        'France'      => '2363',
        'Indonesia'   => '2422256',
        'Morocco'     => '3278215',
        'Italy'       => '2382884',
        'Thailand'    => '2467125',
    ];
    $stockId   = isset($stockMap[$pkg['location']]) ? $stockMap[$pkg['location']] : '1000653';
    $mainImage = "https://images.pexels.com/photos/{$stockId}/pexels-photo-{$stockId}.jpeg?auto=compress&cs=tinysrgb&w=1200";
}

// Parse itinerary into an array of lines
$itineraryLines = !empty($pkg['itinerary']) ? explode("\n", $pkg['itinerary']) : [];

// Parse included / excluded into arrays
$includedItems = !empty($pkg['included']) ? explode("\n", $pkg['included']) : [];
$excludedItems = !empty($pkg['excluded']) ? explode("\n", $pkg['excluded']) : [];
?>

<!-- ============================================================================ -->
<!-- PACKAGE HERO (title, location, price)                                        -->
<!-- ============================================================================ -->
<section class="package-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-up">
                <span class="badge bg-primary mb-2 px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-tag me-1"></i> <?= htmlspecialchars($pkg['category']) ?>
                </span>
                <h1><?= htmlspecialchars($pkg['title']) ?></h1>
                <p class="location mb-0">
                    <i class="fa-solid fa-location-dot text-primary me-1"></i>
                    <?= htmlspecialchars($pkg['location']) ?>
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0" data-aos="fade-up" data-aos-delay="100">
                <div class="price-tag">
                    $<?= number_format($pkg['price'], 2) ?>
                    <small>/ person</small>
                </div>
                <div class="mt-2">
                    <a href="booking.php?id=<?= (int)$pkg['id'] ?>" class="btn btn-primary btn-lg rounded-pill px-4">
                        <i class="fa-solid fa-bookmark me-2"></i> Book Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- PACKAGE DETAILS                                                               -->
<!-- ============================================================================ -->
<section class="section-padding">
    <div class="container">
        <div class="row g-4">
            <!-- ---------------------------------------------------------------- -->
            <!-- LEFT COLUMN: Gallery, Description, Itinerary, Inclusions         -->
            <!-- ---------------------------------------------------------------- -->
            <div class="col-lg-8">

                <!-- Main Image -->
                <div class="mb-4" data-aos="fade-up">
                    <img src="<?= $mainImage ?>" class="w-100 rounded-4 shadow-sm"
                         alt="<?= htmlspecialchars($pkg['title']) ?>"
                         style="border-radius: 16px; max-height: 450px; object-fit: cover;">
                </div>

                <!-- Quick Info Boxes -->
                <div class="row g-3 mb-4" data-aos="fade-up">
                    <div class="col-md-4">
                        <div class="detail-info-box">
                            <i class="fa-regular fa-clock"></i>
                            <div>
                                <h6>Duration</h6>
                                <p class="mb-0"><?= (int)$pkg['duration_days'] ?> Days</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-info-box">
                            <i class="fa-solid fa-location-dot"></i>
                            <div>
                                <h6>Location</h6>
                                <p class="mb-0"><?= htmlspecialchars($pkg['location']) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-info-box">
                            <i class="fa-solid fa-tag"></i>
                            <div>
                                <h6>Category</h6>
                                <p class="mb-0"><?= htmlspecialchars($pkg['category']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="detail-card mb-4" data-aos="fade-up">
                    <h3 class="mb-3"><i class="fa-solid fa-align-left text-primary me-2"></i>Overview</h3>
                    <p class="text-muted" style="font-size: 1rem; line-height: 1.8;">
                        <?= nl2br(htmlspecialchars($pkg['description'])) ?>
                    </p>
                </div>

                <!-- Itinerary -->
                <?php if (!empty($itineraryLines)): ?>
                    <div class="detail-card mb-4" data-aos="fade-up">
                        <h3 class="mb-3"><i class="fa-solid fa-route text-primary me-2"></i>Tour Itinerary</h3>
                        <?php foreach ($itineraryLines as $index => $line):
                            $line = trim($line);
                            if (empty($line)) continue;
                        ?>
                            <div class="itinerary-item">
                                <div class="itinerary-day"><?= $index + 1 ?></div>
                                <div class="pt-2">
                                    <p class="mb-0 text-muted"><?= htmlspecialchars($line) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Included & Excluded -->
                <div class="row g-4">
                    <?php if (!empty($includedItems)): ?>
                        <div class="col-md-6" data-aos="fade-up">
                            <div class="detail-card">
                                <h4 class="mb-3 text-success"><i class="fa-solid fa-circle-check me-2"></i>What's Included</h4>
                                <ul class="include-list ps-0">
                                    <?php foreach ($includedItems as $item):
                                        $item = trim($item);
                                        if (empty($item)) continue;
                                    ?>
                                        <li><i class="fa-solid fa-check"></i> <?= htmlspecialchars($item) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($excludedItems)): ?>
                        <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                            <div class="detail-card">
                                <h4 class="mb-3 text-danger"><i class="fa-solid fa-circle-xmark me-2"></i>What's Excluded</h4>
                                <ul class="exclude-list ps-0">
                                    <?php foreach ($excludedItems as $item):
                                        $item = trim($item);
                                        if (empty($item)) continue;
                                    ?>
                                        <li><i class="fa-solid fa-xmark"></i> <?= htmlspecialchars($item) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ---------------------------------------------------------------- -->
            <!-- RIGHT COLUMN: Booking Sidebar                                    -->
            <!-- ---------------------------------------------------------------- -->
            <div class="col-lg-4">
                <div class="booking-sidebar" data-aos="fade-left">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-3">Book This Tour</h4>
                            <div class="summary-row">
                                <span>Price per person</span>
                                <span>$<?= number_format($pkg['price'], 2) ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Duration</span>
                                <span><?= (int)$pkg['duration_days'] ?> Days</span>
                            </div>
                            <div class="summary-row">
                                <span>Category</span>
                                <span><?= htmlspecialchars($pkg['category']) ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Location</span>
                                <span><?= htmlspecialchars($pkg['location']) ?></span>
                            </div>

                            <hr class="my-3">

                            <a href="booking.php?id=<?= (int)$pkg['id'] ?>" class="btn btn-primary w-100 rounded-pill py-2 mb-2">
                                <i class="fa-solid fa-bookmark me-2"></i> Book Now
                            </a>
                            <a href="contact.php" class="btn btn-outline-primary w-100 rounded-pill py-2">
                                <i class="fa-solid fa-envelope me-2"></i> Ask a Question
                            </a>

                            <!-- Trust badges -->
                            <div class="mt-4 text-center">
                                <p class="text-muted small mb-2"><i class="fa-solid fa-shield-halved text-success me-1"></i> Secure Booking</p>
                                <p class="text-muted small mb-2"><i class="fa-solid fa-rotate-left text-success me-1"></i> Free Cancellation up to 7 days</p>
                                <p class="text-muted small mb-0"><i class="fa-solid fa-headset text-success me-1"></i> 24/7 Customer Support</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- RELATED PACKAGES                                                              -->
<!-- ============================================================================ -->
<?php
// Fetch up to 3 related packages from the same category (excluding current)
$relStmt = $pdo->prepare("SELECT * FROM packages WHERE category = :cat AND id != :id AND status = 1 ORDER BY RAND() LIMIT 3");
$relStmt->execute([':cat' => $pkg['category'], ':id' => $packageId]);
$related = $relStmt->fetchAll();

if (!empty($related)):
?>
<section class="section-padding" style="background: var(--gray-100);">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="subtitle">You May Also Like</span>
            <h2>Related Packages</h2>
        </div>
        <div class="row g-4">
            <?php foreach ($related as $r):
                $rImage = 'assets/uploads/' . htmlspecialchars($r['image']);
                if (empty($r['image']) || !file_exists('assets/uploads/' . $r['image'])) {
                    $rImage = getStockPhotoRelated($r['location']);
                }
            ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up">
                    <div class="package-card">
                        <div class="image-wrap">
                            <img src="<?= $rImage ?>" class="card-img-top" alt="<?= htmlspecialchars($r['title']) ?>">
                            <span class="category-badge"><?= htmlspecialchars($r['category']) ?></span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($r['title']) ?></h5>
                            <p class="location"><i class="fa-solid fa-location-dot me-1 text-primary"></i><?= htmlspecialchars($r['location']) ?></p>
                            <div class="package-meta">
                                <span class="duration"><i class="fa-regular fa-clock"></i> <?= (int)$r['duration_days'] ?> Days</span>
                                <span class="price">$<?= number_format($r['price'], 0) ?></span>
                            </div>
                            <a href="package-details.php?id=<?= (int)$r['id'] ?>" class="btn btn-outline-primary btn-sm w-100 mt-3">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Helper for related package images
function getStockPhotoRelated($location) {
    $map = [
        'Maldives'    => '3224168',
        'Switzerland' => '417074',
        'Tanzania'    => '7224109',
        'France'      => '2363',
        'Indonesia'   => '2422256',
        'Morocco'     => '3278215',
        'Italy'       => '2382884',
        'Thailand'    => '2467125',
    ];
    $id = isset($map[$location]) ? $map[$location] : '1000653';
    return "https://images.pexels.com/photos/{$id}/pexels-photo-{$id}.jpeg?auto=compress&cs=tinysrgb&w=800";
}

require_once 'includes/footer.php';
?>
