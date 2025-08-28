# Folder-Based Access Control System

This document explains the folder-based access control system implemented for the Barangay Commonwealth Administration System.

## Overview

The system implements a hierarchical role-based access control (RBAC) system with four user levels:

1. **Super Admin** (Level 3) - Highest privileges
2. **Secretary Admin** (Level 2) - Medium-high privileges  
3. **Admin** (Level 1) - Medium privileges
4. **User** (Level 0) - Basic privileges

## User Hierarchy

```
Super Admin (Level 3)
├── Can access: super admin/, sec admin/, admin/, user areas
├── Can manage all users and permissions
└── Full system control

Secretary Admin (Level 2)
├── Can access: sec admin/, admin/, user areas
├── Cannot access super admin area
└── Document and record management

Admin (Level 1)
├── Can access: admin/, user areas
├── Cannot access super admin or sec admin areas
└── Basic administrative functions

User (Level 0)
├── Can access: user areas only
└── No administrative access
```

## Folder Structure

```
views/
├── super admin/
│   ├── auth_check.php          # Super admin access control
│   ├── dashboard.php           # Super admin dashboard
│   ├── user_management.php     # User management system
│   └── .htaccess              # Web server protection
├── sec admin/
│   ├── auth_check.php          # Secretary admin access control
│   ├── sadashboard.php         # Secretary admin dashboard
│   └── .htaccess              # Web server protection
├── admin/
│   ├── auth_check.php          # Admin access control
│   ├── adashboard.php          # Admin dashboard
│   └── .htaccess              # Web server protection
└── errors/
    ├── 403.php                # Access forbidden page
    └── 404.php                # Not found page
```

## Authentication System

### Core Files

- `includes/Auth.php` - Enhanced authentication class with role-based methods
- `process_login.php` - Updated login process with role-based redirects
- `database_setup.sql` - Updated database schema with multiple admin types

### Key Features

1. **Role-Based Authentication**
   - `hasRole($role)` - Check specific role
   - `hasAnyRole($roles)` - Check multiple roles
   - `hasMinimumRole($minimumRole)` - Check minimum privilege level
   - `isSuperAdmin()`, `isSecAdmin()`, `isAdmin()` - Convenience methods

2. **Automatic Redirects**
   - Users are automatically redirected to their appropriate dashboard after login
   - Super admins → `super admin/dashboard`
   - Secretary admins → `sec admin/sadashboard`
   - Admins → `admin/adashboard`
   - Users → `welcome`

3. **Access Control Middleware**
   - Each admin folder has an `auth_check.php` file
   - Validates user authentication and role permissions
   - Logs access attempts for security
   - Redirects unauthorized users

## Default Users

The system comes with three default admin accounts (password: `admin123`):

1. **Super Admin**
   - Email: `superadmin@caps.com`
   - Access: Full system control

2. **Secretary Admin**
   - Email: `secadmin@caps.com`
   - Access: Secretary admin and below

3. **Admin**
   - Email: `admin@caps.com`
   - Access: Admin and below

## Security Features

### 1. Multi-Layer Protection

- **Application Level**: PHP authentication checks
- **Web Server Level**: `.htaccess` files
- **Database Level**: Role-based permissions

### 2. Access Logging

All admin area access is logged with:
- User email
- Access timestamp
- Area accessed

### 3. Session Security

- Session-based authentication
- Automatic logout on session expiry
- Secure password hashing using `password_hash()`

### 4. Input Validation

- All user inputs are validated and sanitized
- SQL injection protection through prepared statements
- XSS protection through `htmlspecialchars()`

## Usage Examples

### Checking User Permissions

```php
require_once 'includes/Auth.php';
$auth = new Auth();

// Check if user is logged in
if (!$auth->check()) {
    header('Location: login');
    exit();
}

// Check specific role
if (!$auth->isSuperAdmin()) {
    $_SESSION['error'] = 'Access denied. Super Admin privileges required.';
    header('Location: welcome');
    exit();
}

// Check minimum role level
if (!$auth->hasMinimumRole('sec_admin')) {
    $_SESSION['error'] = 'Access denied. Secretary Admin privileges required.';
    header('Location: welcome');
    exit();
}
```

### Adding Access Control to New Pages

1. **Include the auth check at the top of your PHP file:**
```php
<?php
require_once __DIR__ . '/auth_check.php';
?>
```

2. **For custom access control, use the Auth class methods:**
```php
<?php
require_once __DIR__ . '/../../includes/Auth.php';
$auth = new Auth();

if (!$auth->hasMinimumRole('admin')) {
    $_SESSION['error'] = 'Access denied.';
    header('Location: ../../welcome');
    exit();
}
?>
```

## User Management

The Super Admin has access to a comprehensive user management system at:
`views/super admin/user_management.php`

### Features:
- Create new users with any role
- Edit existing user information
- Change user passwords
- Delete users (except self)
- View all system users

### User Types Available:
- **User** - Basic access
- **Admin** - Administrative access
- **Secretary Admin** - Document and record management
- **Super Admin** - Full system control

## Database Schema

The `users` table has been updated to support multiple admin types:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    usertype ENUM('user', 'admin', 'sec_admin', 'super_admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Error Handling

The system includes proper error handling:

1. **403 Forbidden** - When users try to access unauthorized areas
2. **404 Not Found** - For missing pages
3. **Session Errors** - Displayed to users for authentication issues
4. **Database Errors** - Logged for debugging

## Best Practices

1. **Always include auth checks** at the top of admin pages
2. **Use the minimum required role** for access control
3. **Log important actions** for security auditing
4. **Validate all inputs** before processing
5. **Use prepared statements** for database queries
6. **Keep passwords secure** using proper hashing
7. **Regular security audits** of user permissions

## Troubleshooting

### Common Issues:

1. **"Access denied" errors**
   - Check user role in database
   - Verify session is active
   - Ensure auth_check.php is included

2. **Login redirects to wrong dashboard**
   - Check user's usertype in database
   - Verify Auth class redirect logic

3. **Session issues**
   - Check PHP session configuration
   - Verify session_start() is called

### Debug Mode:

To enable debug mode, add this to your PHP files:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Security Recommendations

1. **Regular Updates**: Keep PHP and web server updated
2. **Strong Passwords**: Enforce strong password policies
3. **HTTPS**: Use SSL/TLS for all admin communications
4. **Backup**: Regular database and file backups
5. **Monitoring**: Monitor access logs for suspicious activity
6. **Training**: Train users on security best practices

## Support

For issues or questions about the access control system:
1. Check this documentation
2. Review error logs
3. Verify database configuration
4. Test with default admin accounts
