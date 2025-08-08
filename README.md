# CAPS - Plain PHP Application

This is a converted Laravel application to plain PHP, providing user authentication, admin management, and basic CRUD operations.

## Features

- **User Authentication**: Login, registration, and logout functionality
- **Admin Panel**: User management, settings, and reports
- **Database Integration**: PDO-based database operations
- **Input Validation**: Custom validation system
- **Session Management**: Secure session handling
- **Password Security**: Bcrypt password hashing

## Directory Structure

```
capstone/
├── config.php              # Database configuration
├── index.php               # Main entry point and routing
├── database_setup.sql      # Database schema and setup
├── includes/               # Core PHP classes
│   ├── Database.php       # Database connection and operations
│   ├── Auth.php          # Authentication management
│   ├── User.php          # User model
│   ├── Validator.php     # Input validation
│   └── helpers.php       # Helper functions
├── controllers/           # Application controllers
│   └── UserController.php # User and admin operations
└── views/                 # Application views
    ├── login.php         # Login page
    ├── register.php      # Registration page
    ├── welcome.php       # Welcome page
    ├── admin/           # Admin views
    │   ├── dashboard.php
    │   ├── users/
    │   ├── settings/
    │   └── reports/
    └── errors/          # Error pages
        └── 404.php
```

## Setup Instructions

### 1. Database Setup

1. Open phpMyAdmin or your MySQL client
2. Import the `database_setup.sql` file
3. This will create the `caps_db` database with all necessary tables
4. A default admin user will be created:
   - Email: `admin@caps.com`
   - Password: `admin123`

### 2. Configuration

1. Edit `config.php` to match your database settings:
   ```php
   return [
       'host' => 'localhost',
       'dbname' => 'caps_db',
       'username' => 'root',
       'password' => '',
       'charset' => 'utf8mb4'
   ];
   ```

### 3. Web Server Setup

1. Ensure your web server (XAMPP) is running
2. Access the application via: `http://localhost/capstone/`
3. The application will automatically route requests to appropriate controllers

## Usage

### User Features
- **Login**: Navigate to `/login` or click "Login" on the welcome page
- **Register**: Navigate to `/register` or click "Register" on the welcome page
- **Dashboard**: After login, users are redirected to their dashboard

### Admin Features
- **Admin Dashboard**: Access via `/admin/dashboard` (admin users only)
- **User Management**: View and manage all users at `/admin/users`
- **Settings**: System configuration at `/admin/settings`
- **Reports**: Analytics and reports at `/admin/reports`

## Security Features

- **CSRF Protection**: All forms include CSRF tokens
- **Password Hashing**: Passwords are securely hashed using bcrypt
- **Session Security**: Secure session management
- **Input Validation**: Comprehensive input validation and sanitization
- **SQL Injection Prevention**: Prepared statements for all database queries

## Technical Details

### Database Schema
- **users**: User accounts with authentication data
- **password_resets**: Password reset functionality (for future use)
- **failed_jobs**: Job queue management (for future use)
- **personal_access_tokens**: API token management (for future use)

### Core Classes
- **Database**: Singleton PDO wrapper for database operations
- **Auth**: User authentication and session management
- **User**: User model with CRUD operations
- **Validator**: Input validation with multiple rule types

### Helper Functions
- `redirect()`: Redirect to specified URL
- `back()`: Redirect back to previous page
- `old()`: Retrieve old form input
- `csrf_token()`: Generate CSRF token
- `asset()`: Generate asset URL
- `view()`: Render view with data
- `auth_check()`: Check if user is logged in
- `auth_user()`: Get current user data

## Troubleshooting

1. **Database Connection Error**: Check your database configuration in `config.php`
2. **404 Errors**: Ensure your web server is properly configured
3. **Session Issues**: Check PHP session configuration
4. **Permission Errors**: Ensure proper file permissions

## Default Credentials

- **Admin User**:
  - Email: `admin@caps.com`
  - Password: `admin123`

## License

This application is converted from a Laravel project to plain PHP for educational purposes.
