<?php
// Start session
session_start();

// Include configuration and functions
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Check if assignment ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$assignment_id = (int)$_GET['id'];

// Connect to database
$conn = connectDB();

// Get assignment details based on user role
$assignment = null;
$assigned_students = [];

if ($user_role == 'teacher') {
    // Check if the teacher is the creator of the assignment
    $stmt = $conn->prepare("SELECT * FROM assignments WHERE id = ? AND teacher_id = ?");
    $stmt->bind_param("ii", $assignment_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $assignment = $result->fetch_assoc();
        
        // Get assigned students and their progress
        $students_stmt = $conn->prepare("SELECT sa.*, u.name, u.email 
                                      FROM student_assignments sa 
                                      JOIN users u ON sa.student_id = u.id 
                                      WHERE sa.assignment_id = ?");
        $students_stmt->bind_param("i", $assignment_id);
        $students_stmt->execute();
        $students_result = $students_stmt->get_result();
        
        while ($row = $students_result->fetch_assoc()) {
            $assigned_students[] = $row;
        }
        
        $students_stmt->close();
    }
    
    $stmt->close();
} else {
    // Check if the student is assigned to this assignment
    $stmt = $conn->prepare("SELECT a.*, sa.status, sa.submission, sa.feedback, u.name as teacher_name 
                          FROM assignments a 
                          JOIN student_assignments sa ON a.id = sa.assignment_id 
                          JOIN users u ON a.teacher_id = u.id 
                          WHERE a.id = ? AND sa.student_id = ?");
    $stmt->bind_param("ii", $assignment_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $assignment = $result->fetch_assoc();
    }
    
    $stmt->close();
}

// If assignment not found or user doesn't have access
if (!$assignment) {
    header("Location: dashboard.php");
    exit();
}

// Handle student assignment update
$update_success = false;
$update_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && $user_role == 'student') {
    $new_status = sanitizeInput($_POST['status']);
    $submission = sanitizeInput($_POST['submission']);
    
    // Validate status
    if (!in_array($new_status, ['not_started', 'in_progress', 'completed'])) {
        $update_error = "Invalid status selected";
    } else {
        // Update student assignment status
        $update_stmt = $conn->prepare("UPDATE student_assignments SET status = ?, submission = ? WHERE assignment_id = ? AND student_id = ?");
        $update_stmt->bind_param("ssii", $new_status, $submission, $assignment_id, $user_id);
        
        if ($update_stmt->execute()) {
            $update_success = true;
            
            // Refresh assignment data
            $refresh_stmt = $conn->prepare("SELECT a.*, sa.status, sa.submission, sa.feedback, u.name as teacher_name 
                                         FROM assignments a 
                                         JOIN student_assignments sa ON a.id = sa.assignment_id 
                                         JOIN users u ON a.teacher_id = u.id 
                                         WHERE a.id = ? AND sa.student_id = ?");
            $refresh_stmt->bind_param("ii", $assignment_id, $user_id);
            $refresh_stmt->execute();
            $refresh_result = $refresh_stmt->get_result();
            
            if ($refresh_result->num_rows > 0) {
                $assignment = $refresh_result->fetch_assoc();
            }
            
            $refresh_stmt->close();
        } else {
            $update_error = "Failed to update status: " . $conn->error;
        }
        
        $update_stmt->close();
    }
}

// Handle teacher providing feedback
if ($_SERVER["REQUEST_METHOD"] == "POST" && $user_role == 'teacher' && isset($_POST['student_id']) && isset($_POST['feedback'])) {
    $student_id = (int)$_POST['student_id'];
    $feedback = sanitizeInput($_POST['feedback']);
    
    // Update feedback
    $feedback_stmt = $conn->prepare("UPDATE student_assignments SET feedback = ? WHERE assignment_id = ? AND student_id = ?");
    $feedback_stmt->bind_param("sii", $feedback, $assignment_id, $student_id);
    
    if ($feedback_stmt->execute()) {
        $update_success = true;
        
        // Refresh assigned students data
        $students_stmt = $conn->prepare("SELECT sa.*, u.name, u.email 
                                      FROM student_assignments sa 
                                      JOIN users u ON sa.student_id = u.id 
                                      WHERE sa.assignment_id = ?");
        $students_stmt->bind_param("i", $assignment_id);
        $students_stmt->execute();
        $students_result = $students_stmt->get_result();
        
        $assigned_students = [];
        while ($row = $students_result->fetch_assoc()) {
            $assigned_students[] = $row;
        }
        
        $students_stmt->close();
    } else {
        $update_error = "Failed to save feedback: " . $conn->error;
    }
    
    $feedback_stmt->close();
}

// Close database connection
$conn->close();

// Set the page title
$page_title = "View Assignment - Student Assignment Tracker";

// Include header
include_once 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><?php echo htmlspecialchars($assignment['title']); ?></h1>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
    
    <?php if ($update_success): ?>
        <div class="alert alert-success">
            <?php if ($user_role == 'student'): ?>
                Your progress has been updated successfully.
            <?php else: ?>
                Feedback has been saved successfully.
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($update_error)): ?>
        <div class="alert alert-danger"><?php echo $update_error; ?></div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Assignment Details</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Description:</h5>
                        <p><?php echo nl2br(htmlspecialchars($assignment['description'])); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Due Date:</h5>
                        <p>
                            <?php 
                            $due_date = new DateTime($assignment['due_date']);
                            echo $due_date->format('F d, Y'); 
                            
                            // Check if assignment is overdue for students
                            $today = new DateTime();
                            if ($user_role == 'student' && $today > $due_date && $assignment['status'] != 'completed') {
                                echo ' <span class="badge bg-danger">Overdue</span>';
                            }
                            ?>
                        </p>
                    </div>
                    
                    <?php if ($user_role == 'student'): ?>
                        <div class="mb-3">
                            <h5>Assigned By:</h5>
                            <p><?php echo htmlspecialchars($assignment['teacher_name']); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <h5>Current Status:</h5>
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
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($user_role == 'student'): ?>
                <!-- Student Update Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Update Your Progress</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $assignment_id; ?>">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="not_started" <?php echo ($assignment['status'] == 'not_started') ? 'selected' : ''; ?>>Not Started</option>
                                    <option value="in_progress" <?php echo ($assignment['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="completed" <?php echo ($assignment['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="submission" class="form-label">Your Submission/Notes</label>
                                <textarea class="form-control" id="submission" name="submission" rows="4"><?php echo htmlspecialchars($assignment['submission'] ?? ''); ?></textarea>
                                <div class="form-text">Enter your submission details, links to your work, or any notes for your teacher.</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Update Progress</button>
                        </form>
                    </div>
                </div>
                
                <?php if (!empty($assignment['feedback'])): ?>
                    <!-- Feedback from Teacher -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h3>Teacher Feedback</h3>
                        </div>
                        <div class="card-body">
                            <p><?php echo nl2br(htmlspecialchars($assignment['feedback'])); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <?php if ($user_role == 'teacher'): ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3>Student Progress</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($assigned_students)): ?>
                            <div class="alert alert-info">No students have been assigned to this assignment.</div>
                        <?php else: ?>
                            <div class="accordion" id="studentAccordion">
                                <?php foreach ($assigned_students as $index => $student): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                            <button class="accordion-button <?php echo ($index > 0) ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="<?php echo ($index === 0) ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $index; ?>">
                                                <?php echo htmlspecialchars($student['name']); ?>
                                                <?php 
                                                switch ($student['status']) {
                                                    case 'completed':
                                                        echo '<span class="badge bg-success ms-2">Completed</span>';
                                                        break;
                                                    case 'in_progress':
                                                        echo '<span class="badge bg-warning ms-2">In Progress</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge bg-danger ms-2">Not Started</span>';
                                                        break;
                                                }
                                                ?>
                                            </button>
                                        </h2>
                                        <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse <?php echo ($index === 0) ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#studentAccordion">
                                            <div class="accordion-body">
                                                <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                                                
                                                <?php if (!empty($student['submission'])): ?>
                                                    <div class="mb-3">
                                                        <h5>Student Submission:</h5>
                                                        <div class="p-3 bg-light rounded">
                                                            <?php echo nl2br(htmlspecialchars($student['submission'])); ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $assignment_id; ?>">
                                                    <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                                                    <div class="mb-3">
                                                        <label for="feedback<?php echo $student['student_id']; ?>" class="form-label">Feedback</label>
                                                        <textarea class="form-control" id="feedback<?php echo $student['student_id']; ?>" name="feedback" rows="3"><?php echo htmlspecialchars($student['feedback'] ?? ''); ?></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-sm">Save Feedback</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Include footer
include_once 'footer.php';
?>
