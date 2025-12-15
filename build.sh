#!/bin/bash
set -e

# Download and install Composer
if ! command -v composer &> /dev/null; then
    echo "Installing Composer..."
    EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"
    
    if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
        >&2 echo 'ERROR: Invalid composer installer checksum'
        rm composer-setup.php
        exit 1
    fi
    
    php composer-setup.php --quiet
    rm composer-setup.php
    mv composer.phar /usr/local/bin/composer || mv composer.phar ./composer
    chmod +x /usr/local/bin/composer 2>/dev/null || chmod +x ./composer
    export PATH="$PATH:$(pwd)"
fi

# Install PHP dependencies
echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Install Node dependencies and build
echo "Installing Node dependencies..."
npm install

echo "Building assets..."
npm run build

echo "Build complete!"
