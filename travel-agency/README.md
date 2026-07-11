# TravelEase - Travel Agency Management System

A complete, professional Travel Agency Management System built with Core PHP 8, MySQL, and Bootstrap 5.3. Features a modern public website and a full-featured admin panel.

## Tech Stack

| Layer       | Technologies                                      |
|-------------|---------------------------------------------------|
| Frontend    | HTML5, CSS3, Bootstrap 5.3, JavaScript (ES6), jQuery, Font Awesome 6, AOS Animations, Swiper.js |
| Backend     | Core PHP 8, PDO (Prepared Statements), AJAX       |
| Database    | MySQL (via XAMPP)                                 |
| Server      | XAMPP (Apache + MySQL + PHP)                      |

## Features

### Public Website
- **Home Page** - Hero banner with search form, popular destinations, featured packages, why choose us, tour categories, customer testimonials (Swiper slider), gallery, newsletter, stats counters
- **About Page** - Company introduction, mission, vision, services, team members, statistics, why choose us
- **Packages Page** - Dynamic package listing from database with live AJAX search, category filter, price range filter, sorting, and pagination
- **Package Details** - Full package info with image, description, price, duration, day-by-day itinerary, included/excluded items, booking sidebar, related packages
- **Booking Page** - Customer booking form with live total price calculation, AJAX submission, booking summary sidebar
- **Contact Page** - Contact form (AJAX), office info cards, Google Map embed, social links, FAQ accordion, newsletter subscription

### Admin Panel
- **Login** - Secure login with bcrypt password hashing and session authentication
- **Dashboard** - Stats cards (total packages, bookings, customers, messages), revenue banner, recent bookings, recent messages
- **Manage Packages** - Full CRUD (add, edit, delete) with image upload, gallery upload, featured/published toggles
- **Manage Bookings** - View all bookings, update status (pending/confirmed/cancelled/completed), view details in modal, delete
- **Manage Contacts** - View all messages, mark as read, delete, reply via email

## Folder Structure

```
travel-agency/
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── packages.php
│   ├── add-package.php
│   ├── edit-package.php
│   ├── delete-package.php
│   ├── bookings.php
│   ├── contacts.php
│   └── logout.php
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   ├── images/
│   └── uploads/
├── config/
│   └── db.php
├── includes/
│   ├── header.php
│   ├── navbar.php
│   └── footer.php
├── index.php
├── about.php
├── packages.php
├── package-details.php
├── booking.php
├── contact.php
├── travel_agency.sql
└── README.md
```

## Installation

### Step 1: Install XAMPP
Download and install [XAMPP](https://www.apachefriends.org/) on your computer.

### Step 2: Copy Project Files
Copy the `travel-agency` folder into your XAMPP `htdocs` directory:
```
C:\xampp\htdocs\travel-agency\
```

### Step 3: Import the Database
1. Start **Apache** and **MySQL** from the XAMPP Control Panel
2. Open [phpMyAdmin](http://localhost/phpmyadmin)
3. Click **Import** in the top menu
4. Choose the file `travel-agency/travel_agency.sql`
5. Click **Go** to import

### Step 4: Configure Database Connection (if needed)
Open `config/db.php` and update the credentials if your XAMPP setup uses a different password:
```php
$db_host = 'localhost';
$db_name = 'travel_agency';
$db_user = 'root';
$db_pass = '';
```

### Step 5: Access the Website
- **Public Website:** http://localhost/travel-agency/
- **Admin Panel:** http://localhost/travel-agency/admin/login.php

## Admin Login Credentials

| Field  | Value                    |
|--------|--------------------------|
| Email  | admin@travelease.com     |
| Password | admin123               |

## Security Features

- **Prepared Statements** - All database queries use PDO prepared statements to prevent SQL injection
- **Password Hashing** - Admin passwords stored as bcrypt hashes using `password_hash()`
- **XSS Protection** - All user input sanitized with `htmlspecialchars()` before output
- **Session Authentication** - Admin pages check for valid session before granting access
- **Session Fixation Protection** - Session ID regenerated on successful login
- **Input Validation** - Server-side validation on all forms (email, required fields, numeric values)

## Database Tables

| Table      | Description                                      |
|------------|--------------------------------------------------|
| `admins`   | Admin accounts with bcrypt-hashed passwords      |
| `packages` | Tour packages with images, itinerary, pricing    |
| `bookings` | Customer booking requests linked to packages (FK)|
| `contacts` | Contact form messages and newsletter signups     |

## Design

- **Theme:** Blue and white professional travel agency theme
- **UI Components:** Glassmorphism cards, rounded corners, smooth hover effects
- **Animations:** AOS (fade-up, zoom-in), hover zoom on images, button hover transitions, smooth scrolling
- **Typography:** Poppins (headings) + Inter (body) from Google Fonts
- **Responsive:** Fully responsive across desktop, laptop, tablet, and mobile devices

## License

This project is created for educational purposes as a college project.
