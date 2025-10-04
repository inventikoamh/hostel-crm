<?php
/**
 * Laravel Migration Runner
 *
 * This script runs database migrations with optional seeders.
 *
 * Usage:
 * - php run-migrations.php (migrations only)
 * - php run-migrations.php --seed (migrations with seeders)
 * - php run-migrations.php --fresh (fresh migrations with seeders)
 * - php run-migrations.php --fresh-seed (fresh migrations with seeders)
 */

// Set execution time limit
set_time_limit(300); // 5 minutes

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🗄️  Laravel Migration Runner\n\n";

// Check if we're in the right directory
if (!file_exists('../artisan')) {
    die("❌ Error: artisan file not found. Please run this script from your Laravel project root directory.\n");
}

// Change to parent directory (Laravel root)
chdir('..');

// Parse command line arguments
$args = $argv ?? [];
$runSeeders = in_array('--seed', $args) || in_array('--fresh-seed', $args);
$freshMigration = in_array('--fresh', $args) || in_array('--fresh-seed', $args);

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

// Check if .env file exists
if (!file_exists('.env')) {
    echo "❌ Error: .env file not found. Please create it from .env.example and configure your database settings.\n";
    exit(1);
}

// Check database connection
echo "🔍 Testing database connection...\n";
$output = [];
$return_var = 0;
exec('php artisan migrate:status 2>&1', $output, $return_var);

if ($return_var !== 0) {
    echo "❌ Database connection failed!\n";
    echo "Output: " . implode("\n", $output) . "\n";
    echo "Please check your database configuration in the .env file.\n";
    exit(1);
} else {
    echo "✅ Database connection successful!\n\n";
}

// Show current migration status
echo "📊 Current migration status:\n";
runCommand('php artisan migrate:status', 'Checking migration status');

// Run migrations
if ($freshMigration) {
    echo "⚠️  WARNING: You are about to run FRESH migrations!\n";
    echo "This will drop all tables and recreate them. All data will be lost!\n";
    echo "Are you sure you want to continue? (yes/no): ";

    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);

    if (trim(strtolower($line)) !== 'yes') {
        echo "❌ Fresh migration cancelled.\n";
        exit(0);
    }

    echo "🔄 Running fresh migrations...\n";
    if ($runSeeders) {
        runCommand('php artisan migrate:fresh --seed --force', 'Running fresh migrations with seeders');
    } else {
        runCommand('php artisan migrate:fresh --force', 'Running fresh migrations');
    }
} else {
    echo "🔄 Running migrations...\n";
    runCommand('php artisan migrate --force', 'Running database migrations');
}

// Run seeders if requested
if ($runSeeders && !$freshMigration) {
    echo "🌱 Running database seeders...\n";
    runCommand('php artisan db:seed --force', 'Running database seeders');
}

// Show final migration status
echo "📊 Final migration status:\n";
runCommand('php artisan migrate:status', 'Checking final migration status');

echo "🎉 Migration process completed successfully!\n";

if ($runSeeders) {
    echo "✅ Database has been migrated and seeded with sample data.\n";
} else {
    echo "✅ Database has been migrated successfully.\n";
}

echo "\n📋 Available migration commands:\n";
echo "- php run-migrations.php (run pending migrations)\n";
echo "- php run-migrations.php --seed (run migrations with seeders)\n";
echo "- php run-migrations.php --fresh (fresh migration - DANGEROUS!)\n";
echo "- php run-migrations.php --fresh-seed (fresh migration with seeders - DANGEROUS!)\n";
?>
