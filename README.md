# TaskTick

TaskTick is a PHP-based student assignment tracking system that helps teachers create assignments, assign them to students, monitor progress, and provide feedback. Students can view their assigned work, update completion status, submit notes or links, and receive assignment notifications by email.

## Features

- Teacher and student registration/login
- Role-based dashboard for teachers and students
- Teachers can create assignments with title, description, due date, and selected students
- Students can update assignment status:
  - Not Started
  - In Progress
  - Completed
- Teachers can view student progress and provide feedback
- Email notifications for newly assigned tasks using PHPMailer
- Forgot password flow with OTP verification
- Bootstrap-based responsive UI
- Basic client-side and server-side validation

## Tech Stack

- PHP
- MySQL / MariaDB
- Composer
- PHPMailer
- Bootstrap 5
- Font Awesome
- JavaScript

## Project Structure

```text
TaskTick/
├── assets/
│   ├── css/
│   └── js/
├── includes/
├── sql/
│   └── database.sql
├── vendor/
├── config.php
├── index.php
├── login.php
├── register.php
├── dashboard.php
├── create_assignment.php
├── view_assignment.php
├── forgotPassword.php
├── verifyOTP.php
├── send_email.php
├── composer.json
└── README.md
