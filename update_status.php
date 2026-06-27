<?php
// Start session
session_start();

// Include configuration and functions
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if request is AJAX and POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get user information
$student_id = $_SESSION['user_id'];

// Get and validate input
$assignment_id = isset($_POST['assignment_id']) ? (int)$_POST['assignment_id'] : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';
$submission = isset($_POST['submission']) ? $_POST['submission'] : '';

// Validate status
if (!in_array($status, ['not_started', 'in_progress', 'completed'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

// Validate assignment_id
if ($assignment_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid assignment ID']);
    exit();
}

// Connect to database
$conn = connectDB();

// Check if the student is assigned to this assignment
$check_stmt = $conn->prepare("SELECT id FROM student_assignments WHERE assignment_id = ? AND student_id = ?");
$check_stmt->bind_param("ii", $assignment_id, $student_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows == 0) {
    $check_stmt->close();
    $conn->close();
    
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'You are not assigned to this assignment']);
    exit();
}

$check_stmt->close();

// Update assignment status
$update_stmt = $conn->prepare("UPDATE student_assignments SET status = ?, submission = ?, updated_at = NOW() WHERE assignment_id = ? AND student_id = ?");
$update_stmt->bind_param("ssii", $status, $submission, $assignment_id, $student_id);

if ($update_stmt->execute()) {
    $update_stmt->close();
    $conn->close();
    
    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    exit();
} else {
    $error = $conn->error;
    $update_stmt->close();
    $conn->close();
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error updating status: ' . $error]);
    exit();
}
?>
