# Open Game Panel Development Guide

## Understanding the Codebase

This guide helps developers understand and work with the Open Game Panel codebase effectively.

## Component Integration

### How the Web Panel and Agents Work Together

1. **Agent Registration**
   - Agents are registered through the web panel's administration interface
   - Each agent requires: IP address, port, encryption key, connection timeout
   - Agent connectivity is tested during registration

2. **Command Flow**
   ```
   User Action (Web UI) → Web Panel → Encrypted Command → Agent → Game Server
                                  ← Encrypted Response ←       ←
   ```

3. **Data Synchronization**
   - Web panel maintains server state in MySQL database
   - Agents report status updates to panel
   - File changes are synchronized between panel and agents

### Key Integration Points

**Game Server Management**:
- `modules/server/server.php` - Server control interface
- `includes/lib_remote.php` - Agent communication
- Database tables: `ogp_servers`, `ogp_games`, `ogp_user_games`

**File Management**:
- `modules/ftp/` - Web-based file manager
- `modules/litefm/` - Alternative file manager
- Agents handle actual file operations on remote systems

**Configuration Management**:
- `modules/editconfigfiles/` - Config file editor
- `modules/gamemanager/` - Game templates and settings
- Agents apply configurations to game servers

## Database Schema Overview

### Core Tables

**ogp_servers** - Game server instances
```sql
- server_id (Primary Key)
- agent_id (Foreign Key to ogp_agents)
- user_id (Foreign Key to ogp_users)
- game_id (Foreign Key to ogp_games)
- server_name, ip, port, home_path, etc.
```

**ogp_agents** - Remote agent machines
```sql
- agent_id (Primary Key)
- agent_name, agent_ip, agent_port
- encryption_key, timeout
- os_type (linux/windows)
```

**ogp_games** - Game definitions
```sql
- game_id (Primary Key)
- game_name, installer_name
- cfg_template, startup_cmd
- platform (linux/windows/both)
```

**ogp_users** - Panel users
```sql
- user_id (Primary Key)
- username, password_hash
- user_email, access_rights
```

## Agent Communication Deep Dive

### Encryption Protocol
```php
// lib_remote.php - Example encryption
$enc = new Crypt_XXTEA();
$enc->setKey($encryption_key);
$encrypted_data = $enc->encrypt($command_json);
```

### Command Structure
```json
{
  "action": "server_action",
  "parameters": {
    "home_id": "123",
    "action": "start"
  }
}
```

### Response Format
```json
{
  "status": "success|error",
  "message": "Operation completed",
  "data": {...}
}
```

## Module Development

### Creating a New Module

1. **Directory Structure**
   ```
   modules/mymodule/
   ├── mymodule.php        # Main module file
   ├── install.xml         # Installation metadata
   └── lang/              # Language files
       └── English/
           └── modules/
               └── mymodule.php
   ```

2. **Module Template**
   ```php
   <?php
   // modules/mymodule/mymodule.php
   
   // Security check
   if (!defined('OGP_LANG')) {
       exit('Direct access not allowed');
   }
   
   // Module initialization
   function exec_ogp_module() {
       global $db, $view;
       
       // Module logic here
   }
   ?>
   ```

3. **Language File**
   ```php
   <?php
   // lang/English/modules/mymodule.php
   define('OGP_LANG_mymodule_title', 'My Module');
   define('OGP_LANG_mymodule_description', 'Module description');
   ?>
   ```

### Module Registration
Modules are auto-discovered from the `modules/` directory and registered in the database.

## Common Development Tasks

### Adding a New Game
1. Create game definition in `modules/gamemanager/`
2. Add server configuration templates
3. Test installation and startup procedures
4. Add game-specific configuration files

### Extending the API
1. Add new endpoints to `ogp_api.php`
2. Implement authentication checks
3. Add request validation
4. Document API changes

### Creating Custom Themes
1. Copy existing theme from `themes/`
2. Modify CSS and template files
3. Test responsive design
4. Add theme metadata

## Debugging Guide

### Web Panel Debugging

**Enable Debug Mode**:
```php
// index.php - Change error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**Database Queries**:
```php
// Add to functions.php for query debugging
$db->debug = true;
```

**Agent Communication**:
```php
// lib_remote.php - Add debugging
echo "Sending command: " . $command . "\n";
echo "Response: " . $response . "\n";
```

### Common Issues

**Agent Connection Failures**:
- Check firewall settings on agent machine
- Verify encryption key matches
- Test network connectivity
- Check agent process is running

**Game Server Won't Start**:
- Verify game files are installed correctly
- Check server configuration files
- Review agent logs for errors
- Validate file permissions

**Database Errors**:
- Check database credentials in config.inc.php
- Verify table structure is up to date
- Review MySQL error logs
- Test database connectivity

## Testing Procedures

### Manual Testing Checklist

**Panel Functionality**:
- [ ] User login/logout
- [ ] Server creation/deletion  
- [ ] Agent management
- [ ] File operations
- [ ] Game installation

**Agent Communication**:
- [ ] Server start/stop/restart
- [ ] File upload/download
- [ ] Configuration updates
- [ ] Log file access

**API Testing**:
- [ ] Token creation/validation
- [ ] Server control endpoints
- [ ] Error handling
- [ ] Rate limiting

## Deployment Considerations

### Production Setup
- Use production-ready web server (Apache/Nginx)
- Configure SSL/TLS for encrypted web access
- Set up database with proper user permissions
- Configure firewall rules for agent communication
- Implement backup procedures

### Security Hardening
- Change default database credentials
- Use strong encryption keys for agents
- Implement IP whitelisting for admin access
- Regular security updates
- Monitor access logs

## Performance Optimization

### Database Optimization
- Add indexes for frequently queried columns
- Optimize slow queries
- Consider read replicas for large deployments
- Regular database maintenance

### Caching Strategies
- Implement Redis/Memcached for session storage
- Cache game configurations
- Optimize file operations
- Use CDN for static assets

## Contributing Guidelines

### Code Standards
- Follow existing code style
- Add comments for complex logic
- Validate all user input
- Use prepared statements for database queries
- Test changes thoroughly

### Pull Request Process
1. Create feature branch from main
2. Implement changes with tests
3. Update documentation
4. Submit pull request with description
5. Address review feedback