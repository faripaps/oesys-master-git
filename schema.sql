CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `role` ENUM('student', 'examiner') NOT NULL DEFAULT 'student',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS `exams` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `category_id` INT,
  `time_limit` INT NOT NULL COMMENT 'Time limit in minutes',
  `status` ENUM('draft', 'published', 'closed') DEFAULT 'draft',
  `created_by` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `questions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `exam_id` INT NOT NULL,
  `type` ENUM('mcq', 'tf', 'descriptive') NOT NULL DEFAULT 'mcq',
  `question_text` TEXT NOT NULL,
  `options_json` TEXT COMMENT 'JSON array of options for MCQ',
  `correct_answer` TEXT NOT NULL COMMENT 'Could be string or JSON depends on type',
  `marks` INT DEFAULT 1,
  FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `exam_attempts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `exam_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME,
  `status` ENUM('in_progress', 'completed', 'abandoned') DEFAULT 'in_progress',
  FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `results` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `attempt_id` INT NOT NULL UNIQUE,
  `score` DECIMAL(5,2) DEFAULT 0,
  `total_marks` INT DEFAULT 0,
  `passed` BOOLEAN DEFAULT FALSE,
  `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`attempt_id`) REFERENCES `exam_attempts`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `student_answers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `attempt_id` INT NOT NULL,
  `question_id` INT NOT NULL,
  `given_answer` TEXT,
  `is_correct` BOOLEAN DEFAULT FALSE,
  `marks_awarded` DECIMAL(5,2) DEFAULT 0,
  FOREIGN KEY (`attempt_id`) REFERENCES `exam_attempts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) ON DELETE CASCADE
);

-- Insert default admin user
-- Username: admin, Password: adminpassword (hashed)
INSERT IGNORE INTO `users` (`username`, `password`, `email`, `role`) VALUES ('admin', '$2y$10$tZ92rMyw61pL0.2b6C.fGuNq48aG.X5D9N8jOM2Y1M1L38nE3A9wO', 'admin@example.com', 'examiner');
