<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'config.php';
require 'functions.php';
require 'vendor/autoload.php';

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

function ensurePasswordResetColumnsForVerify($conn) {
    $checkToken = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'reset_token'");
    if ($checkToken && mysqli_num_rows($checkToken) === 0) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN reset_token VARCHAR(10) NULL");
    }

    $checkExpiry = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'reset_token_expires'");
    if ($checkExpiry && mysqli_num_rows($checkExpiry) === 0) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN reset_token_expires DATETIME NULL");
    }
}

ensurePasswordResetColumnsForVerify($conn);

$email = isset($_GET['email']) ? $_GET['email'] : '';
$otp = isset($_GET['otp']) ? $_GET['otp'] : '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $otp = trim($_POST['otp'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($email === '' || $otp === '' || $newPassword === '' || $confirmPassword === '') {
        $error = 'All fields are required.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, reset_token, reset_token_expires FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) === 1) {
            mysqli_stmt_bind_result($stmt, $userId, $storedToken, $tokenExpires);
            mysqli_stmt_fetch($stmt);

            $tokenMatches = ((string) $storedToken === (string) $otp);
            $tokenValid = !empty($tokenExpires) && strtotime($tokenExpires) >= time();

            if ($tokenMatches && $tokenValid) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateStmt = mysqli_prepare($conn, "UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
                mysqli_stmt_bind_param($updateStmt, 'si', $hashedPassword, $userId);
                mysqli_stmt_execute($updateStmt);
                mysqli_stmt_close($updateStmt);
                $success = 'Password reset successful. You can now log in.';
                $email = '';
                $otp = '';
            } else {
                $error = 'Invalid or expired OTP.';
            }
        } else {
            $error = 'No account found for that email.';
        }

        mysqli_stmt_close($stmt);
    }
}

$page_title = 'Verify OTP - Student Assignment Tracker';
include_once 'header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center mb-0">Verify OTP</h3>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted text-center mb-4">Enter the OTP from your email and choose a new password.</p>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="otp" class="form-label">OTP</label>
                            <input type="text" class="form-control" id="otp" name="otp" value="<?php echo htmlspecialchars($otp); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Reset Password</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center bg-white">
                    <a href="login.php" class="text-decoration-none">Back to login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>