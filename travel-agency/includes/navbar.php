<?php
/**
 * ============================================================================
 * TravelEase - Public Navigation Bar
 * File: includes/navbar.php
 *
 * This file contains the responsive Bootstrap navbar used on all public
 * pages. It highlights the active page automatically.
 *
 * Usage (after including header.php):
 *     <?php $current_page = 'home'; require_once 'includes/navbar.php'; ?>
 *
 * Set $current_page in each page to highlight the correct nav link:
 *     'home', 'about', 'packages', 'contact'
 * ============================================================================
 */

// Default current page to avoid undefined variable errors
$current_page = isset($current_page) ? $current_page : '';
?>
<!-- ============================================================================ -->
<!-- NAVIGATION BAR                                                               -->
<!-- ============================================================================ -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm" id="mainNavbar">
    <div class="container">
        <!-- Brand Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <i class="fa-solid fa-plane-departure text-primary fs-3"></i>
            <span class="fw-bold fs-4">Travel<span class="text-primary">Ease</span></span>
        </a>

        <!-- Mobile Toggler Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible Menu -->
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'home' ? 'active' : '' ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'about' ? 'active' : '' ?>" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'packages' ? 'active' : '' ?>" href="packages.php">Packages</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'contact' ? 'active' : '' ?>" href="contact.php">Contact</a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-primary px-4 rounded-pill" href="packages.php">
                        <i class="fa-solid fa-compass me-1"></i> Book Now
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
