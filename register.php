<?php
// Start session
session_start();

// Include configuration and functions
require_once 'config.php';
require_once 'functions.php';

// Redirect to dashboard if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Initialize variables
$name = "";
$email = "";
$error = "";
$success = "";

// Process the registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize input
    $name = sanitizeInput($_POST["name"]);
    $email = sanitizeInput($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $role = sanitizeInput($_POST["role"]);

    // Validate input
    if (empty($name)) {
        $error = "Name is required";
    } elseif (empty($email)) {
        $error = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (empty($password)) {
        $error = "Password is required";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } elseif ($password != $confirm_password) {
        $error = "Passwords do not match";
    } elseif (!in_array($role, ['teacher', 'student'])) {
        $error = "Invalid role selected";
    } else {
        // Connect to database
        $conn = connectDB();

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Email already exists";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Prepare and execute the insert statement
            $insert_stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
            
            if ($insert_stmt->execute()) {
                $success = "Registration successful. You can now login.";
                // Clear form fields after successful registration
                $name = "";
                $email = "";
            } else {
                $error = "Registration failed: " . $conn->error;
            }
            
            $insert_stmt->close();
        }
        
        // Close connection
        $stmt->close();
        $conn->close();
    }
}

// Set the page title
$page_title = "Register - Student Assignment Tracker";

// Include header
include_once 'header.php';
?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Register</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="registrationForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="text-muted">Password must be at least 6 characters</small>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">I am a:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="teacher" value="teacher" required>
                                <label class="form-check-label" for="teacher">Teacher</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="student" value="student" required>
                                <label class="form-check-label" for="student">Student</label>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">Already have an account? <a href="login.php">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="validation.js"></script>

<?php
// Include footer
include_once 'footer.php';
?>
