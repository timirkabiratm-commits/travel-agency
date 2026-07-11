<?php
/**
 * ============================================================================
 * TravelEase - Database Connection File
 * File: config/db.php
 *
 * This file creates a single, reusable PDO connection to the MySQL database.
 * PDO (PHP Data Objects) is used because it supports prepared statements,
 * which protect against SQL injection attacks.
 *
 * Every page that needs database access should include this file:
 *     require_once 'config/db.php';
 *
 * After including, the connection is available as the variable $pdo.
 * ============================================================================
 */

// ----------------------------------------------------------------------------
// 1. DATABASE CONFIGURATION
//    Change these values to match your XAMPP / hosting environment.
//    Default XAMPP settings use root with no password.
// ----------------------------------------------------------------------------
$db_host    = 'localhost';   // Database server host (usually "localhost" on XAMPP)
$db_name    = 'travel_agency'; // Database name (must match the SQL file)
$db_user    = 'root';        // Database username (default XAMPP = root)
$db_pass    = '';            // Database password (default XAMPP = empty)
$db_charset = 'utf8mb4';     // Character set (utf8mb4 supports all languages + emojis)

// ----------------------------------------------------------------------------
// 2. DATA SOURCE NAME (DSN)
//    The DSN tells PDO which database driver and server to use.
// ----------------------------------------------------------------------------
$dsn = "mysql:host={$db_host};dbname={$db_name};charset={$db_charset}";

// ----------------------------------------------------------------------------
// 3. PDO OPTIONS
//    These options configure how PDO behaves:
//    - ERRMODE_EXCEPTION  : Throw exceptions on errors (easier to catch)
//    - DEFAULT_FETCH_MODE : Return rows as associative arrays
//    - EMULATE_PREPARES   : false = use real prepared statements (more secure)
// ----------------------------------------------------------------------------
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// ----------------------------------------------------------------------------
// 4. CREATE THE PDO CONNECTION
//    If the connection fails, we show a friendly error message and stop the
//    script so no broken data is displayed to the user.
// ----------------------------------------------------------------------------
try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // In a real production app you would log this to a file instead of
    // showing raw error details to visitors. For a college project this is fine.
    die('Database connection failed: ' . $e->getMessage());
}

/**
 * ----------------------------------------------------------------------------
 * Helper function: sanitize_input()
 *
 * Cleans user input to help prevent XSS (Cross-Site Scripting) attacks.
 * Use this on any data that comes from the user before displaying it on a page.
 *
 * @param  string $data  The raw input string
 * @return string        The cleaned string
 * ----------------------------------------------------------------------------
 */
function sanitize_input($data) {
    $data = trim($data);            // Remove spaces from start and end
    $data = stripslashes($data);    // Remove backslashes (\)
    $data = htmlspecialchars($data); // Convert special characters to HTML entities
    return $data;
}
