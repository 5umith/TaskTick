<?php
// Start session
session_start();

// Redirect to dashboard only for teachers; allow others to see landing page
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'teacher') {
    header("Location: dashboard.php");
    exit();
}

// Set the page title
$page_title = "Student Assignment Tracker - Home";

// Include header
include_once 'header.php';
?>
<link rel="stylesheet" href="assets/css/style.css">
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8 text-center">
            <h1 class="display-4">Student Assignment Tracker</h1>
            <p class="lead mt-3">An efficient way to track and manage student assignments</p>
            <div class="mt-5">
                <a href="login.php" class="btn btn-primary btn-lg mx-2">Login</a>
                <a href="register.php" class="btn btn-outline-primary btn-lg mx-2">Register</a>
            </div>
        </div>
    </div>
    
    <div class="row mt-5 pt-5">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                    <h3>For Teachers</h3>
                    <p>Create and assign projects to your students. Monitor their progress and provide feedback.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-user-graduate fa-3x mb-3"></i>
                    <h3>For Students</h3>
                    <p>View your assignments, update your progress, and receive notifications for new tasks.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                    <h3>Track Progress</h3>
                    <p>Visualize progress with intuitive dashboards and stay on top of deadlines.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'footer.php';
?>
