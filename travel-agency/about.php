<?php
/**
 * ============================================================================
 * TravelEase - About Page
 * File: about.php
 *
 * Displays company introduction, mission, vision, services, team members,
 * statistics, and why-choose-us section.
 * ============================================================================
 */

require_once 'config/db.php';

$page_title    = 'About Us - TravelEase';
$current_page  = 'about';

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- ============================================================================ -->
<!-- PAGE BANNER                                                                   -->
<!-- ============================================================================ -->
<section class="page-banner">
    <div class="container">
        <h1 data-aos="fade-up">About TravelEase</h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About</li>
            </ol>
        </nav>
    </div>
</section>

<!-- ============================================================================ -->
<!-- COMPANY INTRODUCTION                                                          -->
<!-- ============================================================================ -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="about-img-card">
                    <img src="https://images.pexels.com/photos/2901209/pexels-photo-2901209.jpeg?auto=compress&cs=tinysrgb&w=900"
                         alt="TravelEase Team">
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <span class="badge bg-primary-soft text-primary mb-3 px-3 py-2 rounded-pill"
                      style="background: var(--primary-light); color: var(--primary);">
                    <i class="fa-solid fa-plane me-1"></i> Our Story
                </span>
                <h2 class="mb-3">Crafting Unforgettable Journeys Since 2010</h2>
                <p class="text-muted mb-3">
                    TravelEase was founded with a simple mission: to make extraordinary travel
                    experiences accessible to everyone. What started as a small travel agency
                    has grown into a trusted brand serving thousands of travelers across the globe.
                </p>
                <p class="text-muted mb-4">
                    We believe travel is more than just visiting new places — it's about creating
                    memories, discovering cultures, and returning home with stories that last a
                    lifetime. Our team of passionate travel experts works tirelessly to design
                    journeys that inspire, excite, and delight.
                </p>

                <!-- Mission & Vision Cards -->
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="detail-info-box">
                            <i class="fa-solid fa-bullseye"></i>
                            <div>
                                <h6>Our Mission</h6>
                                <p class="mb-0">Make travel effortless</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="detail-info-box">
                            <i class="fa-solid fa-eye"></i>
                            <div>
                                <h6>Our Vision</h6>
                                <p class="mb-0">Inspire global exploration</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- MISSION & VISION (Detailed)                                                   -->
<!-- ============================================================================ -->
<section class="section-padding" style="background: var(--gray-100);">
    <div class="container">
        <div class="row g-4">
            <!-- Mission -->
            <div class="col-md-6" data-aos="fade-up">
                <div class="detail-card h-100">
                    <div class="feature-icon mb-3" style="margin: 0;">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <h3 class="mb-3">Our Mission</h3>
                    <p class="text-muted">
                        To provide seamless, affordable, and memorable travel experiences by
                        combining expert knowledge, personalized service, and cutting-edge technology.
                        We strive to be the travel partner our customers can rely on for every journey,
                        big or small.
                    </p>
                </div>
            </div>
            <!-- Vision -->
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="detail-card h-100">
                    <div class="feature-icon mb-3" style="margin: 0;">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <h3 class="mb-3">Our Vision</h3>
                    <p class="text-muted">
                        To become the world's most loved travel agency by inspiring people to explore
                        the world, connect with diverse cultures, and create lasting memories. We
                        envision a future where travel is not a luxury but a shared human experience.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- SERVICES                                                                      -->
<!-- ============================================================================ -->
<section class="section-padding">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="subtitle">What We Offer</span>
            <h2>Our Services</h2>
            <p>From planning to execution, we handle every detail so you can focus on enjoying the journey.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="0">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-route"></i></div>
                    <h5>Tour Planning</h5>
                    <p>Customized itineraries crafted by travel experts based on your preferences and budget.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-hotel"></i></div>
                    <h5>Hotel Booking</h5>
                    <p>Handpicked accommodations from budget-friendly stays to luxury resorts worldwide.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-plane"></i></div>
                    <h5>Flight Booking</h5>
                    <p>Best fares on domestic and international flights with flexible booking options.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="0">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-passport"></i></div>
                    <h5>Visa Assistance</h5>
                    <p>Complete visa guidance and documentation support to make your travel hassle-free.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-car"></i></div>
                    <h5>Transport Services</h5>
                    <p>Airport transfers, car rentals, and local transportation arranged by trusted partners.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-user-tie"></i></div>
                    <h5>Travel Guides</h5>
                    <p>Experienced local guides who show you the best of every destination like a local.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- STATISTICS                                                                    -->
