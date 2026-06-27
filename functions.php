<?php
/**
 * Connect to the database
 * 
 * @return mysqli The database connection
 */
function connectDB(){
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }    
    return $conn;
}

/**
 * Sanitize user input
 * 
 * @param string $data The data to sanitize
 * @return string The sanitized data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Generate a random string
 * 
 * @param int $length The length of the string
 * @return string The random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Format date
 * 
 * @param string $date The date to format
 * @param string $format The format to use
 * @return string The formatted date
 */
function formatDate($date, $format = 'F j, Y') {
    $dateObj = new DateTime($date);
    return $dateObj->format($format);
}

/**
 * Check if a date is in the past
 * 
 * @param string $date The date to check
 * @return bool True if the date is in the past, false otherwise
 */
function isDatePast($date) {
    $dateObj = new DateTime($date);
    $today = new DateTime();
    return $dateObj < $today;
}

/**
 * Get status badge HTML
 * 
 * @param string $status The status
 * @return string The HTML badge
 */
function getStatusBadge($status) {
    switch ($status) {
        case 'completed':
            return '<span class="badge bg-success">Completed</span>';
        case 'in_progress':
            return '<span class="badge bg-warning">In Progress</span>';
        default:
            return '<span class="badge bg-danger">Not Started</span>';
    }
}

/**
 * Create a token for CSRF protection
 * 
 * @return string The CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token The token to verify
 * @return bool True if the token is valid, false otherwise
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * Calculate days remaining until due date
 * 
 * @param string $dueDate The due date
 * @return int Number of days remaining, negative if in the past
 */
function daysRemaining($dueDate) {
    $due = new DateTime($dueDate);
    $today = new DateTime();
    $interval = $today->diff($due);
    
    return $interval->invert ? -$interval->days : $interval->days;
}

/**
 * Check if user has a specific role
 * 
 * @param string $requiredRole The required role
 * @return bool True if the user has the role, false otherwise
 */
function hasRole($requiredRole) {
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    
    return $_SESSION['user_role'] === $requiredRole;
}
?>
