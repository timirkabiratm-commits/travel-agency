<?php
/**
 * ============================================================================
 * TravelEase - Booking Page
 * File: booking.php
 *
 * Displays a booking form where customers can book a tour package.
 * On form submission (POST), the booking is saved into the database.
 * Also handles AJAX booking submissions (returns JSON).
 * ============================================================================
 */

require_once 'config/db.php';

$page_title    = 'Book Your Tour - TravelEase';
$current_page  = 'packages';

require_once 'includes/header.php';
require_once 'includes/navbar.php';

// ----------------------------------------------------------------------------
// Handle AJAX booking submission (returns JSON response)
// ----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_booking'])) {
    header('Content-Type: application/json');

    $packageId      = (int)($_POST['package_id'] ?? 0);
    $customerName   = sanitize_input($_POST['customer_name'] ?? '');
    $customerEmail  = sanitize_input($_POST['customer_email'] ?? '');
    $customerPhone  = sanitize_input($_POST['customer_phone'] ?? '');
    $travelers      = max(1, (int)($_POST['travelers'] ?? 1));
    $travelDate     = sanitize_input($_POST['travel_date'] ?? '');
    $specialRequest = sanitize_input($_POST['special_request'] ?? '');

    // Basic validation
    $errors = [];
    if ($packageId <= 0)  $errors[] = 'Invalid package selected.';
    if (empty($customerName))  $errors[] = 'Name is required.';
    if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (empty($customerPhone)) $errors[] = 'Phone number is required.';
    if (empty($travelDate))    $errors[] = 'Travel date is required.';

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
        exit;
    }

    // Fetch package price to calculate total
    $stmt = $pdo->prepare("SELECT price, title FROM packages WHERE id = :id AND status = 1");
    $stmt->execute([':id' => $packageId]);
    $pkg = $stmt->fetch();

    if (!$pkg) {
        echo json_encode(['success' => false, 'message' => 'Package not found.']);
        exit;
    }

    $totalPrice = $travelers * (float)$pkg['price'];

    // Insert booking using prepared statement
    $insertSql = "INSERT INTO bookings
        (package_id, customer_name, customer_email, customer_phone, travelers,
         travel_date, special_request, total_price, booking_status)
        VALUES
        (:package_id, :name, :email, :phone, :travelers,
         :travel_date, :special_request, :total_price, 'pending')";

    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->execute([
        ':package_id'      => $packageId,
        ':name'            => $customerName,
        ':email'           => $customerEmail,
        ':phone'           => $customerPhone,
        ':travelers'        => $travelers,
        ':travel_date'      => $travelDate,
        ':special_request'  => $specialRequest,
        ':total_price'      => $totalPrice
    ]);

    echo json_encode([
        'success'  => true,
        'message'  => 'Booking confirmed! We will contact you shortly.',
        'redirect' => 'packages.php'
    ]);
    exit;
}

// ----------------------------------------------------------------------------
// Handle regular (non-AJAX) POST submission — fallback
// ----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax_booking'])) {
    $packageId      = (int)($_POST['package_id'] ?? 0);
    $customerName   = sanitize_input($_POST['customer_name'] ?? '');
    $customerEmail  = sanitize_input($_POST['customer_email'] ?? '');
    $customerPhone  = sanitize_input($_POST['customer_phone'] ?? '');
    $travelers      = max(1, (int)($_POST['travelers'] ?? 1));
    $travelDate     = sanitize_input($_POST['travel_date'] ?? '');
    $specialRequest = sanitize_input($_POST['special_request'] ?? '');

    if ($packageId > 0 && !empty($customerName) && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare("SELECT price FROM packages WHERE id = :id AND status = 1");
        $stmt->execute([':id' => $packageId]);
        $pkg = $stmt->fetch();

        if ($pkg) {
            $totalPrice = $travelers * (float)$pkg['price'];

            $insertSql = "INSERT INTO bookings
                (package_id, customer_name, customer_email, customer_phone, travelers,
                 travel_date, special_request, total_price, booking_status)
                VALUES
                (:package_id, :name, :email, :phone, :travelers,
                 :travel_date, :special_request, :total_price, 'pending')";

            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute([
                ':package_id'      => $packageId,
                ':name'            => $customerName,
                ':email'           => $customerEmail,
                ':phone'           => $customerPhone,
                ':travelers'        => $travelers,
                ':travel_date'      => $travelDate,
                ':special_request'  => $specialRequest,
                ':total_price'      => $totalPrice
            ]);

            $bookingSuccess = true;
        }
    }
}

