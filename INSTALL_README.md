# GSP / WDS Panel — Installer Guide

> **GSP is a heavily customized fork of OGP maintained by WDS.**

---

## 1. Quick Install

### 1.1 Install Ubuntu 24.04 dependencies

```bash
sudo apt update
sudo apt install apache2 mysql-client unzip tar screen sudo subversion git rsync \
    php8.3 php8.3-mysql php8.3-gd php8.3-curl php8.3-mbstring php8.3-zip \
    php8.3-xml php8.3-xmlrpc php-pear libapache2-mod-php8.3 -y
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 1.2 Set file permissions

```bash
sudo chown -R www-data:www-data /var/www/html/gsp
sudo chmod -R 755 /var/www/html/gsp
sudo chmod 664 /var/www/html/gsp/includes/config.inc.php
```

### 1.3 Create database

```sql
CREATE DATABASE panel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'localuser'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON panel.* TO 'localuser'@'localhost';
FLUSH PRIVILEGES;
```

### 1.4 Run the installer

1. Open `http://your-server/check.php` — review dependency status (no hard blockers).
2. Open `http://your-server/install.php` — follow the wizard.
3. Fill in database credentials (host, port, name, user, password, table prefix).
4. Default table prefix: `gsp_`
5. Click **Next** to install.

---

## 2. Dependency Check (`check.php`)

`check.php` is a standalone page that checks your server environment **before or after install**. It never blocks installation and can be loaded at any time.

Checks include:

| Category | What's checked |
|---|---|
| PHP Runtime | PHP version (≥ 8.3 recommended) |
| PHP Extensions | mysqli, curl, gd, mbstring, zip, xml, json, openssl, fileinfo, session, xmlrpc |
| PHP Libraries | PEAR |
| Filesystem | Writable paths (includes/, modules/, upload/, cache/, log/, temp/) |
| Linux Commands | unzip, tar, screen, sudo, subversion, git, rsync, mysql |
| Apache | mod_rewrite |
| Database | Optional live connectivity test if config.inc.php exists |

**Statuses:**
- ✅ **OK** — Requirement satisfied
- ⚠ **Warning** — Missing but non-fatal; installation can proceed
- ❌ **Missing** — Extension or binary not found
- ❓ **Unknown** — Cannot be determined (e.g. shell_exec disabled)

---

## 3. Installer Form Fields

| Field | Default | Description |
|---|---|---|
| Database Host | `localhost` | MySQL hostname or IP |
| Database Port | `3306` | MySQL TCP port |
| Database Name | _(empty)_ | Target database name |
| Database User | _(empty)_ | MySQL username |
| Database Password | _(empty)_ | MySQL password |
| Table Prefix | `gsp_` | Prefix for all panel tables |

**Generated `includes/config.inc.php`:**

```php
$db_host="HOST";
$db_port="3306";
$db_user="USER";
$db_pass="PASSWORD";
$db_name="DATABASE";
$table_prefix="gsp_";
$db_type="mysql";
```

---

## 4. Reinstall Flow

If you need to reinstall the panel (e.g. after a migration or reset):

1. Restore the full installer:
   ```bash
   cp install.php.bak install.php
   ```
   _Or_ open `install.php` in your browser — it will show a **Restore & Re-run Installer** button.

2. Navigate to `http://your-server/install.php` and follow the wizard again.

3. The installer will detect an existing database, back it up, then reinstall cleanly.

---

## 5. Backup Behavior

When the installer detects **existing tables** in the target database, it:

1. Displays a warning: _"Existing database detected. A backup will be created before reinstall."_
2. Creates a backup database named `panel_BAK`.
   - If `panel_BAK` already exists, a timestamped name is used: `panel_BAK_YYYYMMDD_HHMMSS`.
3. Copies schema + data for every table into the backup database.
4. Drops all tables from the target database.
5. Proceeds with a fresh install.

**To restore from backup:**

```sql
-- Example restore of a single table
INSERT INTO panel.gsp_users SELECT * FROM panel_BAK.gsp_users;

-- Or restore the full backup DB
mysqldump panel_BAK | mysql panel
```

---

## 6. Post-Install Security

After installation completes:

- `install.php` is automatically replaced with a **stub page** (the full installer is saved as `install.php.bak`).
- The stub prevents accidental re-runs and offers an admin action to restore the installer.
- **Change the default admin password immediately** — default credentials are `admin` / `admin`.
- Secure `includes/config.inc.php`:
  ```bash
  sudo chmod 640 includes/config.inc.php
  sudo chown www-data:www-data includes/config.inc.php
  ```

---

## 7. Table Prefix Migration (ogp_ → gsp_)

If you are migrating from an older OGP installation:

- The installer automatically renames `ogp_*` tables to `gsp_*` (or your chosen prefix) when they don't already exist.
- Tables that already exist under the new prefix are skipped safely.
- You can choose a custom prefix (e.g. `mypanel_`) in the installer form.

**Module SQL files** containing `ogp_` references are dynamically converted to the chosen prefix at import time.

---

## 8. Modules

All modules found in the `modules/` directory are automatically detected and installed. Module SQL files are imported with dynamic prefix substitution.

- `modulemanager` is installed first (prerequisite for all other modules).
- Prerequisite failures are treated as warnings, not hard errors.

---

## 9. Rollback

| Scenario | Action |
|---|---|
| Bad install, want fresh start | Restore DB from `panel_BAK`, restore installer via `cp install.php.bak install.php`, re-run |
| Config broken | Edit `includes/config.inc.php` manually or re-run installer |
| Installer stub needs removal | `cp install.php.bak install.php` |
| Modules failed to install | Re-run installer (it detects existing DB and backs up first) |

---

## 10. Security Notes

- Passwords are **never printed** in check.php or any installer output.
- All user input is escaped with `htmlspecialchars()` before rendering.
- The `install.php.bak` stub restore action is unprotected — remove `install.php` and `install.php.bak` once you no longer need them.
- The default admin password is stored as MD5 to match the legacy panel login system. Change it immediately.
