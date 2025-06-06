#!/bin/bash

# dcBotHosting Setup Script for Ubuntu 24.04
# This script installs all dependencies and sets up the project

# Exit on error
set -e

# Function to check if a command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Update package lists
sudo apt update

# Install required packages
sudo apt install -y \
    git \
    curl \
    unzip \
    sqlite3 \
    libsqlite3-dev

# Install PHP and required extensions
sudo apt install -y \
    php8.3 \
    php8.3-cli \
    php8.3-common \
    php8.3-curl \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-zip \
    php8.3-bcmath \
    php8.3-sqlite3 \
    php8.3-gd \
    php8.3-intl \
    php8.3-fpm

# Configure PHP-FPM
echo "Configuring PHP-FPM..."
sudo systemctl enable php8.3-fpm
sudo systemctl start php8.3-fpm

# Install Composer if not already installed
if ! command_exists composer; then
    echo "Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
fi

# Install Node.js and npm if not already installed
if ! command_exists node; then
    echo "Installing Node.js and npm..."
    curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
    sudo apt install -y nodejs
fi

# Install Docker if not already installed
if ! command_exists docker; then
    echo "Installing Docker..."
    sudo apt install -y docker.io
    sudo systemctl enable --now docker
    sudo usermod -aG docker $USER
    echo "Added current user to docker group. You may need to log out and back in for this to take effect."
fi

# Install Caddy if not already installed
if ! command_exists caddy; then
    echo "Installing Caddy..."
    sudo apt install -y debian-keyring debian-archive-keyring apt-transport-https
    curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | sudo gpg --dearmor -o /usr/share/keyrings/caddy-stable-archive-keyring.gpg
    curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | sudo tee /etc/apt/sources.list.d/caddy-stable.list
    sudo apt update
    sudo apt install -y caddy
fi

# Clone the repository (if not already in the repository directory)
if [ "$1" != "--skip-clone" ]; then
    echo "Cloning repository..."
    git clone https://github.com/FemRene/dcBotHosting.git
    cd dcBotHosting
fi

# Install PHP dependencies
echo "Installing PHP dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# Install JavaScript dependencies
echo "Installing JavaScript dependencies..."
npm install
npm install vite --save-dev
chmod +x node_modules/@esbuild/linux-x64/bin/esbuild

# Set up environment file
echo "Setting up environment file..."
if [ ! -f .env ]; then
    cp .env.example .env

    # Generate application key
    php artisan key:generate
fi

# Create database directory if it doesn't exist
if [ ! -d database ]; then
    mkdir -p database
fi

# Create SQLite database if it doesn't exist
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Build assets
echo "Building assets..."
npm run build

# Set appropriate permissions
echo "Setting permissions..."
sudo chown -R $USER:$USER .
sudo find . -type f -exec chmod 644 {} \;
sudo find . -type d -exec chmod 755 {} \;
sudo chmod -R 775 storage bootstrap/cache

# Create storage symlink
echo "Creating storage symlink..."
php artisan storage:link

# Clear caches
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Configure Caddy
echo "Configuring Caddy..."
CADDY_CONFIG_DIR="/etc/caddy"
SITE_ROOT=$(pwd)
SITE_DOMAIN=${SITE_DOMAIN:-"localhost"}

# Create Caddyfile
sudo tee $CADDY_CONFIG_DIR/Caddyfile > /dev/null << EOF
$SITE_DOMAIN {
    root * $SITE_ROOT/public
    php_fastcgi unix//run/php/php8.3-fpm.sock
    file_server
    encode gzip
    log {
        output file /var/log/caddy/$SITE_DOMAIN-access.log
    }
}
EOF

# Create log directory for Caddy
sudo mkdir -p /var/log/caddy
sudo chown -R caddy:caddy /var/log/caddy

# Restart Caddy to apply changes
sudo systemctl restart caddy

# Build for production
echo "Building for production..."
composer prod build

echo "Setup complete!"
echo ""
echo "Your Laravel application is now set up with Caddy as the web server."
echo "You can access it at: http://$SITE_DOMAIN"
echo ""
echo "For development mode with hot reloading, run:"
echo "composer dev"
echo ""
echo "For production builds, run:"
echo "composer prod build"
echo ""
echo "To use the Discord authentication, make sure to update your .env file with your Discord credentials:"
echo "DISCORD_CLIENT_ID=your-discord-client-id"
echo "DISCORD_CLIENT_SECRET=your-discord-client-secret"
echo "DISCORD_REDIRECT_URI=https://your-app-url/auth/discord/callback"
