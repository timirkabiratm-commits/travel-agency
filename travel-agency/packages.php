<?php
/**
 * ============================================================================
 * TravelEase - Packages Listing Page
 * File: packages.php
 *
 * Displays all published tour packages with search, category filter,
 * price filter, and sorting. Supports both AJAX (for dynamic filtering)
 * and non-AJAX (direct page load / fallback) modes.
 *
 * When the request is an AJAX call (detected via the "ajax" GET parameter
 * or X-Requested-With header), only the package grid HTML is returned.
 * Otherwise the full page (header, navbar, filters, footer) is rendered.
 * ============================================================================
 */

require_once 'config/db.php';

$page_title    = 'Tour Packages - TravelEase';
$current_page  = 'packages';

// Detect AJAX request — if true, we return only the package grid HTML
$isAjax = (isset($_GET['ajax']) && $_GET['ajax'] == 1)
       || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

// ----------------------------------------------------------------------------
// Read filter parameters from the URL (works for both AJAX and normal load)
// ----------------------------------------------------------------------------
$search     = isset($_GET['search'])     ? trim($_GET['search'])         : '';
$category   = isset($_GET['category'])   ? trim($_GET['category'])       : '';
$sort       = isset($_GET['sort'])       ? trim($_GET['sort'])           : 'newest';
$maxPrice   = isset($_GET['max_price'])  ? (float)$_GET['max_price']     : 0;
$page       = isset($_GET['page'])       ? max(1, (int)$_GET['page'])    : 1;
$perPage    = 6; // Number of packages per page

// ----------------------------------------------------------------------------
// Build the SQL query with prepared statements
// ----------------------------------------------------------------------------
$where  = "status = 1";
$params = [];

if (!empty($search)) {
    $where .= " AND (title LIKE :search OR location LIKE :search2)";
    $params[':search']  = "%{$search}%";
    $params[':search2'] = "%{$search}%";
}
if (!empty($category)) {
    $where .= " AND category = :category";
    $params[':category'] = $category;
}
if ($maxPrice > 0) {
    $where .= " AND price <= :max_price";
    $params[':max_price'] = $maxPrice;
}

// Sorting
switch ($sort) {
    case 'price_low':
        $orderBy = "price ASC";
        break;
    case 'price_high':
        $orderBy = "price DESC";
        break;
    case 'duration':
        $orderBy = "duration_days DESC";
        break;
    default:
        $orderBy = "created_at DESC"; // newest
}

// Count total matching packages for pagination
$countSql  = "SELECT COUNT(*) FROM packages WHERE {$where}";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRecords = (int)$countStmt->fetchColumn();
$totalPages   = $totalRecords > 0 ? ceil($totalRecords / $perPage) : 1;
$offset       = ($page - 1) * $perPage;

// Fetch packages for the current page
$dataSql = "SELECT * FROM packages WHERE {$where} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
$dataStmt = $pdo->prepare($dataSql);
$dataStmt->execute($params);
$packages = $dataStmt->fetchAll();

