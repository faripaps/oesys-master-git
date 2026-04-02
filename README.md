# OESYS - Online Examination System

## Overview
OESYS is a comprehensive web-based examination system built using PHP and MySQL that facilitates online testing for educational institutions, training centers, and organizations. The system provides separate interfaces for students and examiners with robust exam management capabilities.

## Features
- **User Management**: User registration and profile management with separate student and examiner roles.
- **Secure Authentication**: Secure login and session management system.
- **Exam Management**: Create and manage examination categories, design exams with various question types, and set time limits and schedules.
- **Real-Time Interface**: Real-time exam interface with question navigation and countdown timer.
- **Automated Workflows**: Auto-submission when time expires and automated scoring system.
- **Analytics & Reporting**: View detailed analytics on exam performance, access historical exam records with timestamps, and generate comprehensive student reports.

## Technology Stack
- **Backend**: PHP (7.4+)
- **Database**: MySQL (5.7+)
- **Frontend**: HTML5, Vanilla CSS, JavaScript
- **Server**: Apache/Nginx (or PHP Built-in Server for dev)

## Installation Requirements
- PHP 7.4 or higher (with PDO, MySQLi, MBstring extensions)
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

## Installation Steps

1. **Clone the Repository**
   Clone or download the project files into your web root directory.

2. **Database Setup**
   Create a new MySQL database:
   ```sql
   CREATE DATABASE oesys;
   USE oesys;
   SOURCE schema.sql;
   ```

3. **Configuration**
   - Navigate to the `config/` directory.
   - Update `database.php` with your local database credentials (host, dbname, user, password).
   - Configure global settings in `settings.php` as needed.

4. **File Permissions**
   Set appropriate permissions for the uploads and config directories (if required by your OS environment):
   ```bash
   chmod 755 uploads/
   chmod 644 config/
   ```

5. **Start the Application**
   - Access the system through your web browser via Apache/Nginx.
   - Or, run the built-in PHP development server from the root directory:
     ```bash
     php -S localhost:8000
     ```

## Default Access

**Admin/Examiner Login**
- **URL**: `http://localhost:8000/index.php` (or your domain)
- **Username**: `admin`
- **Password**: `adminpassword`

**Student Registration**
- **URL**: `http://localhost:8000/register.php` (or your domain)
- Students can register and instantly log in to take available published exams.

## Database Structure
Core Tables Include:
- `users`: Student and examiner accounts
- `categories`: Examination categories
- `exams`: Exam details and configurations (time limits, deadlines)
- `questions`: Question bank with options and correct answers
- `exam_attempts`: Student exam attempts with start/end timestamps
- `results`: Exam results and scoring
- `student_answers`: Specific answers recorded to calculate correct evaluations

## Directory Structure
```text
/
├── admin/          # Examiner Dashboard and Management
├── assets/         # CSS and JS files
├── config/         # System and Database Configurations
├── includes/       # Shared DB, Auth layout elements
├── student/        # Student Dashboard and Exam interaction
├── uploads/        # Asset Uploads
├── index.php       # Landing / Login
├── register.php    # Student Registration
├── logout.php      # Session flush
└── schema.sql      # Initialization Data
```

## Security Considerations
- Ensure database credentials in `config/database.php` are not exposed.
- Use an SSL Certificate for secure access in production environments.
- Maintain regular database backups.
- Keep PHP version and system dependencies up to date.

## Customization
The system is fully customizable and lightweight:
- Modify `assets/css/style.css` for styling overrides.
- Adjust logic securely inside the `includes/` configuration models.

## Support
For technical support and documentation:
- **Email**: georgepapaya@gmail.com
- **Issue tracking**: Open a ticket via our repository tracker.

## License
Provided 'AS IS' under standard usage terms.
