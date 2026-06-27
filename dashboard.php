<?php
// Start session
session_start();

// Include configuration and functions
require_once 'config.php';
require_once 'functions.php';

//added for email
// require_once 'email_functions.php';

//usage of email function
// $success = false;
// if ($success) {
//     $teacher_email = "chaatologyy@gmail.com";
//     $teacher_password = "dsbnkxcogimziclq";

//     sendAssignmentNotification(
//         $email, // Student's email
//         $name, // Student's name
//         "Welcome to TaskTick", // Assignment title
//         "You have successfully registered for TaskTick.", // Assignment description
//         date('Y-m-d'), // Due date (optional for this case)
//         $teacher_email, // Teacher's email
//         $teacher_password // Teacher's password
//     );
// }


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];

// Connect to database
$conn = connectDB();

// Get assignments based on user role
$assignments = [];

if ($user_role == 'teacher') {
    // Get assignments created by the teacher
    $stmt = $conn->prepare("SELECT a.*, COUNT(sa.id) as student_count 
                          FROM assignments a 
                          LEFT JOIN student_assignments sa ON a.id = sa.assignment_id 
                          WHERE a.teacher_id = ? 
                          GROUP BY a.id 
                          ORDER BY a.due_date ASC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }
    
    $stmt->close();
    
    // Get completion statistics
    $stats_query = "SELECT 
                    COUNT(CASE WHEN sa.status = 'completed' THEN 1 END) as completed,
                    COUNT(CASE WHEN sa.status = 'in_progress' THEN 1 END) as in_progress,
                    COUNT(CASE WHEN sa.status = 'not_started' THEN 1 END) as not_started
                  FROM student_assignments sa
                  JOIN assignments a ON sa.assignment_id = a.id
                  WHERE a.teacher_id = ?";
    
    $stats_stmt = $conn->prepare($stats_query);
    $stats_stmt->bind_param("i", $user_id);
    $stats_stmt->execute();
    $stats_result = $stats_stmt->get_result();
    $stats = $stats_result->fetch_assoc();
    $stats_stmt->close();
    
} else {
    // Get assignments assigned to the student
    $stmt = $conn->prepare("SELECT a.*, sa.status, sa.submission, sa.feedback, u.name as teacher_name 
                          FROM assignments a 
                          JOIN student_assignments sa ON a.id = sa.assignment_id 
                          JOIN users u ON a.teacher_id = u.id 
                          WHERE sa.student_id = ? 
                          ORDER BY a.due_date ASC");
    


    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }
    
    $stmt->close();
    
    // Get completion statistics
    $stats_query = "SELECT 
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
                    COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress,
                    COUNT(CASE WHEN status = 'not_started' THEN 1 END) as not_started
                  FROM student_assignments
                  WHERE student_id = ?";
    
    $stats_stmt = $conn->prepare($stats_query);
    $stats_stmt->bind_param("i", $user_id);
    $stats_stmt->execute();
    $stats_result = $stats_stmt->get_result();
    $stats = $stats_result->fetch_assoc();
    $stats_stmt->close();
}

// Close database connection
$conn->close();

// Set the page title
$page_title = "Dashboard - Student Assignment Tracker";

// Include header
include_once 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Welcome, <?php echo htmlspecialchars($user_name); ?></h1>
                <?php if ($user_role == 'teacher'): ?>
                    <a href="create_assignment.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Assignment
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Completed</h5>
                    <h2><?php echo isset($stats['completed']) ? $stats['completed'] : 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">In Progress</h5>
                    <h2><?php echo isset($stats['in_progress']) ? $stats['in_progress'] : 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Not Started</h5>
                    <h2><?php echo isset($stats['not_started']) ? $stats['not_started'] : 0; ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <?php if ($user_role == 'teacher'): ?>
                        <h3>Your Created Assignments</h3>
                    <?php else: ?>
                        <h3>Your Assigned Assignments</h3>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($assignments)): ?>
                        <div class="alert alert-info">
                            <?php if ($user_role == 'teacher'): ?>
                                You haven't created any assignments yet. Click the "Create New Assignment" button to get started.
                            <?php else: ?>
                                You don't have any assignments yet. They will appear here when your teachers assign them to you.
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Due Date</th>
                                        <?php if ($user_role == 'teacher'): ?>
                                            <th>Students Assigned</th>
                                        <?php else: ?>
                                            <th>Teacher</th>
                                            <th>Status</th>
                                        <?php endif; ?>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assignments as $assignment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($assignment['description'], 0, 50)) . (strlen($assignment['description']) > 50 ? '...' : ''); ?></td>
                                            <td>
                                                <?php 
                                                $due_date = new DateTime($assignment['due_date']);
                                                echo $due_date->format('M d, Y'); 
                                                
                                                // Check if assignment is overdue
                                                $today = new DateTime();
                                                if ($today > $due_date && ($user_role == 'student' && $assignment['status'] != 'completed')) {
                                                    echo ' <span class="badge bg-danger">Overdue</span>';
                                                }
                                                ?>
                                            </td>
                                            <?php if ($user_role == 'teacher'): ?>
                                                <td><?php echo $assignment['student_count']; ?></td>
                                            <?php else: ?>
                                                <td><?php echo htmlspecialchars($assignment['teacher_name']); ?></td>
                                                <td>
                                                    <?php 
                                                    $status_badge = '';
                                                    switch ($assignment['status']) {
                                                        case 'completed':
                                                            $status_badge = '<span class="badge bg-success">Completed</span>';
                                                            break;
                                                        case 'in_progress':
                                                            $status_badge = '<span class="badge bg-warning">In Progress</span>';
                                                            break;
                                                        default:
                                                            $status_badge = '<span class="badge bg-danger">Not Started</span>';
                                                            break;
                                                    }
                                                    echo $status_badge;
                                                    ?>
                                                </td>
                                            <?php endif; ?>
                                            <td>
                                                <a href="view_assignment.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="main.js"></script>

<?php
// Include footer
include_once 'footer.php';
?>
