# Website Login Implementation

## Overview
This implementation adds login functionality to the website that authenticates users against the panel's database (ogp_users table) while maintaining separate sessions for the website and panel.

## Files Created/Modified

### 1. `_website/login.php` (NEW)
- Full-featured login page with modern UI
- Authenticates against panel DB using MD5 password hashing (panel-compatible)
- Creates separate website session using `gameservers_website` session name
- Logs all login attempts via logger() function
- Session variables set:
  - `$_SESSION['website_user_id']` - User ID from ogp_users
  - `$_SESSION['website_username']` - Username
  - `$_SESSION['website_user_role']` - User role (admin, user, etc.)
  - `$_SESSION['website_user_email']` - User email
  - `$_SESSION['website_login_time']` - Timestamp of login

### 2. `_website/logout.php` (NEW)
- Cleanly destroys website session
- Logs logout events
- Redirects to homepage after logout
- Properly clears session cookies

### 3. `_website/index.php` (MODIFIED)
- Added session management at the top
- Added header with Login/Logout button and user greeting
- Shows "Welcome, [username]!" when logged in
- Maintains same visual design with added header

## Session Management

### Separate Sessions
- **Website Session**: `gameservers_website` (this implementation)
- **Panel Session**: `opengamepanel_web` (existing panel)

These sessions are completely separate - users can be logged into one without being logged into the other.

## Security Features

1. **SQL Injection Prevention**: Uses `mysqli_real_escape_string()` for input sanitization
2. **Password Hashing**: Compatible with panel's MD5 hashing (legacy but matches panel)
3. **Session Isolation**: Separate session name prevents conflicts with panel
4. **XSS Prevention**: Uses `htmlspecialchars()` for output escaping
5. **Logging**: All login/logout events are logged via logger() function

## Database Requirements

Requires connection to panel database with access to:
- `ogp_users` table (fields: user_id, users_login, users_passwd, users_role, users_email)
- Connection configured in `db.php`

## Usage

### For Users:
1. Visit `_website/login.php` to login
2. Enter panel credentials (username/password)
3. After successful login, redirected to homepage with session active
4. Click "Logout" button to end session

### For Developers:
Check if user is logged in:
```php
session_name("gameservers_website");
session_start();

if (isset($_SESSION['website_user_id']) && !empty($_SESSION['website_user_id'])) {
    // User is logged in
    $username = $_SESSION['website_username'];
    $user_id = $_SESSION['website_user_id'];
    $user_role = $_SESSION['website_user_role'];
}
```

## Future Enhancements (Optional)

1. **Password Hashing Upgrade**: Implement modern bcrypt/argon2 with transparent upgrade on login
2. **CSRF Protection**: Add CSRF tokens to login form
3. **Rate Limiting**: Add IP-based login attempt limiting (similar to panel's ban_list)
4. **Remember Me**: Add persistent login cookie option
5. **Password Reset**: Integrate with panel's password reset flow
6. **Two-Factor Auth**: Optional 2FA for enhanced security

## Testing

All files pass PHP syntax validation:
```bash
php -l _website/index.php
php -l _website/login.php
php -l _website/logout.php
```

## Alignment with Copilot Instructions

This implementation follows the no-code planning guidelines from `.github/copilot-instructions.md`:

✅ Website uses panel DB for authentication  
✅ Sessions remain separate (website ≠ panel)  
✅ Auth compatibility maintained (MD5 hash for panel users)  
✅ Minimal changes to existing code  
✅ Repository-first approach (reused existing db.php, logger function)  
✅ Security considerations (SQL injection prevention, session isolation)  

## Notes

- Login credentials are the same as panel login (same user table)
- Website session does not grant access to panel - separate login required
- Logger function from db.php creates logfile.txt for audit trail
