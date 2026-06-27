<?php
// filepath: c:\xampp\htdocs\TaskTick\email_functions.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer via Composer

function sendAssignmentNotification($to_email, $to_name, $assignment_title, $assignment_description, $due_date, $teacher_email, $teacher_password) {
    $formatted_date = date('F j, Y', strtotime($due_date));
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use the SMTP server for the teacher's email provider
        $mail->SMTPAuth = true;
        $mail->Username = $teacher_email; // Teacher's email
        $mail->Password = $teacher_password; // Teacher's email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom($teacher_email, 'TaskTick'); // Sender's email and name
        $mail->addAddress($to_email, $to_name); // Recipient's email and name

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Assignment: " . $assignment_title;
        $mail->Body = "
        <html>
        <body>
            <h2>New Assignment</h2>
            <p>Hello $to_name,</p>
            <p>You have been assigned a new assignment:</p>
            <h3>$assignment_title</h3>
            <p><strong>Due Date:</strong> $formatted_date</p>
            <p><strong>Description:</strong></p>
            <p>" . nl2br(htmlspecialchars($assignment_description)) . "</p>
            <p>Please log in to your account to view more details and update your progress.</p>
        </body>
        </html>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>