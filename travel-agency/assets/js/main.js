/**
 * ============================================================================
 * TravelEase - Main JavaScript File
 * File: assets/js/main.js
 *
 * Handles all client-side interactions for the public website:
 *   - Navbar scroll effect
 *   - AOS animation initialization
 *   - Swiper sliders (testimonials)
 *   - Back-to-top button
 *   - Newsletter AJAX form
 *   - Package search & filter (AJAX)
 *   - Booking form calculations
 *   - Contact form AJAX submission
 * ============================================================================
 */

// ----------------------------------------------------------------------------
// 1. DOCUMENT READY - Run all code after the DOM is fully loaded
// ----------------------------------------------------------------------------
$(document).ready(function () {

    // ------------------------------------------------------------------------
    // 1a. Initialize AOS (Animate On Scroll) library
    // ------------------------------------------------------------------------
    AOS.init({
        duration: 800,        // Animation duration in ms
        once: true,           // Animate only once per element
        offset: 80,           // Trigger offset from bottom
        easing: 'ease-out-cubic'
    });

    // ------------------------------------------------------------------------
    // 1b. Navbar scroll effect — add shadow and shrink on scroll
    // ------------------------------------------------------------------------
    $(window).on('scroll', function () {
        if ($(window).scrollTop() > 50) {
            $('#mainNavbar').addClass('scrolled');
        } else {
            $('#mainNavbar').removeClass('scrolled');
        }
    });

    // ------------------------------------------------------------------------
    // 1c. Back-to-top button — show/hide and scroll to top
    // ------------------------------------------------------------------------
    $(window).on('scroll', function () {
        if ($(window).scrollTop() > 300) {
            $('.back-to-top').addClass('visible');
        } else {
            $('.back-to-top').removeClass('visible');
        }
    });

    $('.back-to-top').on('click', function (e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 600);
    });

    // ------------------------------------------------------------------------
    // 1d. Testimonial Swiper — initialize if the slider exists on the page
    // ------------------------------------------------------------------------
    if ($('.testimonialSwiper').length) {
        new Swiper('.testimonialSwiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true
            },
            breakpoints: {
                768:  { slidesPerView: 2 },
                992:  { slidesPerView: 3 }
            }
        });
    }

    // ------------------------------------------------------------------------
    // 1e. Newsletter form — AJAX submission
    // ------------------------------------------------------------------------
    $('#newsletterForm').on('submit', function (e) {
        e.preventDefault();
        var email = $(this).find('input[name="email"]').val();
        var $btn  = $('#newsletterBtn');
        var $msg  = $('#newsletterMsg');

        $btn.html('<span class="spinner-border spinner-border-sm"></span>');
        $msg.html('');

        $.ajax({
            url: 'contact.php',
            type: 'POST',
            data: {
                newsletter: 1,
                email: email
            },
            dataType: 'json',
            success: function (response) {
                $btn.html('<i class="fa-solid fa-paper-plane"></i>');
                if (response.success) {
                    $msg.html('<small class="text-success"><i class="fa-solid fa-check-circle"></i> ' + response.message + '</small>');
                    $('#newsletterForm')[0].reset();
                } else {
                    $msg.html('<small class="text-warning"><i class="fa-solid fa-exclamation-circle"></i> ' + response.message + '</small>');
                }
            },
            error: function () {
                $btn.html('<i class="fa-solid fa-paper-plane"></i>');
                $msg.html('<small class="text-danger"><i class="fa-solid fa-times-circle"></i> Something went wrong. Try again.</small>');
            }
        });
    });

    // ------------------------------------------------------------------------
    // 1f. Package search & filter on packages.php (AJAX)
    // ------------------------------------------------------------------------
    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        loadPackages(1);
    });

    $('#searchInput').on('keyup', function () {
        clearTimeout(window.searchTimer);
        window.searchTimer = setTimeout(function () {
            loadPackages(1);
        }, 400);
    });

    $('#categoryFilter, #sortFilter, #maxPrice').on('change', function () {
        loadPackages(1);
    });

    // Clear filters button
    $('#clearFilters').on('click', function () {
        $('#searchInput').val('');
        $('#categoryFilter').val('');
        $('#sortFilter').val('newest');
        $('#maxPrice').val(5000);
        $('#priceDisplay').text('$5,000');
        loadPackages(1);
    });

    // Price range slider display
    $('#maxPrice').on('input', function () {
        $('#priceDisplay').text('$' + parseInt($(this).val()).toLocaleString());
    });

    /**
     * Load packages via AJAX and inject the HTML into #packagesContainer.
     * @param {number} page - The page number for pagination
     */
    function loadPackages(page) {
        var searchData = {
            search: $('#searchInput').val(),
            category: $('#categoryFilter').val(),
            sort: $('#sortFilter').val(),
            max_price: $('#maxPrice').val(),
            page: page
        };

        $.ajax({
            url: 'packages.php',
            type: 'GET',
            data: searchData,
            beforeSend: function () {
                $('#packagesContainer').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3 text-muted">Loading packages...</p></div>');
            },
            success: function (response) {
                $('#packagesContainer').html(response);
            },
            error: function () {
                $('#packagesContainer').html('<div class="no-results"><i class="fa-solid fa-triangle-exclamation"></i><h5>Failed to load packages</h5><p>Please try again later.</p></div>');
            }
        });
    }

    // Load packages on page ready if the container exists
    if ($('#packagesContainer').length) {
        loadPackages(1);
    }

    // Pagination click handler (delegated, since content is loaded dynamically)
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        var page = $(this).data('page');
        loadPackages(page);
        $('html, body').animate({ scrollTop: $('#packagesContainer').offset().top - 120 }, 500);
    });

    // ------------------------------------------------------------------------
    // 1g. Booking form — calculate total price dynamically
    // ------------------------------------------------------------------------
    $('#travelers').on('input change', function () {
        updateBookingTotal();
    });

    function updateBookingTotal() {
        var travelers = parseInt($('#travelers').val()) || 1;
        var price    = parseFloat($('#packagePrice').val()) || 0;
        var total    = travelers * price;

        $('#totalPrice').text('$' + total.toFixed(2));
        $('#totalPriceInput').val(total.toFixed(2));
    }

    // Run once on page load if booking form exists
    if ($('#travelers').length) {
        updateBookingTotal();
    }

    // ------------------------------------------------------------------------
    // 1h. Contact form — AJAX submission
    // ------------------------------------------------------------------------
    $('#contactForm').on('submit', function (e) {
        e.preventDefault();
        var $form  = $(this);
        var $btn   = $('#contactSubmitBtn');
        var $msg   = $('#contactMsg');

        $btn.html('<span class="spinner-border spinner-border-sm me-2"></span> Sending...').prop('disabled', true);
        $msg.html('');

        $.ajax({
            url: 'contact.php',
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function (response) {
                $btn.html('<i class="fa-solid fa-paper-plane me-2"></i> Send Message').prop('disabled', false);
                if (response.success) {
                    $msg.html('<div class="alert alert-success alert-custom"><i class="fa-solid fa-check-circle me-2"></i>' + response.message + '</div>');
                    $form[0].reset();
                } else {
                    $msg.html('<div class="alert alert-danger alert-custom"><i class="fa-solid fa-exclamation-circle me-2"></i>' + response.message + '</div>');
                }
            },
            error: function () {
                $btn.html('<i class="fa-solid fa-paper-plane me-2"></i> Send Message').prop('disabled', false);
                $msg.html('<div class="alert alert-danger alert-custom"><i class="fa-solid fa-times-circle me-2"></i> Something went wrong. Please try again.</div>');
            }
        });
    });

    // ------------------------------------------------------------------------
    // 1i. Booking form — AJAX submission
    // ------------------------------------------------------------------------
    $('#bookingForm').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn  = $('#bookingSubmitBtn');
        var $msg  = $('#bookingMsg');

        $btn.html('<span class="spinner-border spinner-border-sm me-2"></span> Processing...').prop('disabled', true);
        $msg.html('');

        $.ajax({
            url: 'booking.php',
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $msg.html('<div class="alert alert-success alert-custom"><i class="fa-solid fa-check-circle me-2"></i>' + response.message + '</div>');
                    $form[0].reset();
                    // Redirect to confirmation after 2 seconds
                    setTimeout(function () {
                        window.location.href = response.redirect || 'packages.php';
                    }, 2000);
                } else {
                    $btn.html('<i class="fa-solid fa-check me-2"></i> Confirm Booking').prop('disabled', false);
                    $msg.html('<div class="alert alert-danger alert-custom"><i class="fa-solid fa-exclamation-circle me-2"></i>' + response.message + '</div>');
                }
            },
            error: function () {
                $btn.html('<i class="fa-solid fa-check me-2"></i> Confirm Booking').prop('disabled', false);
                $msg.html('<div class="alert alert-danger alert-custom"><i class="fa-solid fa-times-circle me-2"></i> Something went wrong. Please try again.</div>');
            }
        });
    });

    // ------------------------------------------------------------------------
    // 1j. Admin sidebar toggle (mobile)
    // ------------------------------------------------------------------------
    $('#adminToggle').on('click', function () {
        $('.admin-sidebar').toggleClass('open');
    });

    // Close sidebar when clicking outside on mobile
    $(document).on('click', function (e) {
        if ($(window).width() < 992 &&
            !$(e.target).closest('.admin-sidebar').length &&
            !$(e.target).closest('#adminToggle').length) {
            $('.admin-sidebar').removeClass('open');
        }
    });

    // ------------------------------------------------------------------------
    // 1k. Auto-dismiss alerts after 5 seconds
    // ------------------------------------------------------------------------
    setTimeout(function () {
        $('.alert-auto-dismiss').fadeOut(500);
    }, 5000);

});
