<?php
/**
 * ============================================================================
 * TravelEase - Common Footer File
 * File: includes/footer.php
 *
 * This file contains the website footer (with newsletter, quick links,
 * contact info, social media) and all JavaScript includes at the bottom
 * of the page. It is included at the BOTTOM of every public page.
 *
 * Usage (at the bottom of any public page):
 *     <?php require_once 'includes/footer.php'; ?>
 * ============================================================================
 */
?>
<!-- ============================================================================ -->
<!-- FOOTTER SECTION                                                               -->
<!-- ============================================================================ -->
<footer class="footer text-light pt-5 pb-4">
    <div class="container">
        <div class="row g-4">
            <!-- ---------------------------------------------------------------- -->
            <!-- ABOUT COLUMN                                                      -->
            <!-- ---------------------------------------------------------------- -->
            <div class="col-lg-4 col-md-6">
                <h4 class="footer-brand mb-3">
                    <i class="fa-solid fa-plane-departure me-2"></i>TravelEase
                </h4>
                <p class="text-light-opacity">
                    Your trusted travel partner for unforgettable journeys. We craft
                    premium travel experiences across the globe with care and passion.
                </p>
                <!-- Social Media Links -->
                <div class="social-links mt-3">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <!-- ---------------------------------------------------------------- -->
            <!-- QUICK LINKS COLUMN                                                -->
            <!-- ---------------------------------------------------------------- -->
            <div class="col-lg-2 col-md-6">
                <h5 class="footer-title">Quick Links</h5>
                <ul class="footer-links">
                    <li><a href="index.php"><i class="fa-solid fa-chevron-right me-1"></i>Home</a></li>
                    <li><a href="about.php"><i class="fa-solid fa-chevron-right me-1"></i>About Us</a></li>
                    <li><a href="packages.php"><i class="fa-solid fa-chevron-right me-1"></i>Packages</a></li>
                    <li><a href="contact.php"><i class="fa-solid fa-chevron-right me-1"></i>Contact</a></li>
                    <li><a href="admin/login.php"><i class="fa-solid fa-chevron-right me-1"></i>Admin Login</a></li>
                </ul>
            </div>

            <!-- ---------------------------------------------------------------- -->
            <!-- CONTACT INFO COLUMN                                               -->
            <!-- ---------------------------------------------------------------- -->
            <div class="col-lg-3 col-md-6">
                <h5 class="footer-title">Contact Info</h5>
                <ul class="footer-contact">
                    <li><i class="fa-solid fa-location-dot me-2"></i>123 Travel Street, New York, NY 10001</li>
                    <li><i class="fa-solid fa-phone me-2"></i>+1 (555) 123-4567</li>
                    <li><i class="fa-solid fa-envelope me-2"></i>info@travelease.com</li>
                    <li><i class="fa-solid fa-clock me-2"></i>Mon - Sat: 9:00 AM - 6:00 PM</li>
                </ul>
            </div>

            <!-- ---------------------------------------------------------------- -->
            <!-- NEWSLETTER COLUMN                                                 -->
            <!-- ---------------------------------------------------------------- -->
            <div class="col-lg-3 col-md-6">
                <h5 class="footer-title">Newsletter</h5>
                <p class="text-light-opacity">Subscribe to get the latest travel deals and offers.</p>
                <form class="newsletter-form mt-3" id="newsletterForm">
                    <div class="input-group">
                        <input type="email" class="form-control" name="email"
                               placeholder="Your email address" required>
                        <button class="btn btn-primary" type="submit" id="newsletterBtn">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="mt-2" id="newsletterMsg"></div>
                </form>
            </div>
        </div>

        <!-- Divider -->
        <hr class="footer-divider my-4">

        <!-- Copyright -->
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-light-opacity">
                    &copy; <?= date('Y') ?> TravelEase. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0 text-light-opacity">
                    Designed with <i class="fa-solid fa-heart text-danger"></i> for travelers worldwide
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- ============================================================================ -->
<!-- JAVASCRIPT INCLUDES                                                          -->
<!-- ============================================================================ -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap 5.3 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Swiper.js -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- AOS Animation JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Custom JS -->
<script src="assets/js/main.js"></script>