// ----------------------------------------------------------------------------
// Fetch package details for the booking sidebar
// ----------------------------------------------------------------------------
$packageId = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_POST['package_id'] ?? 0);

if ($packageId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM packages WHERE id = :id AND status = 1");
    $stmt->execute([':id' => $packageId]);
    $pkg = $stmt->fetch();
}

if (empty($pkg)) {
    // No package selected — show message
    echo '<section class="booking-section">';
    echo '<div class="container"><div class="row justify-content-center"><div class="col-lg-8 text-center">';
    echo '<i class="fa-solid fa-triangle-exclamation fa-3x text-warning mb-3"></i>';
    echo '<h2 class="mb-3">No Package Selected</h2>';
    echo '<p class="text-muted mb-4">Please choose a package to book your tour.</p>';
    echo '<a href="packages.php" class="btn btn-primary rounded-pill px-4"><i class="fa-solid fa-arrow-left me-1"></i> Browse Packages</a>';
    echo '</div></div></div>';
    echo '</section>';
    require_once 'includes/footer.php';
    exit;
}

// Determine image path
$mainImage = 'assets/uploads/' . htmlspecialchars($pkg['image']);
if (empty($pkg['image']) || !file_exists('assets/uploads/' . $pkg['image'])) {
    $stockMap = [
        'Maldives' => '3224168', 'Switzerland' => '417074', 'Tanzania' => '7224109',
        'France' => '2363', 'Indonesia' => '2422256', 'Morocco' => '3278215',
        'Italy' => '2382884', 'Thailand' => '2467125',
    ];
    $stockId   = isset($stockMap[$pkg['location']]) ? $stockMap[$pkg['location']] : '1000653';
    $mainImage = "https://images.pexels.com/photos/{$stockId}/pexels-photo-{$stockId}.jpeg?auto=compress&cs=tinysrgb&w=800";
}
?>

