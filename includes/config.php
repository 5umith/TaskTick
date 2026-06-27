<?php
// Database configuration - Using PostgreSQL
define('DB_HOST', getenv('PGHOST') ?: 'localhost');
define('DB_USER', getenv('PGUSER') ?: 'postgres');
define('DB_PASS', getenv('PGPASSWORD') ?: '');
define('DB_NAME', getenv('PGDATABASE') ?: 'assignment_tracker');
define('DB_PORT', getenv('PGPORT') ?: '5432');
define('DB_TYPE', 'pgsql'); // PostgreSQL database type

// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', ''); // Add your email address
define('SMTP_PASS', ''); // Add your email password or app password
define('SMTP_FROM', ''); // Add your email address
define('SMTP_FROM_NAME', 'Assignment Tracker');

// Application configuration
define('APP_NAME', 'Student Assignment Tracker');
define('APP_URL', 'http://localhost:5000');
define('APP_VERSION', '1.0.0');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time zone
date_default_timezone_set('UTC');

// Session timeout (in seconds) - 30 minutes
define('SESSION_TIMEOUT', 1800);
?>
