#!/bin/bash

# Check if directory parameter is provided
if [ -z "$1" ]; then
    echo "Please provide the plugin directory path"
    echo "Usage: $0 /path/to/plugin/directory"
    exit 1
fi

PLUGIN_DIR="$1"

# Check if directory exists
if [ ! -d "$PLUGIN_DIR" ]; then
    echo "Directory does not exist: $PLUGIN_DIR"
    exit 1
fi

# Navigate to plugin directory
cd "$PLUGIN_DIR" || exit

# Check if composer.json exists, if not create it
if [ ! -f "composer.json" ]; then
    echo "Creating composer.json..."
    cat > composer.json << EOL
{
    "require-dev": {
        "phpstan/phpstan": "^1.10",
        "szepeviktor/phpstan-wordpress": "^1.1",
        "php-stubs/wordpress-stubs": "^6.2"
    },
    "scripts": {
        "analyze": "phpstan analyze"
    }
}
EOL
fi

# Install composer if not already installed
if ! command -v composer &> /dev/null; then
    echo "Installing composer..."
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --quiet
    rm composer-setup.php
    mv composer.phar /usr/local/bin/composer
fi

# Install dependencies
echo "Installing PHPStan and WordPress stubs..."
composer install

# Create PHPStan configuration if it doesn't exist
if [ ! -f "phpstan.neon" ]; then
    echo "Creating phpstan.neon configuration..."
    cat > phpstan.neon << EOL
parameters:
    level: 5
    paths:
        - .
    excludePaths:
        - vendor/*
        - tests/*
    bootstrapFiles:
        - vendor/php-stubs/wordpress-stubs/wordpress-stubs.php
    ignoreErrors:
        - '#Function [a-zA-Z0-9\\_]+ not found\.#'
        - '#Class [a-zA-Z0-9\\_]+ not found\.#'
    checkMissingIterableValueType: false
includes:
    - vendor/szepeviktor/phpstan-wordpress/extension.neon
EOL
fi

# Create a sample analysis script
cat > analyze.sh << EOL
#!/bin/bash
./vendor/bin/phpstan analyze
EOL

chmod +x analyze.sh

echo "PHPStan setup completed successfully!"
echo "You can run the analysis using: ./analyze.sh"
echo "Or using: composer run-script analyze"
