<?php
/**
 * ============================================================================
 * TravelEase - Home Page
 * File: index.php
 *
 * This is the main landing page of the TravelEase website. It displays:
 *   - Hero banner with search tour form
 *   - Popular destinations
 *   - Featured tour packages (from database)
 *   - Why Choose Us section
 *   - Tour categories
 *   - Customer testimonials (Swiper slider)
 *   - Gallery
 *   - Newsletter / CTA
 * ============================================================================
 */

// Include the database connection and reusable components
require_once 'config/db.php';

// Set the page title (used inside header.php)
$page_title = 'TravelEase - Your Journey Begins Here';

// Track the current page for the navbar active link
$current_page = 'home';

// Include the HTML head and navigation bar
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- ============================================================================ -->
<!-- HERO SECTION                                                                  -->
<!-- ============================================================================ -->
<section class="hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-8" data-aos="fade-up" data-aos-duration="1000">
                <span class="badge bg-primary mb-3 px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-globe me-1"></i> Explore 150+ Destinations
                </span>
                <h1>Discover Your Next <br><span class="text-primary">Adventure</span> with TravelEase</h1>
                <p>From tropical beaches to snow-capped mountains, we curate unforgettable
                   travel experiences tailored just for you. Book your dream vacation today.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="packages.php" class="btn btn-primary btn-lg rounded-pill px-4">
                        <i class="fa-solid fa-compass me-2"></i> Explore Packages
                    </a>
                    <a href="about.php" class="btn btn-outline-light btn-lg rounded-pill px-4">
                        <i class="fa-solid fa-play me-2"></i> Learn More
                    </a>
                </div>
            </div>
        </div>

        <!-- Search Tour Form (Glassmorphism Card) -->
        <div class="row mt-5">
            <div class="col-lg-10" data-aos="fade-up" data-aos-delay="200">
                <div class="search-card">
                    <form action="packages.php" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label"><i class="fa-solid fa-magnifying-glass me-1"></i> Destination</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Where do you want to go?">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="fa-solid fa-tags me-1"></i> Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <option value="Beach">Beach</option>
                                <option value="Mountain">Mountain</option>
                                <option value="Adventure">Adventure</option>
                                <option value="City">City</option>
                                <option value="Cultural">Cultural</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="fa-solid fa-dollar-sign me-1"></i> Max Budget</label>
                            <select name="max_price" class="form-select">
                                <option value="">Any Price</option>
                                <option value="1000">Up to $1,000</option>
                                <option value="1500">Up to $1,500</option>
                                <option value="2000">Up to $2,000</option>
                                <option value="3000">Up to $3,000</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn-search">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- POPULAR DESTINATIONS                                                          -->
<!-- ============================================================================ -->
<section class="section-padding">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="subtitle">Top Picks</span>
            <h2>Popular Destinations</h2>
            <p>Discover the most sought-after travel spots our customers love to visit again and again.</p>
        </div>

        <div class="row g-4">
            <!-- Destination 1 -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="0">
                <a href="packages.php?search=Maldives" class="destination-card d-block">
                    <img src="https://images.pexels.com/photos/3224168/pexels-photo-3224168.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Maldives">
                    <span class="price-badge">From $1,499</span>
                    <div class="destination-overlay">
                        <h5>Maldives</h5>
                        <span class="tours-count"><i class="fa-solid fa-map me-1"></i> 5 Tours Available</span>
                    </div>
                </a>
            </div>

            <!-- Destination 2 -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <a href="packages.php?search=Switzerland" class="destination-card d-block">
                    <img src="https://images.pexels.com/photos/417074/pexels-photo-417074.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Switzerland">
                    <span class="price-badge">From $1,899</span>
                    <div class="destination-overlay">
                        <h5>Switzerland</h5>
                        <span class="tours-count"><i class="fa-solid fa-map me-1"></i> 4 Tours Available</span>
                    </div>
                </a>
            </div>

            <!-- Destination 3 -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <a href="packages.php?search=Tanzania" class="destination-card d-block">
                    <img src="https://images.pexels.com/photos/7224109/pexels-photo-7224109.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Tanzania">
                    <span class="price-badge">From $2,199</span>
                    <div class="destination-overlay">
                        <h5>Tanzania</h5>
                        <span class="tours-count"><i class="fa-solid fa-map me-1"></i> 3 Tours Available</span>
                    </div>
                </a>
            </div>

            <!-- Destination 4 -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="0">
                <a href="packages.php?search=Italy" class="destination-card d-block">
                    <img src="https://images.pexels.com/photos/2382884/pexels-photo-2382884.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Italy">
                    <span class="price-badge">From $1,699</span>
                    <div class="destination-overlay">
                        <h5>Italy</h5>
                        <span class="tours-count"><i class="fa-solid fa-map me-1"></i> 4 Tours Available</span>
                    </div>
                </a>
            </div>

            <!-- Destination 5 -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <a href="packages.php?search=Thailand" class="destination-card d-block">
                    <img src="https://images.pexels.com/photos/2467125/pexels-photo-2467125.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Thailand">
                    <span class="price-badge">From $1,199</span>
                    <div class="destination-overlay">
                        <h5>Thailand</h5>
                        <span class="tours-count"><i class="fa-solid fa-map me-1"></i> 6 Tours Available</span>
                    </div>
                </a>
            </div>

            <!-- Destination 6 -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <a href="packages.php?search=France" class="destination-card d-block">
                    <img src="https://images.pexels.com/photos/2363/france-landmark-lights-night.jpg?auto=compress&cs=tinysrgb&w=800" alt="France">
                    <span class="price-badge">From $999</span>
                    <div class="destination-overlay">
                        <h5>France</h5>
                        <span class="tours-count"><i class="fa-solid fa-map me-1"></i> 3 Tours Available</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- FEATURED TOUR PACKAGES (Dynamic from Database)                               -->
