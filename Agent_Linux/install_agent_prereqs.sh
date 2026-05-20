#!/bin/bash
# OGP Agent Prerequisites Installer for Ubuntu 22.04+
# This script installs all required packages for running OGP Agent
# Usage: sudo bash install_agent_prereqs.sh

set -e

if [ "$(id -u)" -ne 0 ]; then
  echo "This script must be run as root (use sudo)"
  exit 1
fi

# Update package lists
apt-get update

# Install core required packages from original prerequisites
apt-get install -y \
  libxml-parser-perl \
  libpath-class-perl \
  perl-modules \
  screen \
  rsync \
  sudo \
  e2fsprogs \
  unzip \
  subversion \
  libarchive-extract-perl \
  pure-ftpd \
  libarchive-zip-perl \
  libc6 \
  libgcc1 \
  git \
  curl \
  libhttp-daemon-perl

# Install 32-bit compatibility libraries (may fail on some systems, continue anyway)
apt-get install -y libc6-i386 || echo "Warning: Could not install libc6-i386"
apt-get install -y libgcc1:i386 || echo "Warning: Could not install libgcc1:i386" 
apt-get install -y lib32gcc1 || echo "Warning: Could not install lib32gcc1"

# Install additional modern packages for current OGP agent
apt-get install -y \
  libdbi-perl \
  libdbd-mysql-perl \
  libfrontier-rpc-perl \
  libfile-copy-recursive-perl \
  libcrypt-xxtea-perl \
  libschedule-cron-perl \
  libmime-base64-perl \
  libgetopt-long-descriptive-perl \
  libio-compress-perl \
  libcompress-raw-zlib-perl \
  libfile-find-rule-perl \
  libfile-basename-perl \
  libfcgi-perl \
  libwww-perl

# Optional: For FTP management (pure-ftpd already installed above)
# apt-get install -y proftpd-basic

# Optional: For web panel integration
apt-get install -y apache2 php php-mysql || echo "Warning: Optional web packages could not be installed"

# Done

echo "All required packages for OGP Agent have been installed."
echo "Note: Some 32-bit compatibility libraries may not be available on all systems."
echo "This is normal for modern 64-bit only distributions."
