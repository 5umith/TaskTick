<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'config.php';

// Create MySQLi connection using config constants
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$error = "";
$success = "";

function ensurePasswordResetColumns($conn) {
    $columns = ['reset_token', 'reset_token_expires'];

    foreach ($columns as $column) {
        $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE '" . mysqli_real_escape_string($conn, $column) . "'");
        if ($checkColumn && mysqli_num_rows($checkColumn) === 0) {
            if ($column === 'reset_token') {
                mysqli_query($conn, "ALTER TABLE users ADD COLUMN reset_token VARCHAR(10) NULL");
            }

            if ($column === 'reset_token_expires') {
                mysqli_query($conn, "ALTER TABLE users ADD COLUMN reset_token_expires DATETIME NULL");
            }
        }
    }
}

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];

    if(!empty($email)) {
        ensurePasswordResetColumns($conn);

        $stmt = mysqli_prepare($conn, "SELECT id, name, email FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            // Generate a unique OTP
            $otp = rand(100000, 999999);

            $updateStmt = mysqli_prepare($conn, "UPDATE users SET reset_token = ?, reset_token_expires = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE email = ?");
            mysqli_stmt_bind_param($updateStmt, 'ss', $otp, $email);
            mysqli_stmt_execute($updateStmt);

            mysqli_stmt_close($updateStmt);

            // Build reset link using app config
            $appScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $appHost = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
            $resetLink = $appScheme . '://' . $appHost . '/TaskTick/verifyOTP.php?email=' . urlencode($email) . '&otp=' . $otp;

            // Send OTP to the user's email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USER; // from config.php
                $mail->Password = SMTP_PASS; // from config.php
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = SMTP_PORT;
                $mail->CharSet = 'UTF-8';

                //Recipients
                $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset OTP';
                $mail->Body    = "Your OTP for password reset is: $otp<br><a href='$resetLink'>Click here to reset your password</a>";

                $mail->send();
                $success = "OTP has been sent to your email.";
            } catch (Exception $e) {
                $err = htmlspecialchars($e->getMessage(), ENT_QUOTES);
                $error = "Message could not be sent. Mailer Error: {$err}";
            }
        } else {
            $error = "No user found with that email!";
        }

        mysqli_stmt_close($stmt);
    } else {
        $error = "Please enter a valid email!";
    }
}
?>
<?php
$page_title = "Forgot Password - Student Assignment Tracker";
include_once 'header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center mb-0">Forgot Password</h3>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted text-center mb-4">Enter your registered email address and we will send you a password reset OTP.</p>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Enter your registered email" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Send OTP</button>
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