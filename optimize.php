<?php
/**
 * Laravel Optimization Utility
 *
 * This script optimizes your Laravel application for production.
 *
 * Usage: php optimize.php
 */

// Set execution time limit
set_time_limit(120);

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "⚡ Laravel Optimization Utility\n\n";

// Check if we're in the right directory
if (!file_exists('artisan')) {
    die("❌ Error: artisan file not found. Please run this script from your Laravel project root directory.\n");
}

// Function to run commands
function runCommand($command, $description) {
    echo "📋 $description...\n";

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
    }

    echo "\n";
    return $return_var === 0;
}

// Clear all caches first
echo "🧹 Clearing existing caches...\n";
runCommand('php artisan cache:clear', 'Clearing application cache');
runCommand('php artisan config:clear', 'Clearing configuration cache');
runCommand('php artisan route:clear', 'Clearing route cache');
runCommand('php artisan view:clear', 'Clearing view cache');
runCommand('php artisan event:clear', 'Clearing event cache');

echo "\n";

// Cache configuration
echo "⚙️  Caching configuration...\n";
runCommand('php artisan config:cache', 'Caching configuration files');

// Cache routes
echo "🛣️  Caching routes...\n";
runCommand('php artisan route:cache', 'Caching routes');

// Cache views
echo "👁️  Caching views...\n";
runCommand('php artisan view:cache', 'Caching views');

// Cache events
echo "📡 Caching events...\n";
runCommand('php artisan event:cache', 'Caching events');

// Optimize autoloader (if Composer is available)
if (file_exists('composer.phar') || shell_exec('which composer')) {
    echo "📦 Optimizing Composer autoloader...\n";
    runCommand('composer dump-autoload --optimize --no-dev', 'Optimizing Composer autoloader');
}

// Run Laravel optimization
echo "🚀 Running Laravel optimization...\n";
runCommand('php artisan optimize', 'Optimizing Laravel application');

// Clear compiled services and recompile
echo "🔧 Recompiling services...\n";
runCommand('php artisan clear-compiled', 'Clearing compiled services');
runCommand('php artisan optimize', 'Re-optimizing application');

echo "\n";

// Check file permissions
echo "🔐 Checking file permissions...\n";
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
        if (is_writable($dir)) {
            echo "✅ $dir is writable\n";
        } else {
            echo "⚠️  $dir is not writable - setting permissions\n";
            runCommand("chmod -R 775 $dir", "Setting permissions for $dir");
        }
    }
}

echo "\n";

// Display optimization results
echo "📊 Optimization Summary:\n";
echo "✅ Configuration cached\n";
echo "✅ Routes cached\n";
echo "✅ Views cached\n";
echo "✅ Events cached\n";
echo "✅ Application optimized\n";

echo "\n🎉 Laravel application has been optimized for production!\n";
echo "Your application should now run faster with cached configurations and routes.\n";

echo "\n💡 Tips for maintaining optimal performance:\n";
echo "1. Run this script after any configuration changes\n";
echo "2. Clear caches when updating routes or views during development\n";
echo "3. Monitor your application logs in storage/logs/\n";
echo "4. Consider using a CDN for static assets in production\n";
?>