<!-- ============================================================================ -->
<section class="section-padding" style="background: var(--gray-100);">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="subtitle">Handpicked</span>
            <h2>Featured Tour Packages</h2>
            <p>Our most popular travel experiences, loved by thousands of happy travelers worldwide.</p>
        </div>

        <div class="row g-4">
            <?php
            // Fetch featured packages (featured = 1) and published (status = 1)
            $sql = "SELECT * FROM packages WHERE featured = 1 AND status = 1 ORDER BY created_at DESC LIMIT 6";
            $stmt = $pdo->query($sql);

            $delay = 0;
            while ($pkg = $stmt->fetch()):
                // Build the image path — use uploaded image or fallback to a Pexels placeholder
                $imagePath = 'assets/uploads/' . htmlspecialchars($pkg['image']);
                // If the uploaded file does not exist on disk, use a Pexels stock photo
                if (!file_exists('assets/uploads/' . $pkg['image']) || empty($pkg['image'])) {
                    $imagePath = 'https://images.pexels.com/photos/' . getStockPhotoId($pkg['location']) . '/pexels-photo-' . getStockPhotoId($pkg['location']) . '.jpeg?auto=compress&cs=tinysrgb&w=800';
                }
            ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                    <div class="package-card">
                        <div class="image-wrap">
                            <img src="<?= $imagePath ?>" class="card-img-top" alt="<?= htmlspecialchars($pkg['title']) ?>">
                            <span class="category-badge"><?= htmlspecialchars($pkg['category']) ?></span>
                            <span class="featured-badge"><i class="fa-solid fa-star me-1"></i>Featured</span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($pkg['title']) ?></h5>
                            <p class="location"><i class="fa-solid fa-location-dot me-1 text-primary"></i><?= htmlspecialchars($pkg['location']) ?></p>
                            <p class="card-text"><?= htmlspecialchars(substr($pkg['description'], 0, 100)) ?>...</p>
                            <div class="package-meta">
                                <span class="duration">
                                    <i class="fa-regular fa-clock"></i> <?= (int)$pkg['duration_days'] ?> Days
                                </span>
                                <span class="price">$<?= number_format($pkg['price'], 0) ?>
                                    <small>/person</small>
                                </span>
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                <a href="package-details.php?id=<?= (int)$pkg['id'] ?>" class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="fa-solid fa-eye me-1"></i> View Details
                                </a>
                                <a href="booking.php?id=<?= (int)$pkg['id'] ?>" class="btn btn-primary btn-sm flex-fill">
                                    <i class="fa-solid fa-bookmark me-1"></i> Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                $delay += 100;
                if ($delay > 300) $delay = 0;
            endwhile;
            ?>
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="packages.php" class="btn btn-primary btn-lg rounded-pill px-5">
                View All Packages <i class="fa-solid fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- WHY CHOOSE US                                                                 -->
