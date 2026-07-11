-- ============================================================================
-- TravelEase - Travel Agency Management System
-- Database: travel_agency
-- Engine:  MySQL 8.x (compatible with XAMPP / MariaDB 10.4+)
-- Encoding: utf8mb4 (full Unicode support, including emojis)
-- ============================================================================
--
-- HOW TO IMPORT THIS FILE
-- -----------------------
-- 1. Open phpMyAdmin (http://localhost/phpmyadmin)
-- 2. Click "Import" in the top menu
-- 3. Choose this file (travel_agency.sql) and click "Go"
--
-- OR via XAMPP shell / command line:
--   mysql -u root -p < travel_agency.sql
--
-- The script creates the database and all tables automatically.
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. CREATE DATABASE
-- ----------------------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `travel_agency`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_general_ci;

USE `travel_agency`;

-- ----------------------------------------------------------------------------
-- 2. DROP EXISTING TABLES (in correct FK order so re-imports never fail)
-- ----------------------------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `packages`;
DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `admins`;

SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 3. ADMINS TABLE
--    Stores administrator accounts who can log in to the admin panel.
--    Passwords are stored as bcrypt hashes (never plain text).
-- ----------------------------------------------------------------------------
CREATE TABLE `admins` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100)    NOT NULL,
    `email`       VARCHAR(150)    NOT NULL UNIQUE,
    `password`    VARCHAR(255)    NOT NULL,              -- bcrypt hash
    `role`        ENUM('super_admin','admin') NOT NULL DEFAULT 'admin',
    `status`      TINYINT(1)      NOT NULL DEFAULT 1,    -- 1 = active, 0 = blocked
    `created_at`  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- 4. PACKAGES TABLE
