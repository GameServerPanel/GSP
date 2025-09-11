# GameServerPanel (GSP) - Unstable Branch

This is the **GameServerPanel (GSP)** project, a cross-platform game server management panel and agent system.

This README covers installation and setup for:
- **The Web Panel**
- **The Linux Agent**
- **The Windows Agent**

---

## Table of Contents

- [Prerequisites](#prerequisites)
- [Panel Installation (Linux)](#panel-installation-linux)
- [Agent Installation (Linux)](#agent-installation-linux)
- [Agent Installation (Windows)](#agent-installation-windows)
- [Example Installation Scripts](#example-installation-scripts)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)

---

## Prerequisites

### General

- **Git**
- **curl/wget**
- **Sudo/root privileges**

### Panel (Linux)

- **Ubuntu 22.04+** (other distros may work)
- **Apache2** or **nginx**
- **PHP 8.1+**
- **MariaDB** or **MySQL**
- **Required PHP extensions**:  
  `php-mysqli php-json php-curl php-xml php-zip php-gd php-mbstring php-bcmath php-intl php-pdo php-cli`
- **Composer** (for PHP dependency management)

### Linux Agent

- **Ubuntu 22.04+** (other distros may work)
- **Perl 5**
- **Perl modules**:  
  `Proc::ProcessTable`, `IO::Socket`, `DBI`, `Sys::Hostname`, `LWP::UserAgent`, `JSON`, `File::Path`
- **Screen** (for game server management)

### Windows Agent

- **Windows 10/11/Server**
- **Strawberry Perl** (or ActivePerl)
- **Perl modules**:  
  Same as Linux agent (`Proc::ProcessTable`, etc.)
- **Run agent as Administrator** for full functionality

---

## Panel Installation (Linux)

1. **Install prerequisites:**

    ```bash
    sudo apt update
    sudo apt install apache2 mariadb-server php php-mysqli php-json php-curl php-xml php-zip php-gd php-mbstring php-bcmath php-intl php-pdo php-cli composer git unzip
    ```

2. **Clone the repository:**

    ```bash
    git clone -b unstable https://github.com/GameServerPanel/GSP.git
    cd GSP/ControlPanel
    ```

3. **Install PHP dependencies:**

    ```bash
    composer install
    ```

4. **Set permissions:**

    ```bash
    sudo chown -R www-data:www-data .
    sudo chmod -R 755 .
    ```

5. **Configure Apache/nginx:**
   - Point your DocumentRoot to `GSP/ControlPanel/public`
   - Restart your webserver.

6. **Configure the Panel:**
   - Open the panel in your browser
   - Follow setup wizard (enter database details, admin account, etc.)

---

## Agent Installation (Linux)

1. **Install prerequisites:**

    ```bash
    sudo apt update
    sudo apt install perl build-essential screen libproc-processtable-perl libio-socket-perl libdbi-perl libsys-hostname-perl libwww-perl libjson-perl libfile-path-perl
    ```

2. **Clone the agent:**

    ```bash
    git clone -b unstable https://github.com/GameServerPanel/GSP.git
    cd GSP/Agent
    ```

3. **Run the agent:**

    ```bash
    perl ogp_agent.pl
    ```

> If `libproc-process-table-perl` is not available on your distro, install via CPAN:
>
> ```bash
> sudo apt install cpanminus
> sudo cpanm Proc::ProcessTable
> ```

---

## Agent Installation (Windows)

1. **Install [Strawberry Perl](https://strawberryperl.com/)**
2. **Open Command Prompt as Administrator**

3. **Install required Perl modules:**

    ```shell
    cpan install Proc::ProcessTable IO::Socket DBI Sys::Hostname LWP::UserAgent JSON File::Path
    ```

4. **Clone the repository or download the Agent folder**
5. **Run the agent:**

    ```shell
    perl ogp_agent.pl
    ```

---

## Example Installation Scripts

### Linux Agent Quick Install

```bash
#!/bin/bash
sudo apt update
sudo apt install -y perl build-essential screen git \
    libproc-processtable-perl libio-socket-perl libdbi-perl \
    libsys-hostname-perl libwww-perl libjson-perl libfile-path-perl
git clone -b unstable https://github.com/GameServerPanel/GSP.git
cd GSP/Agent
perl ogp_agent.pl
```

### Panel Quick Install

```bash
#!/bin/bash
sudo apt update
sudo apt install -y apache2 mariadb-server php php-mysqli php-json php-curl php-xml php-zip php-gd php-mbstring php-bcmath php-intl php-pdo php-cli composer git unzip
git clone -b unstable https://github.com/GameServerPanel/GSP.git
cd GSP/ControlPanel
composer install
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
# Configure your web server to point to GSP/ControlPanel/public
```

---

## Troubleshooting

- **Missing Perl modules:**  
  Use `cpanm` or `cpan` to install missing modules.

- **libproc-process-table-perl not found:**  
  Install via CPAN (`cpanm Proc::ProcessTable`)

- **Web panel not loading:**  
  Check Apache/nginx logs, file permissions, and PHP modules.

- **Agent cannot communicate with panel:**  
  Check firewall settings and agent config.

---

## Contributing

- Fork the repository
- Create a feature branch
- Make your changes
- Submit a pull request

---

## License

See `LICENSE` file in the repository.

---

**For detailed help or bug reports, open an issue on [GitHub](https://github.com/GameServerPanel/GSP/issues).**