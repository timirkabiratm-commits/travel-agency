<?php
/**
 * ============================================================================
 * TravelEase - Contact Page
 * File: contact.php
 *
 * Displays a contact form, office information, Google Map embed,
 * and social media links. Handles both AJAX and non-AJAX form submissions,
 * plus the newsletter subscription AJAX endpoint.
 * ============================================================================
 */

require_once 'config/db.php';

$page_title    = 'Contact Us - TravelEase';
$current_page  = 'contact';

require_once 'includes/header.php';
require_once 'includes/navbar.php';

// ----------------------------------------------------------------------------
// Handle AJAX newsletter subscription (returns JSON)
// ----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newsletter'])) {
    header('Content-Type: application/json');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);

    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }

    // Save the newsletter subscription as a contact message
    $stmt = $pdo->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
    $stmt->execute([
        ':name'    => 'Newsletter Subscriber',
        ':email'   => $email,
        ':subject' => 'Newsletter Subscription',
        ':message' => 'User subscribed to the newsletter from the website.'
    ]);

    echo json_encode(['success' => true, 'message' => 'Thank you for subscribing!']);
    exit;
}

// ----------------------------------------------------------------------------
// Handle AJAX contact form submission (returns JSON)
// ----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_contact'])) {
    header('Content-Type: application/json');

    $name    = sanitize_input($_POST['name'] ?? '');
    $email   = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $subject = sanitize_input($_POST['subject'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');

    // Validation
    if (empty($name) || !$email || empty($subject) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields correctly.']);
        exit;
    }

    // Insert into contacts table using prepared statement
    $stmt = $pdo->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':subject' => $subject,
        ':message' => $message
    ]);

    echo json_encode(['success' => true, 'message' => 'Your message has been sent. We will get back to you soon!']);
    exit;
}

// ----------------------------------------------------------------------------
// Handle regular (non-AJAX) POST submission — fallback
// ----------------------------------------------------------------------------
$contactSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax_contact']) && !isset($_POST['newsletter'])) {
    $name    = sanitize_input($_POST['name'] ?? '');
    $email   = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $subject = sanitize_input($_POST['subject'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');

    if (!empty($name) && $email && !empty($subject) && !empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
        $stmt->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':subject' => $subject,
            ':message' => $message
        ]);
        $contactSuccess = true;
    }
}
?>

<!-- ============================================================================ -->
<!-- PAGE BANNER                                                                   -->
<!-- ============================================================================ -->
<section class="page-banner">
    <div class="container">
        <h1 data-aos="fade-up">Get in Touch</h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact</li>
            </ol>
        </nav>
    </div>
</section>

<!-- ============================================================================ -->
<!-- CONTACT INFO CARDS                                                           -->
<!-- ============================================================================ -->
<section class="section-padding pb-0">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="0">
                <div class="contact-info-card">
                    <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
                    <h5>Visit Us</h5>
                    <p>123 Travel Street<br>New York, NY 10001<br>United States</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="contact-info-card">
                    <div class="icon"><i class="fa-solid fa-phone"></i></div>
                    <h5>Call Us</h5>
                    <p>+1 (555) 123-4567<br>+1 (555) 765-4321<br>Mon - Sat: 9AM - 6PM</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="contact-info-card">
                    <div class="icon"><i class="fa-solid fa-envelope"></i></div>
                    <h5>Email Us</h5>
                    <p>info@travelease.com<br>support@travelease.com<br>bookings@travelease.com</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- CONTACT FORM + MAP                                                            -->
