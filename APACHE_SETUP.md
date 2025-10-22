# Apache Configuration Files for GSP

This directory contains Apache virtual host configuration files for deploying the GameServerPanel and its associated services.

## Configuration Files

### 1. panel.conf
Configuration for the main Open Game Panel dashboard.
- **Domain**: panel.yourdomain.com
- **Document Root**: /var/www/GSP
- **Purpose**: Main panel interface for server management

### 2. website.conf
Configuration for the GameServers.World storefront website.
- **Domain**: gameservers.world
- **Document Root**: /var/www/GSP/_website
- **Purpose**: Customer-facing storefront for ordering game servers
- **Features**: 
  - Separate session handling
  - Protected includes and data directories
  - Static asset caching
  - Security headers

### 3. fileserver.conf
Configuration for the file server for game downloads.
- **Domain**: files.yourdomain.com
- **Document Root**: /var/www/fileserver
- **Purpose**: File distribution for game server installations
- **Features**:
  - Directory browsing enabled
  - Large file support
  - Script execution disabled in upload directories

## Installation Instructions

### 1. Copy Configuration Files

Copy the configuration files to Apache's sites-available directory:

```bash
# For Ubuntu/Debian
sudo cp panel.conf /etc/apache2/sites-available/
sudo cp website.conf /etc/apache2/sites-available/
sudo cp fileserver.conf /etc/apache2/sites-available/

# For CentOS/RHEL
sudo cp panel.conf /etc/httpd/conf.d/
sudo cp website.conf /etc/httpd/conf.d/
sudo cp fileserver.conf /etc/httpd/conf.d/
```

### 2. Update Configuration

Edit each configuration file to match your environment:

1. Replace `yourdomain.com` with your actual domain
2. Verify document root paths match your installation
3. Update SSL certificate paths (if using HTTPS)

```bash
sudo nano /etc/apache2/sites-available/panel.conf
sudo nano /etc/apache2/sites-available/website.conf
sudo nano /etc/apache2/sites-available/fileserver.conf
```

### 3. Enable Sites (Ubuntu/Debian)

```bash
sudo a2ensite panel.conf
sudo a2ensite website.conf
sudo a2ensite fileserver.conf
```

### 4. Enable Required Apache Modules

```bash
# Ubuntu/Debian
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod expires
sudo a2enmod deflate
sudo a2enmod ssl  # if using HTTPS

# CentOS/RHEL - most modules are enabled by default
# Check /etc/httpd/conf.modules.d/ for module configuration
```

### 5. Create File Server Directory

```bash
sudo mkdir -p /var/www/fileserver
sudo chown -R www-data:www-data /var/www/fileserver  # Ubuntu/Debian
# OR
sudo chown -R apache:apache /var/www/fileserver      # CentOS/RHEL
```

### 6. Test Configuration

```bash
# Ubuntu/Debian
sudo apache2ctl configtest

# CentOS/RHEL
sudo apachectl configtest
```

### 7. Restart Apache

```bash
# Ubuntu/Debian
sudo systemctl restart apache2

# CentOS/RHEL
sudo systemctl restart httpd
```

## SSL/HTTPS Configuration

Each configuration file includes commented-out HTTPS sections. To enable SSL:

1. Obtain SSL certificates (using Let's Encrypt, purchased certificates, etc.)
2. Uncomment the HTTPS VirtualHost sections
3. Update certificate paths
4. Enable SSL module (see step 4 above)
5. Restart Apache

### Using Let's Encrypt

```bash
# Install Certbot
sudo apt-get install certbot python3-certbot-apache  # Ubuntu/Debian
sudo yum install certbot python3-certbot-apache      # CentOS/RHEL

# Obtain certificates
sudo certbot --apache -d panel.yourdomain.com
sudo certbot --apache -d gameservers.world -d www.gameservers.world
sudo certbot --apache -d files.yourdomain.com

# Certbot will automatically update your Apache configuration
```

## DNS Configuration

Make sure your DNS records point to your server:

```
panel.yourdomain.com    A    YOUR_SERVER_IP
gameservers.world       A    YOUR_SERVER_IP
www.gameservers.world   A    YOUR_SERVER_IP
files.yourdomain.com    A    YOUR_SERVER_IP
```

## Firewall Configuration

Ensure ports 80 and 443 are open:

```bash
# UFW (Ubuntu)
sudo ufw allow 'Apache Full'

# firewalld (CentOS/RHEL)
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload

# iptables
sudo iptables -A INPUT -p tcp --dport 80 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 443 -j ACCEPT
```

## Troubleshooting

### Permission Issues

```bash
# Fix ownership
sudo chown -R www-data:www-data /var/www/GSP  # Ubuntu/Debian
sudo chown -R apache:apache /var/www/GSP      # CentOS/RHEL

# Fix permissions
sudo find /var/www/GSP -type d -exec chmod 755 {} \;
sudo find /var/www/GSP -type f -exec chmod 644 {} \;
```

### Check Logs

```bash
# Apache error logs
sudo tail -f /var/log/apache2/error.log           # Ubuntu/Debian
sudo tail -f /var/log/httpd/error_log             # CentOS/RHEL

# Site-specific logs
sudo tail -f /var/log/apache2/ogp-panel-error.log
sudo tail -f /var/log/apache2/gameservers-website-error.log
sudo tail -f /var/log/apache2/fileserver-error.log
```

### Test PHP

Create a test file:

```bash
echo "<?php phpinfo(); ?>" | sudo tee /var/www/GSP/info.php
```

Visit http://panel.yourdomain.com/info.php

**Important**: Delete this file after testing!

## Security Recommendations

1. **Always use HTTPS in production**
2. **Keep Apache and PHP updated**
3. **Configure firewall properly**
4. **Use strong passwords in database configurations**
5. **Regularly backup your data**
6. **Monitor logs for suspicious activity**
7. **Consider using fail2ban to prevent brute force attacks**
8. **Restrict access to sensitive directories**

## Support

For issues specific to:
- **Panel**: Check the main GSP documentation
- **Website**: Review _website/README.md and related documentation
- **Apache**: Consult Apache documentation at https://httpd.apache.org/docs/

## License

These configuration files are part of the Open Game Panel project and follow the same license as the main project.