<!-- ============================================================================ -->
<section class="section-padding" style="background: var(--primary-50);">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="subtitle">By the Numbers</span>
            <h2>Our Achievements</h2>
            <p>Numbers that reflect the trust our travelers have placed in us over the years.</p>
        </div>

        <div class="row g-4">
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
                    <div class="stat-number">12,000+</div>
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
                    <div class="stat-label">Years of Excellence</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- TEAM MEMBERS                                                                  -->
<!-- ============================================================================ -->
<section class="section-padding">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="subtitle">Meet the Team</span>
            <h2>Our Travel Experts</h2>
            <p>The passionate people behind every unforgettable journey at TravelEase.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                <div class="team-card">
                    <div class="team-img">
                        <img src="https://images.pexels.com/photos/2182970/pexels-photo-2182970.jpeg?auto=compress&cs=tinysrgb&w=500" alt="James Carter">
                    </div>
                    <div class="card-body">
                        <h5 class="mb-1">James Carter</h5>
                        <p class="text-primary-custom mb-2">Founder &amp; CEO</p>
                        <div class="social-icons">
                            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#"><i class="fa-brands fa-twitter"></i></a>
                            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="team-card">
                    <div class="team-img">
                        <img src="https://images.pexels.com/photos/3756679/pexels-photo-3756679.jpeg?auto=compress&cs=tinysrgb&w=500" alt="Sophia Lee">
                    </div>
                    <div class="card-body">
                        <h5 class="mb-1">Sophia Lee</h5>
                        <p class="text-primary-custom mb-2">Travel Director</p>
                        <div class="social-icons">
                            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#"><i class="fa-brands fa-twitter"></i></a>
                            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="team-card">
                    <div class="team-img">
                        <img src="https://images.pexels.com/photos/3779448/pexels-photo-3779448.jpeg?auto=compress&cs=tinysrgb&w=500" alt="David Brown">
                    </div>
                    <div class="card-body">
                        <h5 class="mb-1">David Brown</h5>
                        <p class="text-primary-custom mb-2">Tour Manager</p>
                        <div class="social-icons">
                            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#"><i class="fa-brands fa-twitter"></i></a>
                            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="team-card">
                    <div class="team-img">
                        <img src="https://images.pexels.com/photos/3760263/pexels-photo-3760263.jpeg?auto=compress&cs=tinysrgb&w=500" alt="Emma Wilson">
                    </div>
                    <div class="card-body">
                        <h5 class="mb-1">Emma Wilson</h5>
                        <p class="text-primary-custom mb-2">Customer Relations</p>
                        <div class="social-icons">
                            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#"><i class="fa-brands fa-twitter"></i></a>
                            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- WHY CHOOSE US                                                                 -->
<!-- ============================================================================ -->
<section class="section-padding" style="background: var(--gray-100);">
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
                    <h5>Safe &amp; Secure</h5>
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

<!-- CTA -->
<section class="section-padding pt-0">
    <div class="container">
        <div class="cta-section" data-aos="zoom-in">
            <h2>Let's Plan Your Next Trip Together</h2>
            <p>Join thousands of happy travelers who trust TravelEase with their adventures around the world.</p>
            <a href="contact.php" class="btn btn-light-pill btn-lg">
                <i class="fa-solid fa-envelope me-2"></i> Get in Touch
            </a>
        </div>
    </div>
</section>

<!-- Back to top button -->
<button class="back-to-top" aria-label="Back to top">
    <i class="fa-solid fa-arrow-up"></i>
</button>

<?php require_once 'includes/footer.php'; ?>