<!-- ============================================================================ -->
<section class="section-padding">
    <div class="container">
        <div class="row g-4">
            <!-- ---------------------------------------------------------------- -->
            <!-- CONTACT FORM                                                      -->
            <!-- ---------------------------------------------------------------- -->
            <div class="col-lg-7">
                <div class="booking-form-card" data-aos="fade-up">
                    <h2 class="mb-1"><i class="fa-solid fa-paper-plane text-primary me-2"></i>Send Us a Message</h2>
                    <p class="text-muted mb-4">Have a question or need help planning your trip? Fill out the form below.</p>

                    <!-- Success message (non-AJAX fallback) -->
                    <?php if ($contactSuccess): ?>
                        <div class="alert alert-success alert-custom alert-auto-dismiss mb-4">
                            <i class="fa-solid fa-check-circle me-2"></i>
                            Your message has been sent. We will get back to you soon!
                        </div>
                    <?php endif; ?>

                    <!-- AJAX message container -->
                    <div id="contactMsg"></div>

                    <form id="contactForm" method="POST" action="contact.php">
                        <!-- Hidden field for AJAX detection -->
                        <input type="hidden" name="ajax_contact" value="1">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="contactName">Your Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contactName" name="name"
                                       placeholder="John Smith" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="contactEmail">Your Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="contactEmail" name="email"
                                       placeholder="john@email.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="contactSubject">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contactSubject" name="subject"
                                       placeholder="How can we help you?" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="contactMessage">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="contactMessage" name="message" rows="5"
                                          placeholder="Write your message here..." required></textarea>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" id="contactSubmitBtn" class="btn btn-primary btn-lg rounded-pill px-5">
                                    <i class="fa-solid fa-paper-plane me-2"></i> Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ---------------------------------------------------------------- -->
            <!-- MAP + SOCIAL LINKS                                               -->
            <!-- ---------------------------------------------------------------- -->
            <div class="col-lg-5">
                <!-- Google Map Embed (placeholder using OpenStreetMap) -->
                <div class="map-container mb-4" data-aos="fade-left">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.1422935957435!2d-73.9873196845938!3d40.74844797932881!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDQ0JzU2LjQiTiA3M8KwNTknMTQuMyJX!5e0!3m2!1sen!2sus!4v1234567890"
                        allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

                <!-- Office Hours Card -->
                <div class="detail-card mb-4" data-aos="fade-left" data-aos-delay="100">
                    <h5 class="mb-3"><i class="fa-solid fa-clock text-primary me-2"></i>Office Hours</h5>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Monday - Friday</span>
                        <span class="fw-semibold">9:00 AM - 6:00 PM</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Saturday</span>
                        <span class="fw-semibold">10:00 AM - 4:00 PM</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span>Sunday</span>
                        <span class="fw-semibold text-danger">Closed</span>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="detail-card" data-aos="fade-left" data-aos-delay="200">
                    <h5 class="mb-3"><i class="fa-solid fa-share-nodes text-primary me-2"></i>Follow Us</h5>
                    <p class="text-muted mb-3">Stay connected for travel tips, deals, and inspiration.</p>
                    <div class="social-links" style="display: flex; gap: 10px;">
                        <a href="#" style="background: var(--primary-light); color: var(--primary);" aria-label="Facebook">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="#" style="background: var(--primary-light); color: var(--primary);" aria-label="Twitter">
                            <i class="fa-brands fa-twitter"></i>
                        </a>
                        <a href="#" style="background: var(--primary-light); color: var(--primary);" aria-label="Instagram">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="#" style="background: var(--primary-light); color: var(--primary);" aria-label="YouTube">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                        <a href="#" style="background: var(--primary-light); color: var(--primary);" aria-label="LinkedIn">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================================ -->
<!-- FAQ SECTION (Bonus)                                                           -->
<!-- ============================================================================ -->
<section class="section-padding pt-0">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="subtitle">FAQ</span>
            <h2>Frequently Asked Questions</h2>
            <p>Quick answers to questions you may have about booking with TravelEase.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion" data-aos="fade-up">
                    <div class="accordion-item mb-2 border-0 rounded-3 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq1">
                                How do I book a tour package?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Browse our packages, select the one you like, click "Book Now," and fill in your
                                details. Our team will confirm your booking via email within 24 hours.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item mb-2 border-0 rounded-3 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq2">
                                Can I cancel or modify my booking?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Yes! You can cancel or modify your booking up to 7 days before your travel date
                                free of charge. Contact our support team for assistance.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item mb-2 border-0 rounded-3 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq3">
                                Are flights included in the package price?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Package prices typically include accommodation, transfers, and tours. International
                                flights are usually excluded. Check the "What's Included" section on each package.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item mb-2 border-0 rounded-3 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq4">
                                Do you offer group discounts?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Yes! Groups of 6 or more travelers receive special discounts. Contact us with your
                                group size and preferred package for a custom quote.
                            </div>
                        </div>
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