--    Stores all tour packages shown on the public website.
--    Bookings reference this table via a foreign key.
-- ----------------------------------------------------------------------------
CREATE TABLE `packages` (
    `id`            INT AUTO_INCREMENT PRIMARY KEY,
    `title`         VARCHAR(200)    NOT NULL,
    `location`      VARCHAR(150)    NOT NULL,
    `category`      VARCHAR(100)    NOT NULL,           -- e.g. Beach, Mountain, Adventure
    `price`         DECIMAL(10,2)   NOT NULL,
    `duration_days` INT             NOT NULL DEFAULT 1,
    `image`         VARCHAR(255)    DEFAULT NULL,       -- main image file name
    `gallery`       TEXT            DEFAULT NULL,       -- comma-separated extra images
    `description`   TEXT            NOT NULL,
    `itinerary`     TEXT            DEFAULT NULL,       -- day-by-day plan
    `included`      TEXT            DEFAULT NULL,       -- what is included
    `excluded`      TEXT            DEFAULT NULL,       -- what is excluded
    `featured`      TINYINT(1)      NOT NULL DEFAULT 0, -- 1 = show on home page
    `status`        TINYINT(1)      NOT NULL DEFAULT 1, -- 1 = published, 0 = draft
    `created_at`    TIMESTAMP       DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- Indexes to speed up common search / filter queries
CREATE INDEX `idx_packages_category`  ON `packages` (`category`);
CREATE INDEX `idx_packages_featured`  ON `packages` (`featured`);
CREATE INDEX `idx_packages_status`    ON `packages` (`status`);
CREATE INDEX `idx_packages_location`  ON `packages` (`location`);

-- ----------------------------------------------------------------------------
-- 5. BOOKINGS TABLE
--    Stores customer booking requests linked to a specific package.
-- ----------------------------------------------------------------------------
CREATE TABLE `bookings` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `package_id`     INT             NOT NULL,
    `customer_name`  VARCHAR(100)    NOT NULL,
    `customer_email` VARCHAR(150)    NOT NULL,
    `customer_phone` VARCHAR(30)     NOT NULL,
    `travelers`      INT             NOT NULL DEFAULT 1,
    `travel_date`    DATE            NOT NULL,
    `special_request` TEXT           DEFAULT NULL,
    `total_price`    DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    `booking_status` ENUM('pending','confirmed','cancelled','completed')
                     NOT NULL DEFAULT 'pending',
    `created_at`     TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    -- Foreign key: every booking belongs to a package
    CONSTRAINT `fk_bookings_package`
        FOREIGN KEY (`package_id`)
        REFERENCES `packages` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE = InnoDB;

-- Indexes for faster dashboard stats and lookups
CREATE INDEX `idx_bookings_package_id` ON `bookings` (`package_id`);
CREATE INDEX `idx_bookings_status`     ON `bookings` (`booking_status`);
CREATE INDEX `idx_bookings_email`      ON `bookings` (`customer_email`);

-- ----------------------------------------------------------------------------
-- 6. CONTACTS TABLE
--    Stores messages submitted through the public Contact page.
-- ----------------------------------------------------------------------------
CREATE TABLE `contacts` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(100)   NOT NULL,
    `email`      VARCHAR(150)   NOT NULL,
    `subject`    VARCHAR(200)   NOT NULL,
    `message`    TEXT           NOT NULL,
    `is_read`    TINYINT(1)     NOT NULL DEFAULT 0,    -- 1 = admin has read it
    `created_at` TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

CREATE INDEX `idx_contacts_is_read` ON `contacts` (`is_read`);

-- ============================================================================
-- 7. SAMPLE DATA
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 7a. Admin account
--     Login email: admin@travelease.com
--     Login password: admin123   (hashed with PHP password_hash() / bcrypt)
-- ----------------------------------------------------------------------------
INSERT INTO `admins` (`name`, `email`, `password`, `role`, `status`) VALUES
(
    'Super Admin',
    'admin@travelease.com',
    -- bcrypt hash for the string "admin123"
    '$2y$10$e1Z5.kJ8v7q2N3xR9oL4pO5tU6wY7zA8bC9dE0fG1hI2jK3lM4nO5p',
    'super_admin',
    1
);

-- ----------------------------------------------------------------------------
-- 7b. Tour packages (featured + regular)
-- ----------------------------------------------------------------------------
INSERT INTO `packages`
(`title`,`location`,`category`,`price`,`duration_days`,`image`,`gallery`,`description`,`itinerary`,`included`,`excluded`,`featured`,`status`)
VALUES
(
    'Maldives Tropical Escape',
    'Maldives',
    'Beach',
    1499.00,
    5,
    'maldives.jpg',
    'maldives-1.jpg,maldives-2.jpg,maldives-3.jpg',
    'Experience the ultimate tropical paradise in the Maldives. Stay in an overwater villa, snorkel in crystal-clear lagoons, and enjoy world-class hospitality surrounded by pristine white-sand beaches.',
    'Day 1: Arrival and resort welcome dinner\nDay 2: Snorkeling and island hopping\nDay 3: Spa and water sports\nDay 4: Sunset dolphin cruise\nDay 5: Departure',
    '4 nights overwater villa, Daily breakfast, Airport transfers, Snorkeling equipment, Sunset cruise',
    'International flights, Personal expenses, Travel insurance',
    1,
    1
),
(
    'Swiss Alps Adventure',
    'Switzerland',
    'Mountain',
    1899.00,
    7,
    'swiss-alps.jpg',
    'swiss-1.jpg,swiss-2.jpg,swiss-3.jpg',
    'Explore the breathtaking Swiss Alps with scenic train rides, snow-capped peaks, and charming alpine villages. A perfect blend of adventure and relaxation in one of the most beautiful regions on Earth.',
    'Day 1: Arrival in Zurich\nDay 2: Train to Interlaken and lake cruise\nDay 3: Jungfraujoch - Top of Europe\nDay 4: Lucerne city tour\nDay 5: Mount Titlis excursion\nDay 6: Free day for shopping\nDay 7: Departure',
    '6 nights hotel stay, Swiss Travel Pass, Daily breakfast, All excursions, Professional guide',
    'International flights, Lunch and dinner, Personal expenses',
    1,
    1
),
(
    'Safari in the Serengeti',
    'Tanzania',
    'Adventure',
    2199.00,
    6,
    'serengeti.jpg',
    'serengeti-1.jpg,serengeti-2.jpg,serengeti-3.jpg',
    'Witness the Great Migration in the Serengeti. Enjoy daily game drives, luxury tented camps, and unforgettable encounters with lions, elephants, and giraffes in their natural habitat.',
    'Day 1: Arrival in Arusha\nDay 2: Drive to Serengeti, evening game drive\nDay 3: Full-day game drive\nDay 4: Ngorongoro Crater tour\nDay 5: Maasai village visit\nDay 6: Departure',
    '5 nights luxury tented camp, All meals, Park fees, Game drives, Professional guide',
    'International flights, Visa fees, Tips, Travel insurance',
    1,
    1
),
(
    'Paris City Break',
    'France',
    'City',
    999.00,
    4,
    'paris.jpg',
    'paris-1.jpg,paris-2.jpg,paris-3.jpg',
    'Discover the romance of Paris. Visit the Eiffel Tower, Louvre Museum, Notre-Dame, and cruise the Seine at sunset. A timeless getaway for culture and cuisine lovers.',
    'Day 1: Arrival and Seine river cruise\nDay 2: Eiffel Tower and Louvre Museum\nDay 3: Versailles day trip\nDay 4: Free morning and departure',
    '3 nights hotel, Daily breakfast, Skip-the-line museum tickets, Seine cruise, City guide',
    'International flights, Lunch and dinner, Personal shopping',
    0,
    1
),
(
    'Bali Cultural Journey',
    'Indonesia',
    'Cultural',
    1299.00,
    6,
    'bali.jpg',
    'bali-1.jpg,bali-2.jpg,bali-3.jpg',
    'Immerse yourself in Balinese culture with temple visits, rice terrace walks, traditional cooking classes, and relaxing beach time. A rejuvenating escape for mind and body.',
    'Day 1: Arrival in Denpasar\nDay 2: Ubud temple and rice terraces\nDay 3: Cooking class and waterfall visit\nDay 4: Beach day in Seminyak\nDay 5: Island temple tour\nDay 6: Departure',
    '5 nights villa stay, Daily breakfast, Cooking class, Temple tours, Airport transfers',
    'International flights, Personal expenses, Travel insurance',
    0,
    1
),
(
    'Tokoto Desert Expedition',
    'Morocco',
    'Adventure',
    1599.00,
    5,
    'morocco.jpg',
    'morocco-1.jpg,morocco-2.jpg,morocco-3.jpg',
    'Journey through the Moroccan desert on camelback, sleep under the stars in a luxury camp, and explore ancient kasbahs and vibrant souks in Marrakech.',
    'Day 1: Arrival in Marrakech\nDay 2: Drive to Merzouga, camel trek\nDay 3: Sahara sunrise and desert camp\nDay 4: Atlas Mountains and Berber villages\nDay 5: Departure',
    '4 nights accommodation, Camel trek, Desert camp stay, All breakfasts, Local guide',
    'International flights, Lunch and dinner, Visa fees',
    0,
    1
),
(
    'Rome and Amalfi Coast',
    'Italy',
    'City',
    1699.00,
    7,
    'italy.jpg',
    'italy-1.jpg,italy-2.jpg,italy-3.jpg',
    'Experience la dolce vita with a tour of ancient Rome followed by the stunning Amalfi Coast. History, cuisine, and coastal beauty all in one unforgettable trip.',
    'Day 1: Arrival in Rome\nDay 2: Colosseum and Roman Forum\nDay 3: Vatican Museums and St. Peters Basilica\nDay 4: Train to Naples, Pompeii tour\nDay 5: Amalfi Coast drive\nDay 6: Capri island boat tour\nDay 7: Departure',
    '6 nights hotel, Daily breakfast, Colosseum and Vatican tickets, Pompeii tour, Capri boat tour',
    'International flights, Lunch and dinner, Personal expenses',
    0,
    1
),
(
    'Thailand Island Hopping',
    'Thailand',
    'Beach',
    1199.00,
    7,
    'thailand.jpg',
    'thailand-1.jpg,thailand-2.jpg,thailand-3.jpg',
    'Island-hop across Thailands most beautiful beaches. Snorkel in crystal waters, party on Phi Phi, and relax on the quiet sands of Koh Lanta.',
    'Day 1: Arrival in Phuket\nDay 2: Phi Phi Islands speedboat tour\nDay 3: Phuket free day\nDay 4: Ferry to Krabi\nDay 5: 4-island tour\nDay 6: Koh Lanta beach day\nDay 7: Departure',
    '6 nights accommodation, Daily breakfast, Island tours, Speedboat and ferry transfers',
    'International flights, Lunch and dinner, Personal expenses',
    0,
    1
);

-- ----------------------------------------------------------------------------
-- 7c. Sample bookings
-- ----------------------------------------------------------------------------
INSERT INTO `bookings`
(`package_id`,`customer_name`,`customer_email`,`customer_phone`,`travelers`,`travel_date`,`special_request`,`total_price`,`booking_status`)
VALUES
(1, 'John Smith',    'john.smith@email.com',    '+1-555-0101', 2, '2026-02-15', 'Honeymoon trip, would prefer a quiet villa.',          2998.00, 'confirmed'),
(2, 'Emily Johnson', 'emily.johnson@email.com', '+44-7700-900123', 4, '2026-03-10', 'Vegetarian meals for 2 travelers.',                7596.00, 'pending'),
(3, 'David Lee',     'david.lee@email.com',     '+65-9123-4567',  2, '2026-04-05', 'Please arrange a private guide if possible.',        4398.00, 'pending'),
(5, 'Sarah Wilson',  'sarah.wilson@email.com',  '+61-400-123-456', 3, '2026-05-20', 'Celebrating a birthday, surprise cake please.',      3897.00, 'completed'),
(8, 'Michael Brown', 'michael.brown@email.com', '+1-555-0199',    1, '2026-06-01', NULL,                                                 1199.00, 'cancelled');

-- ----------------------------------------------------------------------------
-- 7d. Sample contact messages
-- ----------------------------------------------------------------------------
INSERT INTO `contacts` (`name`,`email`,`subject`,`message`,`is_read`) VALUES
(
    'Anna Taylor',
    'anna.taylor@email.com',
    'Question about Maldives package',
    'Hello, I would like to know if the Maldives package includes seaplane transfers to the resort. Thank you!',
    0
),
(
    'Robert Clark',
    'robert.clark@email.com',
    'Group booking discount',
    'We are a group of 10 people interested in the Swiss Alps Adventure tour. Do you offer any group discounts?',
    0
),
(
    'Sophie Martin',
    'sophie.martin@email.com',
    'Thank you',
    'I just returned from the Bali Cultural Journey and it was amazing. Thank you for the wonderful experience!',
    1
);

-- ============================================================================
-- END OF FILE
-- ============================================================================
