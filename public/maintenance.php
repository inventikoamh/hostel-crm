<?php
/**
 * Laravel Maintenance Mode Utility
 *
 * This script helps you manage Laravel's maintenance mode.
 *
 * Usage:
 * - php maintenance.php on (enable maintenance mode)
 * - php maintenance.php off (disable maintenance mode)
 * - php maintenance.php status (check maintenance mode status)
 */

// Set execution time limit
set_time_limit(30);

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔧 Laravel Maintenance Mode Utility\n\n";

// Check if we're in the right directory
if (!file_exists('../artisan')) {
    die("❌ Error: artisan file not found. Please run this script from your Laravel project root directory.\n");
}

// Change to parent directory (Laravel root)
chdir('..');

// Parse command line arguments
$args = $argv ?? [];
$action = $args[1] ?? 'status';

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

// Function to check maintenance mode status
function checkMaintenanceStatus() {
    $maintenanceFile = 'storage/framework/down';
    return file_exists($maintenanceFile);
}

switch (strtolower($action)) {
    case 'on':
    case 'enable':
        echo "🚧 Enabling maintenance mode...\n";

        if (checkMaintenanceStatus()) {
            echo "⚠️  Maintenance mode is already enabled.\n";
        } else {
            runCommand('php artisan down', 'Enabling maintenance mode');
            echo "✅ Maintenance mode has been enabled.\n";
            echo "Your application is now in maintenance mode and will show a maintenance page to visitors.\n";
        }
        break;

    case 'off':
    case 'disable':
        echo "✅ Disabling maintenance mode...\n";

        if (!checkMaintenanceStatus()) {
            echo "ℹ️  Maintenance mode is not currently enabled.\n";
        } else {
            runCommand('php artisan up', 'Disabling maintenance mode');
            echo "✅ Maintenance mode has been disabled.\n";
            echo "Your application is now accessible to visitors.\n";
        }
        break;

    case 'status':
    default:
        echo "📊 Checking maintenance mode status...\n";

        if (checkMaintenanceStatus()) {
            echo "🚧 Maintenance mode is ENABLED\n";
            echo "Your application is currently in maintenance mode.\n";

            // Try to get more details about maintenance mode
            $output = [];
            $return_var = 0;
            exec('php artisan down --show 2>&1', $output, $return_var);

            if (!empty($output)) {
                echo "Details:\n";
                echo implode("\n", $output) . "\n";
            }
        } else {
            echo "✅ Maintenance mode is DISABLED\n";
            echo "Your application is accessible to visitors.\n";
        }
        break;
}

echo "\n📋 Available commands:\n";
echo "- php maintenance.php on (enable maintenance mode)\n";
echo "- php maintenance.php off (disable maintenance mode)\n";
echo "- php maintenance.php status (check current status)\n";

echo "\n💡 Tips:\n";
echo "1. Use maintenance mode when deploying updates or performing maintenance\n";
echo "2. You can customize the maintenance page in resources/views/errors/503.blade.php\n";
echo "3. Maintenance mode bypasses can be configured in app/Http/Middleware/PreventRequestsDuringMaintenance.php\n";
?>