<!-- ============================================================================ -->
<!-- BOOKING SECTION                                                               -->
<!-- ============================================================================ -->
<section class="booking-section">
    <div class="container">
        <!-- Success message (non-AJAX fallback) -->
        <?php if (isset($bookingSuccess) && $bookingSuccess): ?>
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8">
                    <div class="alert alert-success alert-custom alert-auto-dismiss">
                        <i class="fa-solid fa-check-circle me-2"></i>
                        Booking confirmed! We will contact you shortly.
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- ---------------------------------------------------------------- -->
            <!-- BOOKING FORM                                                      -->
            <!-- ---------------------------------------------------------------- -->
            <div class="col-lg-8">
                <div class="booking-form-card" data-aos="fade-up">
                    <h2 class="mb-1"><i class="fa-solid fa-calendar-check text-primary me-2"></i>Book Your Tour</h2>
                    <p class="text-muted mb-4">Fill in your details below and we'll confirm your booking.</p>

                    <!-- AJAX message container -->
                    <div id="bookingMsg"></div>

                    <form id="bookingForm" method="POST" action="booking.php">
                        <!-- Hidden field for AJAX detection -->
                        <input type="hidden" name="ajax_booking" value="1">
                        <!-- Hidden field for package ID -->
                        <input type="hidden" name="package_id" value="<?= (int)$pkg['id'] ?>">
                        <!-- Hidden field for total price (updated by JS) -->
                        <input type="hidden" name="total_price" id="totalPriceInput" value="<?= number_format($pkg['price'], 2) ?>">
                        <!-- Hidden field for package price (used by JS) -->
                        <input type="hidden" id="packagePrice" value="<?= (float)$pkg['price'] ?>">

                        <div class="row g-3">
                            <!-- Customer Name -->
                            <div class="col-md-6">
                                <label class="form-label" for="customerName">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customerName" name="customer_name"
                                       placeholder="John Smith" required>
                            </div>

                            <!-- Customer Email -->
                            <div class="col-md-6">
                                <label class="form-label" for="customerEmail">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="customerEmail" name="customer_email"
                                       placeholder="john@email.com" required>
                            </div>

                            <!-- Customer Phone -->
                            <div class="col-md-6">
                                <label class="form-label" for="customerPhone">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="customerPhone" name="customer_phone"
                                       placeholder="+1 555 123 4567" required>
                            </div>

                            <!-- Number of Travelers -->
                            <div class="col-md-6">
                                <label class="form-label" for="travelers">Number of Travelers <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="travelers" name="travelers"
                                       min="1" max="20" value="1" required>
                            </div>

                            <!-- Travel Date -->
                            <div class="col-md-6">
                                <label class="form-label" for="travelDate">Travel Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="travelDate" name="travel_date"
                                       min="<?= date('Y-m-d') ?>" required>
                            </div>

                            <!-- Special Request -->
                            <div class="col-12">
                                <label class="form-label" for="specialRequest">Special Requests (optional)</label>
                                <textarea class="form-control" id="specialRequest" name="special_request" rows="4"
                                          placeholder="Any dietary requirements, accessibility needs, or special occasions..."></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 mt-4">
                                <button type="submit" id="bookingSubmitBtn" class="btn btn-primary btn-lg rounded-pill px-5">
                                    <i class="fa-solid fa-check me-2"></i> Confirm Booking
                                </button>
                                <a href="package-details.php?id=<?= (int)$pkg['id'] ?>" class="btn btn-outline-secondary btn-lg rounded-pill px-4 ms-2">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ---------------------------------------------------------------- -->
            <!-- BOOKING SUMMARY SIDEBAR                                          -->
            <!-- ---------------------------------------------------------------- -->
            <div class="col-lg-4">
                <div class="booking-summary" data-aos="fade-left">
                    <h5 class="mb-3"><i class="fa-solid fa-suitcase text-primary me-2"></i>Booking Summary</h5>

                    <!-- Package image -->
                    <img src="<?= $mainImage ?>" class="w-100 rounded-3 mb-3"
                         alt="<?= htmlspecialchars($pkg['title']) ?>"
                         style="height: 180px; object-fit: cover;">

                    <h6 class="fw-bold"><?= htmlspecialchars($pkg['title']) ?></h6>
                    <p class="text-muted small mb-3">
                        <i class="fa-solid fa-location-dot text-primary me-1"></i>
                        <?= htmlspecialchars($pkg['location']) ?>
                    </p>

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
                        <span>Travelers</span>
                        <span id="summaryTravelers">1</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Price</span>
                        <span id="totalPrice">$<?= number_format($pkg['price'], 2) ?></span>
                    </div>

                    <hr class="my-3">

                    <div class="text-center">
                        <p class="text-muted small mb-2"><i class="fa-solid fa-shield-halved text-success me-1"></i> Secure Booking</p>
                        <p class="text-muted small mb-2"><i class="fa-solid fa-rotate-left text-success me-1"></i> Free Cancellation up to 7 days</p>
                        <p class="text-muted small mb-0"><i class="fa-solid fa-headset text-success me-1"></i> 24/7 Customer Support</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Back to top button -->
<button class="back-to-top" aria-label="Back to top">
    <i class="fa-solid fa-arrow-up"></i>
</button>

<?php require_once 'includes/footer.php'; ?>
