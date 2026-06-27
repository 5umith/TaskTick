<?php
require_once __DIR__ . '/config.php';

$autoloadPaths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
];

foreach ($autoloadPaths as $autoloadPath) {
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
        break;
    }
}

/**
 * Send an email notification for a new assignment
 *
 * @param string $to_email Recipient email address
 * @param string $to_name Recipient name
 * @param string $assignment_title Assignment title
 * @param string $assignment_description Assignment description
 * @param string $due_date Assignment due date
 * @return bool True if email sent successfully, false otherwise
 */
function sendAssignmentNotification($to_email, $to_name, $assignment_title, $assignment_description, $due_date) {
    if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        error_log('PHPMailer is not available. Run composer install in the project root.');
        return false;
    }

    $formatted_date = date('F j, Y', strtotime($due_date));
    $subject = 'New Assignment: ' . $assignment_title;
    $loginUrl = buildAssignmentLoginUrl();

    $message = "
    <html>
    <head>
        <title>New Assignment Notification</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #007bff; color: white; padding: 10px 20px; border-radius: 5px 5px 0 0; }
            .content { background-color: #f9f9f9; padding: 20px; border-radius: 0 0 5px 5px; }
            .footer { font-size: 12px; color: #777; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Assignment</h2>
            </div>
            <div class='content'>
                <p>Hello $to_name,</p>
                <p>You have been assigned a new assignment:</p>
                <h3>$assignment_title</h3>
                <p><strong>Due Date:</strong> $formatted_date</p>
                <p><strong>Description:</strong></p>
                <p>" . nl2br(htmlspecialchars($assignment_description, ENT_QUOTES, 'UTF-8')) . "</p>
                <p>Please log in to your account to view more details and update your progress.</p>
                <p><a href='" . htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') . "' style='background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Login Now</a></p>
            </div>
            <div class='footer'>
                <p>This is an automated email from " . APP_NAME . ". Please do not reply to this message.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $plainTextMessage = "Hello {$to_name},\n\n"
        . "You have been assigned a new assignment:\n"
        . "Title: {$assignment_title}\n"
        . "Due Date: {$formatted_date}\n\n"
        . "Description:\n{$assignment_description}\n\n"
        . "Please log in to your account at " . $loginUrl . " to view more details and update your progress.\n";

    try {
        $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = SMTP_HOST;
        $mailer->Port = (int) SMTP_PORT;
        $mailer->CharSet = 'UTF-8';
        $mailer->SMTPAuth = !empty(SMTP_USER);
        $mailer->Username = SMTP_USER;
        $mailer->Password = SMTP_PASS;
        $mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $fromAddress = !empty(SMTP_FROM) ? SMTP_FROM : SMTP_USER;
        $fromName = !empty(SMTP_FROM_NAME) ? SMTP_FROM_NAME : APP_NAME;
        if (empty($fromAddress)) {
            throw new \RuntimeException('SMTP_FROM or SMTP_USER must be configured.');
        }
        $mailer->setFrom($fromAddress, $fromName);
        $mailer->addAddress($to_email, $to_name);
        $mailer->isHTML(true);
        $mailer->Subject = $subject;
        $mailer->Body = $message;
        $mailer->AltBody = $plainTextMessage;

        return $mailer->send();
    } catch (\Throwable $e) {
        error_log('Email sending failed: ' . $e->getMessage());
        return false;
    }
}

function buildAssignmentLoginUrl() {
    if (!empty($_SERVER['HTTP_HOST'])) {
        $scriptName = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_NAME']) : '';
        $scriptDir = rtrim(dirname($scriptName), '/');

        if ($scriptDir === '.' || $scriptDir === '/') {
            $scriptDir = '';
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        return $scheme . '://' . $_SERVER['HTTP_HOST'] . $scriptDir . '/login.php';
    }

    $baseUrl = defined('APP_URL') ? rtrim(APP_URL, '/') : '';

    if ($baseUrl !== '' && stripos($baseUrl, '/TaskTick') === false) {
        $baseUrl .= '/TaskTick';
    }

    return $baseUrl . '/login.php';
}
?>
