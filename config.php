<?php
// Database configuration - Using MySQL (WAMP)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'assignment_tracker');
define('DB_TYPE', 'mysql'); // MySQL database type

// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'mail4sumithsuvarna@gmail.com'); // Add your email address
define('SMTP_PASS', 'xqel riov fxjy wuib'); // Add your email password or app password
define('SMTP_FROM', 'mail4sumithsuvarna@gmail.com'); // Add your email address
define('SMTP_FROM_NAME', 'Assignment Tracker');

// Application configuration
define('APP_NAME', 'Student Assignment Tracker');
define('APP_URL', 'http://localhost');
define('APP_VERSION', '1.0.0');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time zone
date_default_timezone_set('UTC');

// Session timeout (in seconds) - 30 minutes
define('SESSION_TIMEOUT', 1800);
?>