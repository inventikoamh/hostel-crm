<?php
/**
 * Laravel Cache Clearing Utility
 *
 * This script clears all Laravel caches for maintenance purposes.
 *
 * Usage: php clear-cache.php
 */

// Set execution time limit
set_time_limit(60);

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🧹 Laravel Cache Clearing Utility\n\n";

// Check if we're in the right directory
if (!file_exists('../artisan')) {
    die("❌ Error: artisan file not found. Please run this script from your Laravel project root directory.\n");
}

// Change to parent directory (Laravel root)
chdir('..');

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

// Clear application cache
runCommand('php artisan cache:clear', 'Clearing application cache');

// Clear configuration cache
runCommand('php artisan config:clear', 'Clearing configuration cache');

// Clear route cache
runCommand('php artisan route:clear', 'Clearing route cache');

// Clear view cache
runCommand('php artisan view:clear', 'Clearing view cache');

// Clear event cache
runCommand('php artisan event:clear', 'Clearing event cache');

// Clear compiled services
runCommand('php artisan clear-compiled', 'Clearing compiled services');

// Clear opcache if available
if (function_exists('opcache_reset')) {
    echo "📋 Clearing OPcache...\n";
    if (opcache_reset()) {
        echo "✅ OPcache cleared successfully!\n\n";
    } else {
        echo "⚠️  OPcache reset failed or not enabled.\n\n";
    }
} else {
    echo "ℹ️  OPcache not available.\n\n";
}

// Clear browser cache headers (if using a web server)
echo "📋 Clearing browser cache headers...\n";
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
echo "✅ Browser cache headers set.\n\n";

echo "🎉 All caches cleared successfully!\n";
echo "Your Laravel application is now running with fresh caches.\n";
?>
