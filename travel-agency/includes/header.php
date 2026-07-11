<?php
/**
 * ============================================================================
 * TravelEase - Common Header File
 * File: includes/header.php
 *
 * This file contains the opening <html>, <head> section with all meta tags,
 * CSS links, and external library includes (Bootstrap, Font Awesome, AOS,
 * Swiper.js). It is included at the TOP of every public page.
 *
 * Usage (at the top of any public page):
 *     <?php $page_title = 'Home'; require_once 'includes/header.php'; ?>
 *
 * The variable $page_title (optional) lets each page set its own title.
 * ============================================================================
 */

// Default page title if none was set by the calling page
$page_title = isset($page_title) ? $page_title : 'TravelEase - Travel Agency';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ---------------------------------------------------------------- -->
    <!-- META TAGS                                                        -->
    <!-- ---------------------------------------------------------------- -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TravelEase - Book your dream vacation with premium tour packages worldwide. Beaches, mountains, cities, and adventures await.">
    <meta name="author" content="TravelEase">
    <title><?= htmlspecialchars($page_title) ?></title>

    <!-- ---------------------------------------------------------------- -->
    <!-- FAVICON                                                          -->
    <!-- ---------------------------------------------------------------- -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>&#9992;</text></svg>">

    <!-- ---------------------------------------------------------------- -->
    <!-- BOOTSTRAP 5.3 CSS                                                -->
    <!-- ---------------------------------------------------------------- -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ---------------------------------------------------------------- -->
    <!-- FONT AWESOME 6 (Icons)                                           -->
    <!-- ---------------------------------------------------------------- -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- ---------------------------------------------------------------- -->
    <!-- SWIPER.JS CSS (for testimonial / gallery sliders)               -->
    <!-- ---------------------------------------------------------------- -->
    <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">

    <!-- ---------------------------------------------------------------- -->
    <!-- AOS ANIMATION CSS                                                -->
    <!-- ---------------------------------------------------------------- -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- ---------------------------------------------------------------- -->
    <!-- GOOGLE FONTS (Poppins for headings, Inter for body)             -->
    <!-- ---------------------------------------------------------------- -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- ---------------------------------------------------------------- -->
    <!-- CUSTOM STYLESHEET                                                -->
    <!-- ---------------------------------------------------------------- -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
