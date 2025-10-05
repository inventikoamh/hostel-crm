<?php
/**
 * Laravel Hostel CRM Deployment Script - Command Line Version
 *
 * This script automates the deployment process for Laravel applications on shared hosting.
 * Run this file directly on your server after uploading your code.
 *
 * Usage: php deploy-cli.php
 */

// Set execution time limit
set_time_limit(300); // 5 minutes

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🚀 Starting Laravel Hostel CRM Deployment...\n\n";

// Check if we're in the right directory
if (!file_exists('../artisan')) {
    die("❌ Error: artisan file not found. Please run this script from your Laravel project root directory.\n");
}

// Change to parent directory (Laravel root)
chdir('..');

// Function to run commands
function runCommand($command, $description) {
    echo "📋 $description...\n";
    echo "Command: $command\n";

    $output = [];
    $return_var = 0;

    exec($command . ' 2>&1', $output, $return_var);

    if ($return_var === 0) {
        echo "✅ Success!\n";
        if (!empty($output)) {
            echo "Output: " . implode("\n", $output) . "\n";
        }
    } else {
        echo "❌ Error occurred!\n";
        echo "Output: " . implode("\n", $output) . "\n";
        echo "Return code: $return_var\n";
    }

    echo "\n";
    return $return_var === 0;
}

// Function to check if command exists
function commandExists($command) {
    $output = [];
    $return_var = 0;
    exec("which $command 2>/dev/null", $output, $return_var);
    return $return_var === 0;
}

// Check PHP version
echo "🔍 Checking PHP version...\n";
$phpVersion = phpversion();
echo "PHP Version: $phpVersion\n";

if (version_compare($phpVersion, '8.1.0', '<')) {
    echo "⚠️  Warning: PHP 8.1+ is recommended. Current version: $phpVersion\n";
}

echo "\n";

// Check required extensions
echo "🔍 Checking required PHP extensions...\n";
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
$missingExtensions = [];

foreach ($requiredExtensions as $extension) {
    if (!extension_loaded($extension)) {
        $missingExtensions[] = $extension;
    }
}

if (!empty($missingExtensions)) {
    echo "❌ Missing required extensions: " . implode(', ', $missingExtensions) . "\n";
    echo "Please contact your hosting provider to enable these extensions.\n";
    exit(1);
} else {
    echo "✅ All required extensions are available.\n";
}

echo "\n";

// Check if Composer is available
if (commandExists('composer')) {
    echo "📦 Installing/Updating Composer dependencies...\n";
    if (!runCommand('composer install --no-dev --optimize-autoloader', 'Installing production dependencies')) {
        echo "⚠️  Composer install failed. Trying without optimization...\n";
        runCommand('composer install --no-dev', 'Installing dependencies without optimization');
    }
} else {
    echo "⚠️  Composer not found. Please ensure Composer is installed or upload vendor directory.\n";
}

echo "\n";

// Set proper permissions
echo "🔐 Setting file permissions...\n";
$directories = [
    'storage',
    'storage/app',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        runCommand("chmod -R 775 $dir", "Setting permissions for $dir");
    }
}

echo "\n";

// Copy environment file if it doesn't exist
if (!file_exists('.env')) {
    echo "📄 Creating .env file from .env.example...\n";
    if (file_exists('.env.example')) {
        if (copy('.env.example', '.env')) {
            echo "✅ .env file created successfully.\n";
            echo "⚠️  Please update your .env file with your database credentials and other settings.\n";
        } else {
            echo "❌ Failed to create .env file.\n";
        }
    } else {
        echo "❌ .env.example file not found.\n";
    }
} else {
    echo "✅ .env file already exists.\n";
}

echo "\n";

// Generate application key if not set
echo "🔑 Checking application key...\n";
$envContent = file_get_contents('.env');
if (strpos($envContent, 'APP_KEY=') === false || strpos($envContent, 'APP_KEY=base64:') === false) {
    runCommand('php artisan key:generate', 'Generating application key');
} else {
    echo "✅ Application key is already set.\n";
}

echo "\n";

// Clear and cache configuration
echo "🧹 Clearing and caching configuration...\n";
runCommand('php artisan config:clear', 'Clearing configuration cache');
runCommand('php artisan config:cache', 'Caching configuration');

echo "\n";

// Clear and cache routes
echo "🛣️  Clearing and caching routes...\n";
runCommand('php artisan route:clear', 'Clearing route cache');
runCommand('php artisan route:cache', 'Caching routes');

echo "\n";

// Clear and cache views
echo "👁️  Clearing and caching views...\n";
runCommand('php artisan view:clear', 'Clearing view cache');
runCommand('php artisan view:cache', 'Caching views');

echo "\n";

// Clear and cache events
echo "📡 Clearing and caching events...\n";
runCommand('php artisan event:clear', 'Clearing event cache');
runCommand('php artisan event:cache', 'Caching events');

echo "\n";

// Run database migrations
echo "🗄️  Running database migrations...\n";
echo "⚠️  Make sure your database is configured in .env file before proceeding.\n";
echo "Do you want to run migrations? (y/n): ";

$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim(strtolower($line)) === 'y' || trim(strtolower($line)) === 'yes') {
    runCommand('php artisan migrate --force', 'Running database migrations');

    echo "Do you want to run seeders? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);

    if (trim(strtolower($line)) === 'y' || trim(strtolower($line)) === 'yes') {
        runCommand('php artisan db:seed --force', 'Running database seeders');
    }
} else {
    echo "⏭️  Skipping migrations. You can run them later using: php artisan migrate\n";
}

echo "\n";

// Create storage link
echo "🔗 Creating storage link...\n";
runCommand('php artisan storage:link', 'Creating symbolic link for storage');

echo "\n";

// Optimize for production
echo "⚡ Optimizing for production...\n";
runCommand('php artisan optimize', 'Optimizing application');

echo "\n";

// Final checks
echo "🔍 Running final checks...\n";

// Check if storage is writable
if (is_writable('storage')) {
    echo "✅ Storage directory is writable.\n";
} else {
    echo "❌ Storage directory is not writable. Please check permissions.\n";
}

// Check if bootstrap/cache is writable
if (is_writable('bootstrap/cache')) {
    echo "✅ Bootstrap cache directory is writable.\n";
} else {
    echo "❌ Bootstrap cache directory is not writable. Please check permissions.\n";
}

echo "\n";

// Display important information
echo "🎉 Deployment completed!\n\n";
echo "📋 Important Information:\n";
echo "1. Make sure your .env file is properly configured with your database credentials.\n";
echo "2. Ensure your web server document root points to the 'public' directory.\n";
echo "3. Check that all file permissions are set correctly (755 for directories, 644 for files).\n";
echo "4. Storage and bootstrap/cache directories should be writable (775).\n";
echo "5. If you encounter any issues, check the storage/logs/laravel.log file.\n\n";

echo "🛠️  Useful commands for maintenance:\n";
echo "- Clear all caches: php clear-cache.php\n";
echo "- Run migrations: php run-migrations.php\n";
echo "- Run migrations with seeders: php run-migrations.php --seed\n";
echo "- Optimize application: php optimize.php\n\n";

echo "🌐 Your Laravel application should now be accessible!\n";
echo "If you're using shared hosting, make sure your domain points to the 'public' directory.\n";
?>
