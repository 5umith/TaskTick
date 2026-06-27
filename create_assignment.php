<?php
// Start session
session_start();

// Include configuration and functions
require_once 'config.php';
require_once 'functions.php';
require_once 'send_email.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

// Get teacher ID
$teacher_id = $_SESSION['user_id'];

// Initialize variables
$title = "";
$description = "";
$due_date = "";
$selected_students = [];
$error = "";
$success = "";

// Connect to database
$conn = connectDB();

// Get all students
$student_query = "SELECT id, name, email FROM users WHERE role = 'student'";
$student_result = $conn->query($student_query);
$students = [];

if ($student_result->num_rows > 0) {
    while ($row = $student_result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize input
    $title = sanitizeInput($_POST["title"]);
    $description = sanitizeInput($_POST["description"]);
    $due_date = sanitizeInput($_POST["due_date"]);
    $selected_students = isset($_POST["students"]) ? $_POST["students"] : [];

    // Validate input
    if (empty($title)) {
        $error = "Title is required";
    } elseif (empty($description)) {
        $error = "Description is required";
    } elseif (empty($due_date)) {
        $error = "Due date is required";
    } elseif (empty($selected_students)) {
        $error = "You must select at least one student";
    } else {
        // Insert assignment into database
        $stmt = $conn->prepare("INSERT INTO assignments (title, description, due_date, teacher_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $description, $due_date, $teacher_id);
        
        if ($stmt->execute()) {
            $assignment_id = $conn->insert_id;
            
            // Assign the assignment to selected students
            $insert_success = true;
            $notified_students = [];
            
            foreach ($selected_students as $student_id) {
                // Validate student_id is numeric
                if (!is_numeric($student_id)) {
                    continue;
                }
                
                $assign_stmt = $conn->prepare("INSERT INTO student_assignments (student_id, assignment_id, status) VALUES (?, ?, 'not_started')");
                $assign_stmt->bind_param("ii", $student_id, $assignment_id);
                
                if ($assign_stmt->execute()) {
                    // Get student email for notification
                    $student_email_query = "SELECT name, email FROM users WHERE id = ?";
                    $student_email_stmt = $conn->prepare($student_email_query);
                    $student_email_stmt->bind_param("i", $student_id);
                    $student_email_stmt->execute();
                    $student_result = $student_email_stmt->get_result();
                    
                    if ($student_result->num_rows > 0) {
                        $student_data = $student_result->fetch_assoc();
                        $notified_students[] = [
                            'name' => $student_data['name'],
                            'email' => $student_data['email']
                        ];
                    }
                    
                    $student_email_stmt->close();
                } else {
                    $insert_success = false;
                }
                
                $assign_stmt->close();
            }
            
            if ($insert_success) {
                // Send email notifications to assigned students
                foreach ($notified_students as $student) {
                    sendAssignmentNotification($student['email'], $student['name'], $title, $description, $due_date);
                }
                
                $success = "Assignment created successfully and assigned to " . count($selected_students) . " students.";
                
                // Clear form after successful submission
                $title = "";
                $description = "";
                $due_date = "";
                $selected_students = [];
            } else {
                $error = "Error assigning the assignment to some students.";
            }
        } else {
            $error = "Error creating assignment: " . $conn->error;
        }
        
        $stmt->close();
    }
}

// Close database connection
$conn->close();

// Set the page title
$page_title = "Create Assignment - Student Assignment Tracker";

// Include header
include_once 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Create New Assignment</h1>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Assignment Details</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="createAssignmentForm">
                        <div class="mb-3">
                            <label for="title" class="form-label">Assignment Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($description); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo htmlspecialchars($due_date); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Assign to Students</label>
                            <?php if (empty($students)): ?>
                                <div class="alert alert-info">No students are registered in the system.</div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($students as $student): ?>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="students[]" 
                                                       value="<?php echo $student['id']; ?>" id="student_<?php echo $student['id']; ?>"
                                                       <?php echo in_array($student['id'], $selected_students) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="student_<?php echo $student['id']; ?>">
                                                    <?php echo htmlspecialchars($student['name']); ?> (<?php echo htmlspecialchars($student['email']); ?>)
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllStudents">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllStudents">Deselect All</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" <?php echo empty($students) ? 'disabled' : ''; ?>>
                                Create Assignment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select/Deselect all students functionality
    const selectAllBtn = document.getElementById('selectAllStudents');
    const deselectAllBtn = document.getElementById('deselectAllStudents');
    const studentCheckboxes = document.querySelectorAll('input[name="students[]"]');
    
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        });
    }
    
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        });
    }
    
    // Set minimum due date to today
    const dueDateInput = document.getElementById('due_date');
    if (dueDateInput) {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const todayStr = `${yyyy}-${mm}-${dd}`;
        dueDateInput.setAttribute('min', todayStr);
    }
});
</script>

<?php
// Include footer
include_once 'footer.php';
?>
