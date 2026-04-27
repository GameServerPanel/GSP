# GSP Installer – Differences from Original OGP `install.php`

## Overview

`install.php` in this repository is a customized installer for the
**GSP (Game Server Panel)** maintained by WDS. It is based on the original
OGP installer at
<https://github.com/OpenGamePanel/OGP-Website/blob/master/install.php>
but has been adapted for the GSP/WDS environment.

---

## Key differences

### 1. Default table prefix: `gsp_`

The original OGP installer defaults to `ogp_`. Our installer defaults to
`gsp_` (the `$table_prefix` form field pre-fills with `gsp_`). You may
change this during installation.

### 2. Config file now includes `$db_port`

The generated `includes/config.inc.php` includes a `$db_port` variable:

```php
$db_host="HOST";
$db_port="3306";
$db_user="USER";
$db_pass="PASSWORD";
$db_name="DATABASE";
$table_prefix="gsp_";
$db_type="mysql";
```

Existing config files that predate this installer and lack `$db_port`
continue to work because the parameter defaults to `NULL` (MySQL default
port 3306).

### 3. MySQLi connection uses the port

`includes/database_mysqli.php` – `OGPDatabaseMySQL::connect()` – now
accepts an optional `$db_port` argument and passes it to `mysqli_connect()`:

```php
$this->link = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $port);
```

`includes/helpers.php` – `createDatabaseConnection()` – likewise accepts
and forwards `$db_port`.

All panel entry points (`home.php`, `index.php`, `ogp_api.php`,
`server_status.php`, `modules/billing/cron-shop.php`,
`modules/billing/includes/panel_bridge.php`) pass
`isset($db_port) ? $db_port : NULL` when calling
`createDatabaseConnection()`.

### 4. Default admin account created automatically

After modules are installed, the installer automatically creates an `admin`
account with password `admin` using the existing `OGPDatabaseMySQL::addUser()`
method (which stores passwords as `MD5`). If an admin user already exists it
is **not** duplicated.

**Change the default password immediately after your first login.**

### 5. No prerequisite checks

Step 0 shows a welcome screen and language selector only. The original OGP
installer checks for required PHP extensions, Pear, etc. GSP skips those
checks because the deployment environment is pre-validated by our bootstrap
scripts.

### 6. All modules installed automatically

Every directory found under `modules/` is installed via
`install_module()`. Prerequisite failures for individual modules are treated
as warnings (not hard failures) so that the overall installation succeeds
even if optional dependencies are missing.

### 7. `ogp_` → `gsp_` table migration (optional, safe)

If the database already contains tables prefixed with `ogp_`:

* For each `ogp_X` table, if the corresponding `gsp_X` table does **not**
  exist, it is renamed to `gsp_X`.
* If `gsp_X` already exists, the rename is skipped silently.
* The installer never aborts due to pre-existing tables.

This allows upgrading an existing OGP installation to GSP without losing data.

### 8. Branding

The installer title and default site settings reference **GSP – Game Server
Panel** and **WDS** instead of "Open Game Panel".

---

## Running the installer

1. Upload/deploy the panel files.
2. Ensure `includes/config.inc.php` is writable (or does not exist yet).
3. Open `https://yoursite/install.php` in a browser.
4. Select your language and click **Next**.
5. Enter database credentials and click **Next**.
6. The installer writes config, migrates tables (if needed), installs
   modules, and creates the default admin account.
7. **Delete `install.php`** from the server after installation.
8. Optionally `chmod 644 includes/config.inc.php` for security.

---

## Rollback

* To re-run the installer, simply navigate to `install.php` again.
* If the wrong prefix was chosen, edit `includes/config.inc.php` manually or
  re-run `install.php`.
* Renamed (`ogp_` → `gsp_`) tables can be manually renamed back with
  `RENAME TABLE gsp_X TO ogp_X` in MySQL.
