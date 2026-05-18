# Website Features Documentation

This document describes the new features added to the GameServers.World website (_website folder).

## Table of Contents

1. [Password Reset System](#password-reset-system)
2. [My Servers Dashboard](#my-servers-dashboard)
3. [Server Status Page](#server-status-page)
4. [UI Improvements](#ui-improvements)
5. [Apache Configuration](#apache-configuration)

---

## Password Reset System

A complete password reset workflow has been implemented to allow users to recover their accounts.

### Files Created

- **forgot_password.php** - Request password reset
- **reset_password.php** - Reset password with token

### How It Works

1. User visits the login page and clicks "Forgot Password?"
2. User enters their username or email on `forgot_password.php`
3. System generates a secure token and stores it in `ogp_password_reset_tokens` table
4. Email is sent with reset link (falls back to displaying link if email fails)
5. User clicks link and is taken to `reset_password.php?token=XXX`
6. User enters new password (min 8 characters)
7. Password is updated using both MD5 (panel compatibility) and modern hash (if shadow column exists)
8. Token is marked as used

### Database Table

The system automatically creates this table if it doesn't exist:

```sql
CREATE TABLE ogp_password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_user_id (user_id)
)
```

### Security Features

- Tokens expire after 1 hour
- Tokens can only be used once
- Secure random token generation (64 hex characters)
- Password requirements enforced (min 8 chars)
- Passwords hashed with both MD5 (panel) and bcrypt (modern)
- User enumeration protection (doesn't reveal if account exists)

### Email Configuration

The system uses PHP's `mail()` function. For production:

1. Configure your server's mail system (sendmail, postfix, etc.)
2. Or integrate with an email service (SendGrid, Mailgun, etc.)
3. Update the email headers in `forgot_password.php` as needed

---

## My Servers Dashboard

A user dashboard showing all active game servers with renewal options.

### File Created

- **my_servers.php** - User's server management dashboard
- **renew_server.php** - Server renewal page

### Features

- **Server List**: Shows all servers owned by logged-in user
- **Server Details**: Name, game type, location, status
- **Expiration Tracking**: Shows expiration date for each server
- **Status Indicators**: Active, Inactive, Expired
- **Renewal Links**: Quick access to renew each server
- **Empty State**: Helpful message when user has no servers

### Access

- Menu link "My Servers" appears when user is logged in
- Requires authentication via `login_required.php`

### Database Query

Joins multiple tables:
- `ogp_home` - Server instances
- `ogp_remote_servers` - Server locations
- `ogp_game_configs` - Game information
- `ogp_billing_orders` - Order/expiration data
- `ogp_billing_services` - Service pricing

---

## Server Status Page

Public page showing real-time status of all game server infrastructure.

### File Created

- **server_status.php** - Server infrastructure status

### Features

- **Real-time Status**: Online, Offline, Maintenance, Unknown
- **Resource Usage**: CPU, Memory, Disk usage percentages
- **Uptime Display**: How long each server has been running
- **Last Updated**: Time since last status update
- **Color-coded Badges**: Visual status indicators
- **Notes Support**: Display maintenance or status messages

### Database Table

Automatically creates table if it doesn't exist:

```sql
CREATE TABLE ogp_server_status (
    status_id INT AUTO_INCREMENT PRIMARY KEY,
    remote_server_id INT NOT NULL,
    server_name VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    status ENUM('online', 'offline', 'maintenance') DEFAULT 'offline',
    cpu_usage DECIMAL(5,2),
    memory_usage DECIMAL(5,2),
    disk_usage DECIMAL(5,2),
    uptime VARCHAR(50),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    notes TEXT,
    INDEX idx_remote_server (remote_server_id),
    UNIQUE KEY unique_server (remote_server_id)
)
```

### Server Updates

The page displays data from `ogp_server_status`. Servers should update this table:

```php
// Example server update code (run on each server periodically)
$stmt = $db->prepare("INSERT INTO ogp_server_status 
    (remote_server_id, server_name, ip_address, status, cpu_usage, memory_usage, disk_usage, uptime, notes)
    VALUES (?, ?, ?, 'online', ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE 
    status = VALUES(status),
    cpu_usage = VALUES(cpu_usage),
    memory_usage = VALUES(memory_usage),
    disk_usage = VALUES(disk_usage),
    uptime = VALUES(uptime),
    notes = VALUES(notes),
    last_updated = NOW()");
```

### Access

- Link in footer: "Server Status"
- Public page (no login required)

---

## UI Improvements

### Server List Page

**Before**: "Order Server" was a plain link  
**After**: Styled as a button with gradient background

```html
<a href="order.php?service_id=X" class="gsw-btn" 
   style="display:inline-block;padding:12px 24px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;text-decoration:none;border-radius:8px;font-weight:600;transition:transform 0.2s;">
   Order Now
</a>
```

### Order Page

**Fixed**: Game images now display correctly
- Changed from `src="<?php echo $img_url; ?>"` 
- To `src="../<?php echo $img_url; ?>"`
- Assumes images are stored relative to panel root

### Login Page

**Added**: "Forgot Password?" link next to Register link

### Navigation Menu

**Added**: "My Servers" link for logged-in users
- Only visible when user is authenticated
- Positioned between "Game Servers" and "Cart"

### Footer

**Added**: "Server Status" link
- Public access to infrastructure status
- Positioned in footer with other utility links

---

## Apache Configuration

Three Apache virtual host configuration files have been created in the GSP root directory.

### Files Created

- **panel.conf** - Panel dashboard configuration
- **website.conf** - Storefront website configuration  
- **fileserver.conf** - File server configuration
- **APACHE_SETUP.md** - Detailed installation guide

### panel.conf

Main Open Game Panel dashboard:
- Domain: panel.yourdomain.com
- Document Root: /var/www/GSP
- PHP settings optimized for panel operations
- Security headers enabled

### website.conf

GameServers.World storefront:
- Domain: gameservers.world
- Document Root: /var/www/GSP/_website
- Protected includes and data directories
- Static asset caching
- Compression enabled
- Separate session handling

### fileserver.conf

Game file distribution:
- Domain: files.yourdomain.com
- Document Root: /var/www/fileserver
- Directory browsing enabled
- Large file support
- Script execution disabled in uploads
- Bandwidth limiting support (optional)

### Installation

See `APACHE_SETUP.md` for complete installation instructions including:
- Copying configuration files
- Enabling sites and modules
- SSL/HTTPS setup with Let's Encrypt
- DNS configuration
- Firewall rules
- Troubleshooting

---

## Testing

### Password Reset

1. Visit `login.php`
2. Click "Forgot Password?"
3. Enter username or email
4. Check email or view on-screen link (development mode)
5. Click reset link
6. Enter new password (min 8 chars)
7. Confirm password matches
8. Submit and verify redirect to login

### My Servers

1. Login as a user with servers
2. Click "My Servers" in navigation
3. Verify all servers are listed
4. Check expiration dates
5. Click "Renew" on a server
6. Verify renewal page displays correctly

### Server Status

1. Visit footer link "Server Status"
2. Verify all remote servers are displayed
3. Check status badges (color coding)
4. Verify "Last Updated" formatting
5. Confirm public access (no login required)

### UI Changes

1. Visit `serverlist.php`
2. Verify "Order Now" displays as styled button
3. Click button to go to `order.php`
4. Verify game images display correctly
5. Check footer has "Server Status" link
6. Login and verify "My Servers" appears in menu

---

## Security Considerations

### Password Reset

- ✅ Tokens expire after 1 hour
- ✅ One-time use tokens
- ✅ Secure random generation
- ✅ User enumeration protection
- ✅ Password strength requirements
- ⚠️ Email delivery depends on server mail config

### My Servers

- ✅ Login required
- ✅ User can only see own servers
- ✅ SQL injection prevention with prepared statements
- ✅ XSS prevention with htmlspecialchars()

### Server Status

- ✅ Read-only public page
- ✅ No sensitive information exposed
- ✅ SQL injection prevention
- ℹ️ Server updates should be authenticated (implement separately)

### Apache Configs

- ✅ Security headers enabled
- ✅ Sensitive directories protected
- ✅ Directory listing disabled (except fileserver)
- ✅ HTTPS configurations ready
- ⚠️ Update domain names before deployment
- ⚠️ Configure SSL certificates for production

---

## Future Enhancements

### Password Reset
- Email template customization
- Integration with email service provider
- Rate limiting for reset requests
- SMS/2FA backup recovery

### My Servers
- Server control buttons (start/stop/restart)
- Real-time server metrics
- Configuration editor
- File manager integration
- Console access
- Backup/restore functionality

### Server Status
- Automated server monitoring agent
- Alert notifications
- Historical uptime graphs
- Incident history
- Scheduled maintenance display
- Status API for external monitoring

### General
- User profile management
- Invoice history
- Support ticket system
- Knowledge base integration
- Multi-language support
- Dark/light theme toggle

---

## Support

For issues or questions:

1. Check the main GSP documentation
2. Review Apache configuration in `APACHE_SETUP.md`
3. Check PHP error logs
4. Verify database connectivity
5. Ensure proper file permissions

## License

All new features follow the same license as the main Open Game Panel project.