<!-- ============================================================================ -->
<section class="section-padding">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="subtitle">Why TravelEase</span>
            <h2>Why Choose Us</h2>
            <p>We go the extra mile to make sure your journey is safe, smooth, and unforgettable.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <h5>Safe & Secure</h5>
                    <p>Your safety is our top priority. We partner with trusted local guides and certified hotels.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-tags"></i></div>
                    <h5>Best Price</h5>
                    <p>Guaranteed best rates with no hidden fees. Get the most value for your travel budget.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-headset"></i></div>
                    <h5>24/7 Support</h5>
                    <p>Our travel experts are available round the clock to assist you wherever you are.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-medal"></i></div>
                    <h5>Expert Guides</h5>
                    <p>Experienced local guides who know every corner and hidden gem of your destination.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- TOUR CATEGORIES                                                               -->
<!-- ============================================================================ -->
<section class="section-padding" style="background: var(--gray-100);">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="subtitle">Browse by Type</span>
            <h2>Tour Categories</h2>
            <p>Find the perfect trip by choosing the type of experience you're looking for.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="0">
                <a href="packages.php?category=Beach" class="category-card d-block">
                    <div class="category-bg" style="background-image: url('https://images.pexels.com/photos/635279/pexels-photo-635279.jpeg?auto=compress&cs=tinysrgb&w=600');"></div>
                    <div class="category-overlay">
                        <i class="fa-solid fa-umbrella-beach"></i>
                        <h5>Beach Tours</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="100">
                <a href="packages.php?category=Mountain" class="category-card d-block">
                    <div class="category-bg" style="background-image: url('https://images.pexels.com/photos/417074/pexels-photo-417074.jpeg?auto=compress&cs=tinysrgb&w=600');"></div>
                    <div class="category-overlay">
                        <i class="fa-solid fa-mountain"></i>
                        <h5>Mountain Tours</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="200">
                <a href="packages.php?category=Adventure" class="category-card d-block">
                    <div class="category-bg" style="background-image: url('https://images.pexels.com/photos/2422588/pexels-photo-2422588.jpeg?auto=compress&cs=tinysrgb&w=600');"></div>
                    <div class="category-overlay">
                        <i class="fa-solid fa-person-hiking"></i>
                        <h5>Adventure Tours</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="300">
                <a href="packages.php?category=City" class="category-card d-block">
                    <div class="category-bg" style="background-image: url('https://images.pexels.com/photos/2363/france-landmark-lights-night.jpg?auto=compress&cs=tinysrgb&w=600');"></div>
                    <div class="category-overlay">
                        <i class="fa-solid fa-city"></i>
                        <h5>City Tours</h5>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- CUSTOMER TESTIMONIALS (Swiper Slider)                                         -->
<!-- ============================================================================ -->
<section class="section-padding testimonial-section">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="subtitle">Testimonials</span>
            <h2>What Our Travelers Say</h2>
            <p>Real stories from real travelers who explored the world with TravelEase.</p>
        </div>

        <div class="swiper testimonialSwiper" data-aos="fade-up" data-aos-delay="200">
            <div class="swiper-wrapper">
                <!-- Testimonial 1 -->
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <i class="fa-solid fa-quote-right quote-icon"></i>
                        <div class="stars">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <p class="testimonial-text">"The Maldives package was beyond our expectations. Everything from airport pickup to the overwater villa was perfectly organized. TravelEase made our honeymoon truly magical!"</p>
                        <div class="testimonial-author">
                            <img src="https://images.pexels.com/photos/220457/pexels-photo-220457.jpeg?auto=compress&cs=tinysrgb&w=120" alt="Jennifer Adams">
                            <div>
                                <h6>Jennifer Adams</h6>
                                <span>Maldives Trip, 2025</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <i class="fa-solid fa-quote-right quote-icon"></i>
                        <div class="stars">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <p class="testimonial-text">"Swiss Alps Adventure was the trip of a lifetime! The mountain views, the train rides, and the charming villages — everything was perfectly planned. Highly recommend TravelEase!"</p>
                        <div class="testimonial-author">
                            <img src="https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg?auto=compress&cs=tinysrgb&w=120" alt="Michael Chen">
                            <div>
                                <h6>Michael Chen</h6>
                                <span>Switzerland Trip, 2025</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <i class="fa-solid fa-quote-right quote-icon"></i>
                        <div class="stars">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <p class="testimonial-text">"The Serengeti safari was incredible! Seeing the Great Migration up close was a dream come true. Our guide was knowledgeable and the tented camp was luxurious."</p>
                        <div class="testimonial-author">
                            <img src="https://images.pexels.com/photos/733872/pexels-photo-733872.jpeg?auto=compress&cs=tinysrgb&w=120" alt="Sarah Williams">
                            <div>
                                <h6>Sarah Williams</h6>
                                <span>Tanzania Safari, 2025</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 4 -->
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <i class="fa-solid fa-quote-right quote-icon"></i>
                        <div class="stars">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star-half-stroke"></i>
                        </div>
                        <p class="testimonial-text">"Bali Cultural Journey exceeded all expectations. The temples, the rice terraces, and the cooking class were unforgettable. The team was always available to help."</p>
                        <div class="testimonial-author">
                            <img src="https://images.pexels.com/photos/762020/pexels-photo-762020.jpeg?auto=compress&cs=tinysrgb&w=120" alt="David Kumar">
                            <div>
                                <h6>David Kumar</h6>
                                <span>Bali Trip, 2025</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination mt-4"></div>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- GALLERY                                                                       -->
