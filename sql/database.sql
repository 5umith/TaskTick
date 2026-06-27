-- Create enum types
CREATE TYPE user_role AS ENUM ('teacher', 'student');
CREATE TYPE assignment_status AS ENUM ('not_started', 'in_progress', 'completed');

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id SERIAL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role user_role NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Assignments table
CREATE TABLE IF NOT EXISTS assignments (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  due_date DATE NOT NULL,
  teacher_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create index on teacher_id
CREATE INDEX idx_assignments_teacher_id ON assignments(teacher_id);

-- Student assignments table (for tracking student progress)
CREATE TABLE IF NOT EXISTS student_assignments (
  id SERIAL PRIMARY KEY,
  student_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  assignment_id INTEGER NOT NULL REFERENCES assignments(id) ON DELETE CASCADE,
  status assignment_status NOT NULL DEFAULT 'not_started',
  submission TEXT,
  feedback TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create unique constraint on student_id and assignment_id
CREATE UNIQUE INDEX idx_student_assignment ON student_assignments(student_id, assignment_id);
CREATE INDEX idx_student_assignments_assignment_id ON student_assignments(assignment_id);

-- Insert sample data (optional)
-- Uncomment these sections if you want to have sample data

-- Sample teachers
-- INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
-- ('John Smith', 'john.smith@example.com', '$2y$10$G8BOpB7XRCnF8GnL.8lZl.dbk6QE3/xTvKP3JYBzYPdYXOjyKT9A2', 'teacher'), -- Password: password123
-- ('Jane Doe', 'jane.doe@example.com', '$2y$10$YvL8acl3fI2ydQMi7MXWPew3oJWXk7o/vUfD7Oy4w5SAw5jbzScYC', 'teacher'); -- Password: password123

-- Sample students
-- INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
-- ('Alice Johnson', 'alice.johnson@example.com', '$2y$10$L2dFcGEkdwWiLcDr1iUt1.WmAvYqSZiKn50OuTZ0LHpAjAoZBqUKm', 'student'), -- Password: password123
-- ('Bob Williams', 'bob.williams@example.com', '$2y$10$57xRzp6cRZoJXhAP9yfJEOJXMQC67NndmCRARbwT6QQvJNR6md8gG', 'student'), -- Password: password123
-- ('Charlie Brown', 'charlie.brown@example.com', '$2y$10$YAI39EiS/ZMZ6HlP.XdwgeU0n2bDoOdwKP5qFAf91L9Yd5KxUpm9S', 'student'); -- Password: password123

-- Sample assignments
-- INSERT INTO `assignments` (`title`, `description`, `due_date`, `teacher_id`) VALUES
-- ('Introduction to HTML', 'Create a simple HTML webpage that includes headers, paragraphs, images, and links.', '2023-06-01', 1),
-- ('CSS Styling Exercise', 'Style the HTML webpage you created in the previous assignment using CSS.', '2023-06-15', 1),
-- ('JavaScript Basics', 'Create a simple interactive form using JavaScript validation.', '2023-06-30', 2);

-- Sample student assignments
-- INSERT INTO `student_assignments` (`student_id`, `assignment_id`, `status`, `submission`, `feedback`) VALUES
-- (3, 1, 'completed', 'I have completed this assignment. You can view my work at: https://example.com/my-html-page', 'Great work! Your HTML structure is well organized.'),
-- (3, 2, 'in_progress', 'I am still working on the CSS styling.', NULL),
-- (3, 3, 'not_started', NULL, NULL),
-- (4, 1, 'completed', 'Assignment completed on time.', 'Good job, but you could improve the semantic structure.'),
-- (4, 2, 'completed', 'CSS styling done with responsive design.', 'Excellent work on making it responsive!'),
-- (4, 3, 'in_progress', 'Started working on the JavaScript form validation.', NULL),
-- (5, 1, 'in_progress', 'I have created the basic structure but still need to add images.', NULL),
-- (5, 2, 'not_started', NULL, NULL),
-- (5, 3, 'not_started', NULL, NULL);