// ----------------------------------------------------------------------------
// Helper: get a fallback Pexels image for a location
// ----------------------------------------------------------------------------
function getStockPhoto($location) {
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

// ----------------------------------------------------------------------------
// AJAX MODE: output only the package grid and pagination, then stop
// ----------------------------------------------------------------------------
if ($isAjax) {
    renderPackageGrid($packages, $page, $totalPages, $totalRecords);
    exit; // Stop execution — don't render the full page
}

// ----------------------------------------------------------------------------
// NON-AJAX MODE: render the full page with header, filters, and footer
// ----------------------------------------------------------------------------
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- ============================================================================ -->
<!-- PAGE BANNER                                                                   -->
<!-- ============================================================================ -->
<section class="page-banner">
    <div class="container">
        <h1 data-aos="fade-up">Tour Packages</h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Packages</li>
            </ol>
        </nav>
    </div>
</section>

<!-- ============================================================================ -->
<!-- PACKAGES SECTION (with sidebar filters)                                      -->
<!-- ============================================================================ -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <!-- ---------------------------------------------------------------- -->
            <!-- FILTER SIDEBAR                                                   -->
            <!-- ---------------------------------------------------------------- -->
            <div class="col-lg-3 mb-4">
                <form id="filterForm" data-aos="fade-up">
                    <div class="filter-sidebar">
                        <h5><i class="fa-solid fa-filter me-2"></i>Filter Packages</h5>

                        <!-- Search -->
                        <div class="filter-group">
                            <label class="form-label fw-semibold">Search</label>
                            <input type="text" id="searchInput" name="search"
                                   class="form-control" placeholder="Destination or title"
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>

                        <!-- Category -->
                        <div class="filter-group">
                            <label class="form-label fw-semibold">Category</label>
                            <select id="categoryFilter" name="category" class="form-select">
                                <option value="">All Categories</option>
                                <option value="Beach"     <?= $category === 'Beach'     ? 'selected' : '' ?>>Beach</option>
                                <option value="Mountain"  <?= $category === 'Mountain'  ? 'selected' : '' ?>>Mountain</option>
                                <option value="Adventure" <?= $category === 'Adventure' ? 'selected' : '' ?>>Adventure</option>
                                <option value="City"      <?= $category === 'City'      ? 'selected' : '' ?>>City</option>
                                <option value="Cultural"  <?= $category === 'Cultural'  ? 'selected' : '' ?>>Cultural</option>
                            </select>
                        </div>

                        <!-- Max Price -->
                        <div class="filter-group">
                            <label class="form-label fw-semibold">Max Price</label>
                            <input type="range" id="maxPrice" name="max_price"
                                   class="form-range" min="500" max="5000" step="100"
                                   value="<?= $maxPrice > 0 ? (int)$maxPrice : 5000 ?>">
                            <div class="price-range-display">
                                <span>$500</span>
                                <span id="priceDisplay">$<?= $maxPrice > 0 ? number_format($maxPrice) : '5,000' ?></span>
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="filter-group">
                            <label class="form-label fw-semibold">Sort By</label>
                            <select id="sortFilter" name="sort" class="form-select">
                                <option value="newest"     <?= $sort === 'newest'     ? 'selected' : '' ?>>Newest First</option>
                                <option value="price_low"  <?= $sort === 'price_low'  ? 'selected' : '' ?>>Price: Low to High</option>
                                <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                                <option value="duration"   <?= $sort === 'duration'   ? 'selected' : '' ?>>Longest Duration</option>
                            </select>
                        </div>

                        <!-- Clear Filters -->
                        <button type="button" id="clearFilters" class="btn btn-outline-secondary w-100">
                            <i class="fa-solid fa-rotate-left me-1"></i> Clear Filters
                        </button>
                    </div>
                </form>
            </div>

            <!-- ---------------------------------------------------------------- -->
            <!-- PACKAGES GRID (loaded via AJAX into this container)              -->
            <!-- ---------------------------------------------------------------- -->
            <div class="col-lg-9">
                <div id="packagesContainer" class="row g-4">
                    <!-- Initial server-side render so the page works without JS too -->
                    <?php renderPackageGrid($packages, $page, $totalPages, $totalRecords); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Back to top button -->
<button class="back-to-top" aria-label="Back to top">
    <i class="fa-solid fa-arrow-up"></i>
</button>

<?php
require_once 'includes/footer.php';


/**
 * ===========================================================================
 * Helper function: renderPackageGrid()
 *
 * Outputs the HTML for the package cards grid and pagination controls.
 * Called both during initial page load (server-side) and via AJAX.
 *
 * @param array  $packages     Array of package rows from the database
 * @param int    $currentPage  Current page number
 * @param int    $totalPages   Total number of pages
 * @param int    $totalRecords Total matching records
 * ===========================================================================
 */
function renderPackageGrid($packages, $currentPage, $totalPages, $totalRecords) {
    // If no packages found, show a friendly message
    if (empty($packages)) {
        echo '<div class="col-12"><div class="no-results">';
        echo '<i class="fa-solid fa-magnifying-glass"></i>';
        echo '<h4 class="mt-3">No packages found</h4>';
        echo '<p>Try adjusting your filters or search keywords.</p>';
        echo '</div></div>';
        return;
    }
    ?>
    <!-- Results count -->
    <div class="col-12 mb-3">
        <p class="text-muted mb-0">
            <i class="fa-solid fa-list me-1"></i>
            Showing <strong><?= count($packages) ?></strong> of <strong><?= $totalRecords ?></strong> packages
        </p>
    </div>

    <?php
    $delay = 0;
    foreach ($packages as $pkg):
        // Determine image path
        $imagePath = 'assets/uploads/' . htmlspecialchars($pkg['image']);
        if (empty($pkg['image']) || !file_exists('assets/uploads/' . $pkg['image'])) {
            $imagePath = getStockPhoto($pkg['location']);
        }
    ?>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
            <div class="package-card">
                <div class="image-wrap">
                    <img src="<?= $imagePath ?>" class="card-img-top" alt="<?= htmlspecialchars($pkg['title']) ?>">
                    <span class="category-badge"><?= htmlspecialchars($pkg['category']) ?></span>
                    <?php if ($pkg['featured']): ?>
                        <span class="featured-badge"><i class="fa-solid fa-star me-1"></i>Featured</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($pkg['title']) ?></h5>
                    <p class="location"><i class="fa-solid fa-location-dot me-1 text-primary"></i><?= htmlspecialchars($pkg['location']) ?></p>
                    <p class="card-text"><?= htmlspecialchars(substr($pkg['description'], 0, 100)) ?>...</p>
                    <div class="package-meta">
                        <span class="duration"><i class="fa-regular fa-clock"></i> <?= (int)$pkg['duration_days'] ?> Days</span>
                        <span class="price">$<?= number_format($pkg['price'], 0) ?><small>/person</small></span>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <a href="package-details.php?id=<?= (int)$pkg['id'] ?>" class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="fa-solid fa-eye me-1"></i> Details
                        </a>
                        <a href="booking.php?id=<?= (int)$pkg['id'] ?>" class="btn btn-primary btn-sm flex-fill">
                            <i class="fa-solid fa-bookmark me-1"></i> Book
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php
        $delay += 100;
        if ($delay > 300) $delay = 0;
    endforeach;
    ?>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="col-12 mt-4">
            <nav aria-label="Package pagination">
                <ul class="pagination justify-content-center">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="<?= $currentPage - 1 ?>">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="#" data-page="<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="<?= $currentPage + 1 ?>">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif;
}
?>