<!-- ============================================================================ -->
<section class="section-padding">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="subtitle">Gallery</span>
            <h2>Travel Moments</h2>
            <p>A glimpse of the beautiful moments captured by our travelers around the world.</p>
        </div>

        <div class="row g-3">
            <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                <div class="gallery-item">
                    <img src="https://images.pexels.com/photos/1287460/pexels-photo-1287460.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Gallery 1">
                    <div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="gallery-item">
                    <img src="https://images.pexels.com/photos/2422256/pexels-photo-2422256.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Gallery 2">
                    <div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="gallery-item">
                    <img src="https://images.pexels.com/photos/3155661/pexels-photo-3155661.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Gallery 3">
                    <div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="gallery-item">
                    <img src="https://images.pexels.com/photos/1271619/pexels-photo-1271619.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Gallery 4">
                    <div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                <div class="gallery-item">
                    <img src="https://images.pexels.com/photos/3278215/pexels-photo-3278215.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Gallery 5">
                    <div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="gallery-item">
                    <img src="https://images.pexels.com/photos/3601425/pexels-photo-3601425.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Gallery 6">
                    <div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="gallery-item">
                    <img src="https://images.pexels.com/photos/1450363/pexels-photo-1450363.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Gallery 7">
                    <div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="gallery-item">
                    <img src="https://images.pexels.com/photos/2387873/pexels-photo-2387873.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Gallery 8">
                    <div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- CTA / NEWSLETTER                                                              -->
<!-- ============================================================================ -->
<section class="section-padding">
    <div class="container">
        <div class="cta-section" data-aos="zoom-in">
            <h2>Ready for Your Next Adventure?</h2>
            <p>Subscribe to our newsletter and never miss out on exclusive travel deals, new destinations, and seasonal offers.</p>
            <a href="packages.php" class="btn btn-light-pill btn-lg">
                <i class="fa-solid fa-compass me-2"></i> Browse All Packages
            </a>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- STATS / COUNTERS                                                              -->
<!-- ============================================================================ -->
<section class="section-padding pt-0">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="0">
                <div class="stat-item">
                    <div class="stat-icon"><i class="fa-solid fa-globe"></i></div>
                    <div class="stat-number">150+</div>
                    <div class="stat-label">Destinations</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-item">
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-number">12K+</div>
                    <div class="stat-label">Happy Travelers</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-item">
                    <div class="stat-icon"><i class="fa-solid fa-suitcase"></i></div>
                    <div class="stat-number">850+</div>
                    <div class="stat-label">Tours Completed</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-item">
                    <div class="stat-icon"><i class="fa-solid fa-award"></i></div>
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Years Experience</div>
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
/**
 * Helper function: getStockPhotoId()
 * Returns a Pexels photo ID for a given location name.
 * Used as a fallback when an uploaded image file is not found on disk.
 *
 * @param  string $location  The destination name
 * @return string            A Pexels photo ID
 */
function getStockPhotoId($location) {
    $map = [
        'Maldives'     => '3224168',
        'Switzerland'  => '417074',
        'Tanzania'     => '7224109',
        'France'       => '2363',
        'Indonesia'    => '2422256',
        'Morocco'      => '3278215',
        'Italy'        => '2382884',
        'Thailand'     => '2467125',
    ];
    return isset($map[$location]) ? $map[$location] : '1000653';
}

// Include the footer (closes the HTML document)
require_once 'includes/footer.php';
?>
